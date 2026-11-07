@extends('layouts.app')

@section('title', 'Panduan Pengisian ID Akun Game - KAIDEN')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 sm:pt-8 pb-16 space-y-8">

    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-xs text-[#212121]/60 dark:text-zinc-400">
        <a href="{{ route('home') }}" class="hover:text-[#6D0808] dark:hover:text-rose-400 transition">Beranda</a>
        <x-icon name="chevron-right" class="w-3 h-3" />
        <span class="text-[#212121] dark:text-zinc-200 font-semibold">Panduan Pengisian ID</span>
    </div>

    <!-- Header Banner Panduan -->
    <div class="relative overflow-hidden rounded-2xl bg-[#212121] text-white p-6 sm:p-8 border border-white/10 shadow-sm">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-[#6D0808]/40 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl space-y-3">
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-[11px] font-bold text-amber-300 uppercase tracking-wider">
                <x-icon name="help-circle" class="w-3.5 h-3.5 text-amber-400" />
                <span>Pusat Bantuan Top Up</span>
            </div>
            <h1 class="text-xl sm:text-3xl font-black tracking-tight text-white leading-tight">
                Panduan Pengisian ID Game
            </h1>
            <p class="text-xs sm:text-sm text-white/75 leading-relaxed">
                Panduan cara menemukan User ID, Server / Zone ID, atau Tagline akun game Anda agar proses verifikasi dan top up berjalan instan &lt; 60 detik.
            </p>
        </div>
    </div>

    <!-- Grid Panduan Per Game -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($games as $g)
            @php
                $thumb = $g->thumbnail ?? match($g->slug) {
                    'mobile-legends' => 'images/bg/aspirant.jpg',
                    'free-fire' => 'images/bg/freefire.jpg',
                    'valorant' => 'images/bg/valorant.jpg',
                    'call-of-duty-mobile' => 'images/bg/codm.jpg',
                    'arena-of-valor' => 'images/bg/aov.jpg',
                    default => 'images/bg/1.jpg',
                };
            @endphp
            <div id="{{ $g->slug }}" class="p-5 rounded-2xl bg-white dark:bg-[#18181B] border border-[#212121]/10 dark:border-white/10 shadow-xs flex flex-col justify-between space-y-4 hover:border-[#6D0808]/40 dark:hover:border-rose-500/40 transition duration-200">
                <div class="space-y-3.5">
                    <!-- Header Game -->
                    <div class="flex items-center space-x-3 pb-3 border-b border-[#212121]/5 dark:border-white/10">
                        <div class="w-12 h-14 rounded-xl overflow-hidden bg-stone-100 dark:bg-zinc-800 shrink-0 border border-[#212121]/10 dark:border-white/10">
                            <img src="{{ asset($thumb) }}" alt="{{ $g->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-[#6D0808] dark:text-rose-400">
                                {{ $g->category }}
                            </div>
                            <h2 class="text-sm font-bold text-[#212121] dark:text-zinc-100 truncate mt-0.5">{{ $g->name }}</h2>
                            <p class="text-[11px] text-[#212121]/60 dark:text-zinc-400">{{ $g->publisher ?? 'Official' }}</p>
                        </div>
                    </div>

                    <!-- Petunjuk ID -->
                    <div class="space-y-2 text-xs">
                        <div class="font-bold text-[#212121] dark:text-zinc-200 flex items-center space-x-1.5">
                            <x-icon name="alert-circle" class="w-3.5 h-3.5 text-[#6D0808] dark:text-rose-400 shrink-0" />
                            <span>Cara Menemukan ID:</span>
                        </div>
                        <p class="text-[#212121]/75 dark:text-zinc-400 leading-relaxed bg-[#F9F8F6] dark:bg-zinc-800/60 p-3 rounded-xl border border-[#212121]/5 dark:border-white/10">
                            {{ $g->guide_text ?? 'Masukkan User ID yang tertera pada menu profil game Anda.' }}
                        </p>
                    </div>

                    @if($g->needs_zone)
                        <div class="p-2.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-[11px] text-amber-900 dark:text-amber-300 space-y-0.5">
                            <span class="font-bold">Membutuhkan Zone ID:</span>
                            <p class="text-amber-800/90 dark:text-amber-300/80">Masukkan angka di dalam tanda kurung pada profil akun Anda.</p>
                        </div>
                    @endif
                </div>

                <!-- Tombol Aksi Top Up -->
                <div class="pt-3 border-t border-[#212121]/5 dark:border-white/10 flex items-center justify-between">
                    <span class="text-[11px] text-emerald-700 dark:text-emerald-400 font-semibold flex items-center space-x-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Auto Check Aktif</span>
                    </span>
                    <a href="{{ route('order.game', $g->slug) }}" class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs">
                        <span>Top Up</span>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Catatan Keamanan & Bantuan CS -->
    <div class="p-5 sm:p-6 rounded-2xl bg-white dark:bg-[#18181B] border border-[#212121]/10 dark:border-white/10 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <x-icon name="headphones" class="w-5 h-5" />
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-[#212121] dark:text-zinc-100">Masih Bingung Menemukan ID Akun Game?</h3>
                <p class="text-[11px] text-[#212121]/60 dark:text-zinc-400 mt-0.5">Tim Customer Care siap membantu memandu Anda menemukan ID yang tepat via WhatsApp 24/7.</p>
            </div>
        </div>
        <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number', env('WHATSAPP_NUMBER', '6281234567890')) }}?text={{ urlencode('Halo Admin KAIDEN, saya butuh panduan untuk menemukan ID akun game saya.') }}" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="inline-flex items-center justify-center space-x-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-emerald-700 hover:bg-emerald-800 transition shadow-xs shrink-0">
            <x-icon name="headphones" class="w-4 h-4" />
            <span>Chat CS WhatsApp</span>
        </a>
    </div>

</div>
@endsection

