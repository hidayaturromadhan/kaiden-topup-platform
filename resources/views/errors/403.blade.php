@extends('errors.layout')

@section('title', '403 - Akses Ditolak | KAIDEN')

@push('styles')
<style>
    /* Animasi Hardware-Accelerated (Zero Framedrop - 60+ FPS murni GPU) */
    @keyframes smoothFloat {
        0%, 100% {
            transform: translate3d(0, 0, 0);
        }
        50% {
            transform: translate3d(0, -6px, 0);
        }
    }

    @keyframes smoothHalo {
        0%, 100% {
            transform: scale(1);
            opacity: 0.2;
        }
        50% {
            transform: scale(1.12);
            opacity: 0.45;
        }
    }

    .anim-smooth-float {
        animation: smoothFloat 3.5s ease-in-out infinite;
        will-change: transform;
    }

    .anim-smooth-halo {
        animation: smoothHalo 3s ease-in-out infinite;
        will-change: transform, opacity;
        transform-origin: center;
    }
</style>
@endpush

@section('content')
<div class="space-y-4 sm:space-y-4.5">

    <!-- 1. Compact, Lightweight Animated Lock Emblem (Zero Framedrop) -->
    <div class="relative w-24 h-24 sm:w-28 sm:h-28 mx-auto flex items-center justify-center select-none">
        
        <!-- Ambient Pulse Halo Ring (Hardware Accelerated Transform Only) -->
        <div class="absolute inset-0 rounded-full bg-red-500/20 dark:bg-rose-600/25 anim-smooth-halo pointer-events-none"></div>
        <div class="absolute inset-2 rounded-full border border-red-500/30 dark:border-rose-500/30 pointer-events-none"></div>

        <!-- Floating Central Shield & Padlock -->
        <div class="relative z-10 anim-smooth-float">
            <div class="w-16 h-16 sm:w-18 sm:h-18 rounded-2xl bg-gradient-to-br from-[#991B1B] via-[#6D0808] to-[#450A0A] p-0.5 shadow-lg shadow-red-950/25 border border-red-400/40 flex items-center justify-center">
                <div class="w-full h-full rounded-[14px] bg-[#1A0808] dark:bg-[#140505] flex items-center justify-center relative overflow-hidden">
                    <!-- Subtle Internal Pattern -->
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(239,68,68,0.2)_0,transparent_70%)] pointer-events-none"></div>

                    <!-- Clean Padlock SVG -->
                    <svg class="w-7 h-7 sm:w-8 sm:h-8 text-rose-400 relative z-10 transition-transform duration-300 hover:scale-105" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" fill="rgba(239, 68, 68, 0.15)" stroke="#F87171" stroke-width="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" stroke="#FCA5A5" stroke-width="2.2" />
                        <circle cx="12" cy="16" r="1.5" fill="#EF4444" />
                        <path d="M12 17.5V19" stroke="#EF4444" stroke-width="2" />
                    </svg>
                </div>
            </div>
        </div>

    </div>

    <!-- 2. Status Badge & Title (Non-Technical & Human-Friendly) -->
    <div class="space-y-1.5">
        <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-[#6D0808] dark:text-rose-400 font-medium text-xs">
            <x-icon name="shield" class="w-3.5 h-3.5 text-[#6D0808] dark:text-rose-400" />
            <span>HTTP 403 • Izin Belum Tersedia</span>
        </div>

        <h1 class="text-xl sm:text-2xl font-black text-[#212121] dark:text-zinc-100 tracking-tight">
            Akses Ditolak
        </h1>

        <p class="text-xs sm:text-sm text-[#212121]/70 dark:text-zinc-400 leading-relaxed max-w-md mx-auto">
            {{ $exception->getMessage() ?: 'Halaman atau fitur ini memerlukan hak akses khusus. Akun Anda saat ini belum memiliki izin untuk membukanya.' }}
        </p>
    </div>

    <!-- 3. Human-Friendly Guidance & Account Context Card -->
    <div class="p-3.5 sm:p-4 rounded-xl bg-stone-50 dark:bg-zinc-900/60 border border-[#212121]/10 dark:border-white/10 text-left text-xs space-y-2">
        <div class="flex items-center space-x-2 font-semibold text-[#212121] dark:text-zinc-200">
            <x-icon name="info" class="w-4 h-4 text-[#6D0808] dark:text-rose-400 shrink-0" />
            <span>Informasi Akses</span>
        </div>

        <p class="text-[12px] text-[#212121]/70 dark:text-zinc-400 leading-relaxed">
            @auth
                @if(auth()->user()->isSuperadmin())
                    Anda masuk sebagai <strong>Super Administrator</strong>. Izin ke menu ini sedang dimatikan pada profil peran Anda. Anda dapat mengaktifkannya kembali melalui tombol Tata Kelola Peran di bawah.
                @elseif(auth()->user()->isAdmin())
                    Fitur ini belum diaktifkan untuk akun Administrator Anda. Jika Anda memerlukan akses ini untuk operasional, silakan hubungi <strong>Super Administrator</strong>.
                @else
                    Halaman ini dikhususkan untuk staf pengelola sistem KAIDEN. Jika Anda ingin berbelanja atau mengecek pesanan, silakan gunakan menu belanja atau riwayat pesanan.
                @endif
            @else
                Silakan masuk dengan akun yang memiliki hak akses, atau kembali ke beranda untuk melanjutkan berbelanja.
            @endauth
        </p>

        @auth
            <div class="pt-2 border-t border-[#212121]/10 dark:border-white/10 flex flex-wrap items-center justify-between gap-1.5 text-[11px] text-[#212121]/70 dark:text-zinc-400">
                <div class="flex items-center space-x-1.5">
                    <span class="text-[#212121]/45 dark:text-zinc-500">Masuk sebagai:</span>
                    <strong class="font-medium text-[#212121] dark:text-zinc-200">{{ auth()->user()->name }}</strong>
                    <span class="text-[#212121]/40 dark:text-zinc-500 hidden sm:inline">({{ auth()->user()->email }})</span>
                </div>
                <div class="flex items-center space-x-1">
                    <span class="text-[#212121]/45 dark:text-zinc-500">Peran:</span>
                    <span class="px-2 py-0.5 rounded-full font-bold text-[10px] {{ auth()->user()->isSuperadmin() ? 'bg-red-100 dark:bg-red-950/80 text-red-800 dark:text-red-300' : (auth()->user()->isAdmin() ? 'bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300' : 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300') }}">
                        {{ auth()->user()->roles->first()->display_name ?? 'Pelanggan' }}
                    </span>
                </div>
            </div>
        @endauth
    </div>

    <!-- 4. Contextual Smart Action Buttons (Responsive & Fit-to-Viewport) -->
    <div class="pt-1 flex flex-col sm:flex-row items-center justify-center gap-2.5">
        <!-- Tombol Utama: Kembali ke Beranda -->
        <a href="{{ route('home') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-md hover:shadow-lg transform active:scale-95 cursor-pointer">
            <x-icon name="arrow-left" class="w-4 h-4" />
            <span>Kembali ke Beranda</span>
        </a>

        @auth
            @if(auth()->user()->isSuperadmin())
                <!-- Shortcut Cerdas untuk Superadmin: Langsung buka Tata Kelola RBAC untuk aktifkan izin -->
                <a href="{{ route('superadmin.roles.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-bold text-[#6D0808] dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-900 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition shadow-xs">
                    <x-icon name="settings" class="w-4 h-4" />
                    <span>Buka Tata Kelola Peran (RBAC)</span>
                </a>
            @elseif(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-[#212121] dark:text-zinc-200 bg-white dark:bg-zinc-800 border border-[#212121]/15 dark:border-white/15 hover:bg-stone-50 dark:hover:bg-zinc-700 transition">
                    <x-icon name="activity" class="w-4 h-4" />
                    <span>Dashboard Admin</span>
                </a>
            @else
                <a href="{{ route('orders.my') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-[#212121] dark:text-zinc-200 bg-white dark:bg-zinc-800 border border-[#212121]/15 dark:border-white/15 hover:bg-stone-50 dark:hover:bg-zinc-700 transition">
                    <x-icon name="shopping-bag" class="w-4 h-4" />
                    <span>Riwayat Pesanan Saya</span>
                </a>
            @endif
        @else
            <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-[#212121] dark:text-zinc-200 bg-white dark:bg-zinc-800 border border-[#212121]/15 dark:border-white/15 hover:bg-stone-50 dark:hover:bg-zinc-700 transition">
                <x-icon name="user" class="w-4 h-4" />
                <span>Masuk ke Akun</span>
            </a>
        @endauth
    </div>

</div>
@endsection
