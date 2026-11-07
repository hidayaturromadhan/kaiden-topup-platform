<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $roleFilter = $request->query('role');
        $search = $request->query('q');

        $query = User::with('roles');

        if ($roleFilter) {
            $query->whereHas('roles', function ($q) use ($roleFilter) {
                $q->where('name', $roleFilter);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('superadmin.users.index', compact('users', 'roles', 'roleFilter', 'search'));
    }

    /**
     * Membuat akun staf internal (Admin / Superadmin)
     * RULES.md Section 4.B.4: Akun internal HANYA bisa dibuat lewat dashboard oleh Superadmin!
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::min(8)],
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        $role = Role::findOrFail($validated['role_id']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
        ]);

        $user->roles()->sync([$role->id]);

        // RULES.md Section 4.C.2: Wajib mencatat audit log manajemen user
        AuditLog::record(
            action: 'pengguna.buat',
            targetType: 'User',
            targetId: $user->id,
            oldValues: null,
            newValues: [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role->name,
                'status' => $user->status,
            ],
            actorId: Auth::id()
        );

        return back()->with('success', "Pengguna internal '{$user->name}' dengan role {$role->display_name} berhasil dibuat.");
    }

    /**
     * Memperbarui data pengguna atau role
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'password' => ['nullable', Password::min(8)],
        ]);

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name')->toArray(),
            'status' => $user->status,
        ];

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->status = $validated['status'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $role = Role::findOrFail($validated['role_id']);
        $user->roles()->sync([$role->id]);

        // RULES.md Section 4.C.2: Wajib mencatat audit log perubahan user
        AuditLog::record(
            action: 'pengguna.ubah',
            targetType: 'User',
            targetId: $user->id,
            oldValues: $oldValues,
            newValues: [
                'name' => $user->name,
                'email' => $user->email,
                'roles' => [$role->name],
                'status' => $user->status,
                'password_changed' => !empty($validated['password']),
            ],
            actorId: Auth::id()
        );

        return back()->with('success', "Data pengguna '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Toggle status aktif/nonaktif pengguna
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.']);
        }

        $oldStatus = $user->status;
        $user->status = ($user->status === 'aktif') ? 'nonaktif' : 'aktif';
        $user->save();

        AuditLog::record(
            action: 'pengguna.status_ubah',
            targetType: 'User',
            targetId: $user->id,
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => $user->status],
            actorId: Auth::id()
        );

        return back()->with('success', "Status akun '{$user->name}' berhasil diubah menjadi {$user->status}.");
    }
}

