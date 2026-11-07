<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole(['admin', 'superadmin'])) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = 'login:' . $request->ip() . '|' . strtolower($request->input('email'));

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan masuk. Silakan coba kembali dalam {$seconds} detik.",
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            RateLimiter::clear($throttleKey);

            $user = Auth::user();

            // Cek apakah akun aktif
            if ($user->status !== 'aktif') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi Administrator.',
                ])->onlyInput('email');
            }

            // RULES.md Section 4.C.2: Wajib mencatat audit log saat Login Admin/Superadmin
            if ($user->hasRole(['admin', 'superadmin'])) {
                AuditLog::record(
                    action: 'auth.login_internal',
                    targetType: 'User',
                    targetId: $user->id,
                    oldValues: null,
                    newValues: [
                        'email' => $user->email,
                        'role' => $user->roles->pluck('name')->toArray(),
                        'login_at' => now()->toIso8601String(),
                    ],
                    actorId: $user->id
                );

                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('home'));
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'Kombinasi email dan kata sandi tidak cocok dengan catatan kami.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // RULES.md: Cegah mass assignment pada role! Registrasi publik HANYA bisa membuat role pelanggan!
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 'aktif',
        ]);

        // Berikan role 'pelanggan' secara eksplisit
        $rolePelanggan = Role::where('name', 'pelanggan')->first();
        if ($rolePelanggan) {
            $user->roles()->sync([$rolePelanggan->id]);
        }

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Selamat datang di KAIDEN! Akun Anda berhasil didaftarkan.');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user && $user->hasRole(['admin', 'superadmin'])) {
            AuditLog::record(
                action: 'auth.logout_internal',
                targetType: 'User',
                targetId: $user->id,
                oldValues: null,
                newValues: ['email' => $user->email],
                actorId: $user->id
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Anda telah keluar dari akun.');
    }
}

