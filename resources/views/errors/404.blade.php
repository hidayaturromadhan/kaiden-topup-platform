@extends('errors.layout')

@section('title', '404 - Halaman Tidak Ditemukan | KAIDEN')

@push('styles')
<style>
    /* Animasi Hardware-Accelerated (Zero Framedrop) */
    @keyframes smoothFloat404 {
        0%, 100% {
            transform: translate3d(0, 0, 0);
        }
        50% {
            transform: translate3d(0, -6px, 0);
        }
    }

    @keyframes smoothHalo404 {
        0%, 100% {
            transform: scale(1);
            opacity: 0.15;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.35;
        }
    }

    .anim-smooth-float-404 {
        animation: smoothFloat404 3.5s ease-in-out infinite;
        will-change: transform;
    }

    .anim-smooth-halo-404 {
        animation: smoothHalo404 3s ease-in-out infinite;
        will-change: transform, opacity;
        transform-origin: center;
    }
</style>
@endpush

@section('content')
<div class="space-y-4 sm:space-y-4.5">

    <!-- 1. Compact, Lightweight Animated Search Emblem (Zero Framedrop) -->
    <div class="relative w-24 h-24 sm:w-28 sm:h-28 mx-auto flex items-center justify-center select-none">
        
        <!-- Ambient Pulse Halo Ring -->
        <div class="absolute inset-0 rounded-full bg-zinc-400/20 dark:bg-zinc-600/25 anim-smooth-halo-404 pointer-events-none"></div>
        <div class="absolute inset-2 rounded-full border border-zinc-300 dark:border-zinc-700 pointer-events-none"></div>

        <!-- Floating Central Compass / Search Icon -->
        <div class="relative z-10 anim-smooth-float-404">
            <div class="w-16 h-16 sm:w-18 sm:h-18 rounded-2xl bg-gradient-to-br from-[#27272A] via-[#18181B] to-[#09090B] p-0.5 shadow-lg shadow-zinc-950/30 border border-zinc-700/50 flex items-center justify-center">
                <div class="w-full h-full rounded-[14px] bg-[#141416] flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.08)_0,transparent_70%)] pointer-events-none"></div>

                    <!-- Clean Search SVG -->
                    <svg class="w-7 h-7 sm:w-8 sm:h-8 text-rose-400 dark:text-rose-500 relative z-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        <line x1="11" y1="8" x2="11" y2="14" stroke-dasharray="2 2" />
                    </svg>
                </div>
            </div>
        </div>

    </div>

    <!-- 2. Status Badge & Title -->
    <div class="space-y-1.5">
        <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-stone-100 dark:bg-zinc-800 border border-stone-200 dark:border-zinc-700 text-[#212121]/70 dark:text-zinc-300 font-medium text-xs">
            <x-icon name="search" class="w-3.5 h-3.5 text-[#212121]/60 dark:text-zinc-400" />
            <span>HTTP 404 • Halaman Hilang</span>
        </div>

        <h1 class="text-xl sm:text-2xl font-black text-[#212121] dark:text-zinc-100 tracking-tight">
            Halaman Tidak Ditemukan
        </h1>

        <p class="text-xs sm:text-sm text-[#212121]/70 dark:text-zinc-400 leading-relaxed max-w-md mx-auto">
            Halaman atau tautan yang Anda tuju mungkin telah dipindahkan atau alamat URL yang dimasukkan kurang tepat.
        </p>
    </div>

    <!-- 3. Guidance Card -->
    <div class="p-3.5 sm:p-4 rounded-xl bg-stone-50 dark:bg-zinc-900/60 border border-[#212121]/10 dark:border-white/10 text-left text-xs space-y-1">
        <div class="flex items-center space-x-2 font-semibold text-[#212121] dark:text-zinc-200">
            <x-icon name="info" class="w-4 h-4 text-[#6D0808] dark:text-rose-400 shrink-0" />
            <span>Saran Navigasi</span>
        </div>
        <p class="text-[12px] text-[#212121]/70 dark:text-zinc-400 leading-relaxed">
            Periksa kembali penulisan URL di peramban Anda, atau silakan kembali ke halaman utama untuk menelusuri katalog game dan promo terbaru.
        </p>
    </div>

    <!-- 4. Action Buttons (Responsive & Fit-to-Viewport) -->
    <div class="pt-1 flex flex-col sm:flex-row items-center justify-center gap-2.5">
        <a href="{{ route('home') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-md hover:shadow-lg transform active:scale-95 cursor-pointer">
            <x-icon name="arrow-left" class="w-4 h-4" />
            <span>Kembali ke Beranda</span>
        </a>

        <a href="{{ route('catalog.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-[#212121] dark:text-zinc-200 bg-white dark:bg-zinc-800 border border-[#212121]/15 dark:border-white/15 hover:bg-stone-50 dark:hover:bg-zinc-700 transition">
            <x-icon name="gamepad" class="w-4 h-4" />
            <span>Katalog Game</span>
        </a>
    </div>

</div>
@endsection
