<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KAIDEN - Platform Top Up Game Kilat, Termurah & 100% Legal')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo2.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @if(request()->routeIs('home'))
        <!-- Preload Critical LCP Assets for Instant Render -->
        <link rel="preload" as="image" href="{{ asset('images/bg/dark.jpg') }}">
    @endif

    <!-- Anti-Flicker & Persistent Theme Initializer (Zero FOUC, < 1ms execution) -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('kaiden_theme');
                const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-[#F9F8F6] dark:bg-[#0E0E10] text-[#212121] dark:text-[#F4F4F5] flex flex-col font-sans antialiased selection:bg-[#6D0808] selection:text-white transition-colors duration-200">

    <!-- Top Navigation Bar (Redesigned: Clean, Consistent, Secure & Human-crafted) -->
    <nav class="sticky top-0 z-40 bg-white/95 dark:bg-[#121214]/95 backdrop-blur-md border-b border-stone-200/80 dark:border-white/10 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Sisi Kiri: Brand Logo & Nav Links -->
                <div class="flex items-center space-x-6 lg:space-x-8">
                    <!-- Brand Logo Dinamis (Light: logo2.png, Dark: logo.png) -->
                    <a href="{{ route('home') }}" class="flex items-center group shrink-0">
                        <img src="{{ asset('images/logo2.png') }}" alt="KAIDEN" width="140" height="36" class="h-9 w-auto object-contain transition group-hover:opacity-90 dark:hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="KAIDEN" width="140" height="36" class="h-9 w-auto object-contain transition group-hover:opacity-90 hidden dark:block">
                    </a>

                    <!-- Navigasi Utama: Bersih & Seragam (Tanpa Ikon Ganjil) -->
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="{{ route('catalog.index') }}" 
                           class="px-3.5 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('catalog.*') ? 'text-[#6D0808] dark:text-rose-400 font-bold bg-[#6D0808]/5 dark:bg-rose-950/30' : 'text-stone-600 hover:text-stone-900 dark:text-zinc-400 dark:hover:text-white hover:bg-stone-100 dark:hover:bg-zinc-800/60' }}">
                            Katalog Produk
                        </a>
                        <a href="{{ route('home') }}#katalog-game" 
                           class="px-3.5 py-2 rounded-lg text-xs font-semibold text-stone-600 hover:text-stone-900 dark:text-zinc-400 dark:hover:text-white hover:bg-stone-100 dark:hover:bg-zinc-800/60 transition">
                            Promo Spesial
                        </a>
                        <a href="{{ route('guide.index') }}" 
                           class="px-3.5 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('guide.*') ? 'text-[#6D0808] dark:text-rose-400 font-bold bg-[#6D0808]/5 dark:bg-rose-950/30' : 'text-stone-600 hover:text-stone-900 dark:text-zinc-400 dark:hover:text-white hover:bg-stone-100 dark:hover:bg-zinc-800/60' }}">
                            Panduan ID
                        </a>
                    </div>
                </div>

                <!-- Sisi Kanan: Bantuan, Tema & Profil / Tombol Masuk -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <!-- Realtime Server Time (WIB) -->
                    <div class="hidden lg:inline-flex items-center space-x-2 px-2.5 py-1 rounded-full bg-stone-100/90 dark:bg-zinc-800/90 border border-stone-200/80 dark:border-white/10 text-[11px] font-mono text-stone-600 dark:text-zinc-300 select-none" 
                         title="Waktu Indonesia Barat (WIB) Realtime">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="kaiden-wib-clock font-medium">--:--:-- WIB</span>
                    </div>

                    <!-- Bantuan CS (Minimalis & Elegan) -->
                    <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number', env('WHATSAPP_NUMBER', '6281234567890')) }}?text={{ urlencode('Halo Admin KAIDEN, saya butuh bantuan layanan top up.') }}" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="hidden sm:inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-stone-600 hover:text-stone-900 dark:text-zinc-400 dark:hover:text-white hover:bg-stone-100 dark:hover:bg-zinc-800/60 transition"
                       title="Hubungi Dukungan Pelanggan">
                        <span>Bantuan</span>
                    </a>

                    <!-- Toggle Tema Gelap / Terang -->
                    <button type="button" 
                            onclick="toggleKaidenTheme()" 
                            aria-label="Alihkan tema gelap/terang"
                            class="p-2 rounded-xl text-stone-600 hover:text-stone-900 dark:text-zinc-400 dark:hover:text-white hover:bg-stone-100 dark:hover:bg-zinc-800/60 transition flex items-center justify-center cursor-pointer"
                            title="Alihkan Mode Gelap / Terang">
                        <x-icon name="moon" class="w-4 h-4 theme-icon-moon" />
                        <x-icon name="sun" class="w-4 h-4 theme-icon-sun hidden text-amber-400" />
                    </button>

                    @auth
                        <!-- Secure User Profile Dropdown Pill -->
                        <div class="relative" id="user-menu-container">
                            <button type="button" 
                                    id="user-menu-button" 
                                    aria-haspopup="true" 
                                    aria-expanded="false"
                                    class="flex items-center space-x-2 pl-1.5 pr-2.5 py-1 rounded-full bg-stone-100/90 hover:bg-stone-200/80 dark:bg-zinc-800/90 dark:hover:bg-zinc-700/80 border border-stone-200/80 dark:border-white/10 transition cursor-pointer">
                                <!-- User Avatar Initial -->
                                <div class="w-7 h-7 rounded-full bg-[#6D0808] text-white flex items-center justify-center font-bold text-[11px] shrink-0 shadow-2xs">
                                    {{ strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}
                                </div>

                                <!-- User Name Display -->
                                <span class="hidden sm:inline-block max-w-[120px] truncate text-xs font-semibold text-stone-800 dark:text-zinc-200 text-left">
                                    {{ auth()->user()->name }}
                                </span>

                                <x-icon name="chevron-down" class="w-3.5 h-3.5 text-stone-500 dark:text-zinc-400" />
                            </button>

                            <!-- Secure Dropdown Menu Panel -->
                            <div id="user-menu-dropdown" 
                                 class="hidden absolute right-0 mt-2 w-56 rounded-2xl bg-white dark:bg-[#18181B] border border-stone-200 dark:border-white/10 shadow-xl py-2 z-50 animate-fade-in divide-y divide-stone-100 dark:divide-white/10"
                                 role="menu">
                                <!-- User Identity Header & Role Badge -->
                                <div class="px-4 py-2.5 space-y-1">
                                    <p class="text-xs font-bold text-stone-900 dark:text-zinc-100 truncate">
                                        {{ auth()->user()->name }}
                                    </p>
                                    <p class="text-[11px] text-stone-500 dark:text-zinc-400 truncate font-mono">
                                        {{ auth()->user()->email }}
                                    </p>
                                    <div class="pt-1">
                                        <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded {{ auth()->user()->hasRole(['admin', 'superadmin']) ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300' : 'bg-stone-100 dark:bg-zinc-800 text-stone-600 dark:text-zinc-400' }}">
                                            {{ auth()->user()->roles->first()->display_name ?? 'Pelanggan' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Navigation Options -->
                                <div class="py-1">
                                    @if(auth()->user()->hasRole(['admin', 'superadmin']))
                                        <a href="{{ route('admin.dashboard') }}" 
                                           class="flex items-center justify-between px-4 py-2 text-xs font-semibold text-[#6D0808] dark:text-rose-400 hover:bg-[#6D0808]/5 dark:hover:bg-rose-950/30 transition"
                                           role="menuitem">
                                            <span>Panel Admin Operasional</span>
                                            <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                                        </a>
                                    @endif

                                    @if(auth()->user()->hasPermission('transaksi.lihat_milik_sendiri'))
                                        <a href="{{ route('orders.my') }}" 
                                           class="flex items-center justify-between px-4 py-2 text-xs font-medium text-stone-700 dark:text-zinc-300 hover:bg-stone-50 dark:hover:bg-zinc-800 transition"
                                           role="menuitem">
                                            <span>Riwayat Pesanan Saya</span>
                                            <x-icon name="chevron-right" class="w-3.5 h-3.5 text-stone-400" />
                                        </a>
                                    @endif
                                </div>

                                <!-- Secure CSRF Protected Logout Action -->
                                <div class="py-1">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full text-left flex items-center justify-between px-4 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition cursor-pointer"
                                                role="menuitem">
                                            <span>Keluar dari Akun</span>
                                            <x-icon name="log-out" class="w-3.5 h-3.5" />
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Guest Auth Buttons: Clean & Direct -->
                        <div class="hidden sm:flex items-center space-x-2">
                            <a href="{{ route('login') }}" 
                               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold text-stone-700 hover:text-stone-900 dark:text-zinc-300 dark:hover:text-white hover:bg-stone-100 dark:hover:bg-zinc-800 transition">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" 
                               class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs focus:ring-2 focus:ring-[#6D0808]/30">
                                Daftar Akun
                            </a>
                        </div>
                    @endauth

                    <!-- Mobile Hamburger Button -->
                    <button type="button" 
                            onclick="toggleMobileNav()" 
                            class="md:hidden p-2 rounded-xl text-stone-600 hover:text-stone-900 dark:text-zinc-400 dark:hover:text-white hover:bg-stone-100 dark:hover:bg-zinc-800 transition" 
                            title="Menu Navigasi Mobile">
                        <x-icon name="menu" class="w-5 h-5" id="nav-icon-menu" />
                        <x-icon name="x" class="w-5 h-5 hidden" id="nav-icon-close" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Panel (Clean Drawer) -->
        <div id="mobile-nav-panel" class="hidden md:hidden border-t border-stone-200/80 dark:border-white/10 bg-white dark:bg-[#18181B] px-4 py-4 space-y-3 shadow-lg animate-fade-in transition-colors">
            <div class="space-y-1">
                <a href="{{ route('catalog.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('catalog.*') ? 'bg-[#6D0808]/10 text-[#6D0808] dark:bg-rose-950/40 dark:text-rose-400 font-bold' : 'text-stone-700 dark:text-zinc-300 hover:bg-stone-100 dark:hover:bg-zinc-800' }}">
                    <span>Katalog Produk</span>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5 text-stone-400" />
                </a>
                <a href="{{ route('home') }}#katalog-game" onclick="toggleMobileNav()" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-stone-700 dark:text-zinc-300 hover:bg-stone-100 dark:hover:bg-zinc-800 transition">
                    <span>Promo Spesial</span>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5 text-stone-400" />
                </a>
                <a href="{{ route('guide.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('guide.*') ? 'bg-[#6D0808]/10 text-[#6D0808] dark:bg-rose-950/40 dark:text-rose-400 font-bold' : 'text-stone-700 dark:text-zinc-300 hover:bg-stone-100 dark:hover:bg-zinc-800' }}">
                    <span>Panduan ID Game</span>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5 text-stone-400" />
                </a>
                @auth
                    @if(auth()->user()->hasPermission('transaksi.lihat_milik_sendiri'))
                        <a href="{{ route('orders.my') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('orders.my') ? 'bg-[#6D0808]/10 text-[#6D0808] dark:bg-rose-950/40 dark:text-rose-400 font-bold' : 'text-stone-700 dark:text-zinc-300 hover:bg-stone-100 dark:hover:bg-zinc-800' }}">
                            <span>Riwayat Pesanan Saya</span>
                            <x-icon name="chevron-right" class="w-3.5 h-3.5 text-stone-400" />
                        </a>
                    @endif
                    @if(auth()->user()->hasRole(['admin', 'superadmin']))
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-[#6D0808] dark:text-rose-400 bg-[#6D0808]/5 dark:bg-rose-950/30 hover:bg-[#6D0808]/10 transition">
                            <span>Panel Admin Operasional</span>
                            <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        </a>
                    @endif
                @endauth
                <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number', env('WHATSAPP_NUMBER', '6281234567890')) }}?text={{ urlencode('Halo Admin KAIDEN, saya butuh bantuan layanan top up.') }}" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50/80 dark:bg-emerald-950/30 hover:bg-emerald-100/70 border border-emerald-200/60 dark:border-emerald-800/40 transition">
                    <span>Bantuan CS WhatsApp</span>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                </a>

                <!-- Mobile Realtime Server Time (WIB) -->
                <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-mono text-stone-600 dark:text-zinc-400 bg-stone-50 dark:bg-zinc-800/60 border border-stone-200/80 dark:border-white/10">
                    <span class="flex items-center space-x-2 font-sans font-medium">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>Waktu Server (WIB)</span>
                    </span>
                    <span class="kaiden-wib-clock text-[11px] font-semibold text-stone-900 dark:text-zinc-100">
                        --:--:-- WIB
                    </span>
                </div>

                <!-- Mobile Theme Toggle -->
                <button type="button" 
                        onclick="toggleKaidenTheme()" 
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-stone-700 dark:text-zinc-300 hover:bg-stone-100 dark:hover:bg-zinc-800 transition cursor-pointer">
                    <span class="flex items-center space-x-2">
                        <x-icon name="moon" class="w-4 h-4 theme-icon-moon" />
                        <x-icon name="sun" class="w-4 h-4 theme-icon-sun hidden text-amber-400" />
                        <span>Mode Tampilan</span>
                    </span>
                    <span class="text-[10px] uppercase font-mono px-2 py-0.5 rounded bg-stone-100 dark:bg-zinc-800 text-stone-600 dark:text-zinc-300 theme-label-current">
                        Terang
                    </span>
                </button>
            </div>

            <!-- Mobile Auth Section -->
            <div class="pt-3 border-t border-stone-200/80 dark:border-white/10">
                @auth
                    <div class="p-3 rounded-2xl bg-stone-50 dark:bg-zinc-800/60 border border-stone-200/80 dark:border-white/10 space-y-2.5">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-full bg-[#6D0808] text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                                {{ strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-xs font-bold text-stone-900 dark:text-zinc-100 truncate">{{ auth()->user()->name }}</div>
                                <div class="text-[11px] text-stone-500 dark:text-zinc-400 capitalize">{{ auth()->user()->roles->first()->display_name ?? 'Pelanggan' }}</div>
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="pt-1">
                            @csrf
                            <button type="submit" class="w-full py-2 px-3 rounded-xl text-xs font-semibold text-center text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/40 border border-rose-200/50 dark:border-rose-900/40 transition flex items-center justify-center space-x-1.5 cursor-pointer">
                                <x-icon name="log-out" class="w-3.5 h-3.5" />
                                <span>Keluar dari Akun</span>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('login') }}" class="py-2.5 px-3 rounded-xl text-xs font-semibold text-center text-stone-700 dark:text-zinc-200 bg-stone-100 dark:bg-zinc-800 border border-stone-200 dark:border-white/10 hover:bg-stone-200 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="py-2.5 px-3 rounded-xl text-xs font-bold text-center text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs">
                            Daftar Akun
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Flash Messages & Notifikasi Realtime (Kombinasi Notiflix + Notyf) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
        @if(session('success'))
            <div class="mb-4 p-3.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <x-icon name="check-circle" class="w-4 h-4 text-emerald-600 shrink-0" />
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error') || $errors->has('error'))
            <div class="mb-4 p-3.5 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <x-icon name="alert-circle" class="w-4 h-4 text-red-600 shrink-0" />
                    <span>{{ session('error') ?? $errors->first('error') }}</span>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-4 p-3.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <x-icon name="alert-triangle" class="w-4 h-4 text-amber-600 shrink-0" />
                    <span>{{ session('info') }}</span>
                </div>
            </div>
        @endif
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            if (window.kaidenToast) {
                window.kaidenToast.success(@json(session('success')));
            }
        @endif

        @if(session('error') || $errors->has('error'))
            if (window.kaidenAlert) {
                window.kaidenAlert.error('Pemberitahuan', @json(session('error') ?? $errors->first('error')));
            } else if (window.kaidenToast) {
                window.kaidenToast.error(@json(session('error') ?? $errors->first('error')));
            }
        @endif

        @if(session('info'))
            if (window.kaidenToast) {
                window.kaidenToast.info(@json(session('info')));
            }
        @endif

        @if(session('warning'))
            if (window.kaidenToast) {
                window.kaidenToast.warning(@json(session('warning')));
            }
        @endif

        @if($errors->any() && !session('error') && !$errors->has('error'))
            if (window.kaidenAlert) {
                window.kaidenAlert.error('Validasi Gagal', {!! json_encode(implode('<br>', $errors->all())) !!});
            }
        @endif
    });
    </script>

    <!-- Main Content -->
    <main class="flex-1 pb-16">
        @yield('content')
    </main>

    <!-- Footer: Bersih, Komersial & Terpercaya (Bukan Tampilan Akademik/AI) -->
    <footer class="bg-[#FFFFFF] dark:bg-[#121214] border-t border-[#212121]/10 dark:border-white/10 mt-auto transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Info Brand -->
                <div class="space-y-3">
                    <div class="flex items-center">
                        <img src="{{ asset('images/logo2.png') }}" alt="KAIDEN" width="144" height="36" class="h-9 w-auto object-contain dark:hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="KAIDEN" width="144" height="36" class="h-9 w-auto object-contain hidden dark:block">
                    </div>
                    <p class="text-xs text-[#212121]/70 dark:text-zinc-400 leading-relaxed">
                        Platform penyedia top up game online, voucher, dan item game terpercaya di Indonesia dengan pengiriman instan.
                    </p>
                </div>

                <!-- Jaminan Layanan -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#212121] dark:text-zinc-200 mb-3">Jaminan Layanan</h4>
                    <ul class="space-y-2 text-xs text-[#212121]/70 dark:text-zinc-400">
                        <li>Deteksi Nickname Otomatis</li>
                        <li>Pengiriman Kilat &lt; 60 Detik</li>
                        <li>Garansi Dana &amp; Riwayat Jelas</li>
                        <li>Keamanan Data Terlindungi</li>
                    </ul>
                </div>

                <!-- Metode Pembayaran -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#212121] dark:text-zinc-200 mb-3">Metode Pembayaran</h4>
                    <div class="flex flex-wrap items-center gap-3">
                        <img src="{{ asset('images/payments/qris.svg') }}" alt="QRIS" class="h-5 w-auto object-contain bg-white dark:bg-zinc-800 p-1 rounded border border-transparent dark:border-white/10" loading="lazy">
                        <img src="{{ asset('images/payments/gopay.svg') }}" alt="GoPay" class="h-4.5 w-auto object-contain bg-white dark:bg-zinc-800 p-1 rounded border border-transparent dark:border-white/10" loading="lazy">
                        <img src="{{ asset('images/payments/dana.svg') }}" alt="DANA" class="h-4.5 w-auto object-contain bg-white dark:bg-zinc-800 p-1 rounded border border-transparent dark:border-white/10" loading="lazy">
                        <img src="{{ asset('images/payments/shopeepay.svg') }}" alt="ShopeePay" class="h-5 w-auto object-contain bg-white dark:bg-zinc-800 p-1 rounded border border-transparent dark:border-white/10" loading="lazy">
                        <img src="{{ asset('images/payments/bca.svg') }}" alt="BCA" class="h-4 w-auto object-contain bg-white dark:bg-zinc-800 p-1 rounded border border-transparent dark:border-white/10" loading="lazy">
                        <img src="{{ asset('images/payments/mandiri.svg') }}" alt="Mandiri" class="h-4 w-auto object-contain bg-white dark:bg-zinc-800 p-1 rounded border border-transparent dark:border-white/10" loading="lazy">
                        <img src="{{ asset('images/payments/bri.svg') }}" alt="BRI" class="h-4 w-auto object-contain bg-white dark:bg-zinc-800 p-1 rounded border border-transparent dark:border-white/10" loading="lazy">
                        <img src="{{ asset('images/payments/indomaret.svg') }}" alt="Indomaret" class="h-4.5 w-auto object-contain bg-white dark:bg-zinc-800 p-1 rounded border border-transparent dark:border-white/10" loading="lazy">
                    </div>
                </div>

                <!-- Pusat Bantuan -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#212121] dark:text-zinc-200 mb-3">Pusat Bantuan</h4>
                    <div>
                        <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number', env('WHATSAPP_NUMBER', '6281234567890')) }}?text={{ urlencode('Halo Admin KAIDEN, saya butuh bantuan terkait top up game.') }}" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-emerald-700 hover:bg-emerald-800 transition shadow-xs">
                            <span>Chat WhatsApp CS</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-[#212121]/10 dark:border-white/10 flex flex-col sm:flex-row items-center justify-between text-xs text-[#212121]/50 dark:text-zinc-500">
                <div>&copy; {{ date('Y') }} KAIDEN. Seluruh hak cipta dilindungi.</div>
                <div class="mt-2 sm:mt-0 font-medium">Platform Top Up Game Online Resmi &amp; Terpercaya</div>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Customer Service Pill: Animasi Halus & Natural (No Lebay, No AI) -->
    <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number', env('WHATSAPP_NUMBER', '6281234567890')) }}?text={{ urlencode('Halo Admin KAIDEN, saya butuh bantuan terkait top up game.') }}"
       target="_blank"
       rel="noopener noreferrer"
       class="group fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-50 inline-flex items-center space-x-2 sm:space-x-2.5 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs shadow-lg hover:shadow-xl transition-all duration-300 border border-white/20 animate-gentle-float hover:animate-none hover:scale-105 active:scale-95"
       title="Hubungi Customer Support KAIDEN via WhatsApp">
        <span class="relative flex h-2 w-2 shrink-0">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-200"></span>
        </span>
        <x-icon name="headphones" class="w-4 h-4 shrink-0 transition-transform duration-300 group-hover:scale-110" />
        <span class="font-medium tracking-tight">Bantuan WhatsApp</span>
    </a>

    <script>
    function toggleMobileNav() {
        const panel = document.getElementById('mobile-nav-panel');
        const iconMenu = document.getElementById('nav-icon-menu');
        const iconClose = document.getElementById('nav-icon-close');
        if (panel) {
            const isHidden = panel.classList.contains('hidden');
            if (isHidden) {
                panel.classList.remove('hidden');
                if (iconMenu && iconClose) {
                    iconMenu.classList.add('hidden');
                    iconClose.classList.remove('hidden');
                }
            } else {
                panel.classList.add('hidden');
                if (iconMenu && iconClose) {
                    iconMenu.classList.remove('hidden');
                    iconClose.classList.add('hidden');
                }
            }
        }
    }

    function toggleKaidenTheme() {
        const html = document.documentElement;
        const isDark = html.classList.toggle('dark');
        try {
            localStorage.setItem('kaiden_theme', isDark ? 'dark' : 'light');
        } catch (e) {}
        syncKaidenThemeState();
    }

    function syncKaidenThemeState() {
        const isDark = document.documentElement.classList.contains('dark');
        document.querySelectorAll('.theme-icon-sun').forEach(el => el.classList.toggle('hidden', !isDark));
        document.querySelectorAll('.theme-icon-moon').forEach(el => el.classList.toggle('hidden', isDark));
        document.querySelectorAll('.theme-label-current').forEach(el => {
            el.textContent = isDark ? 'Gelap' : 'Terang';
        });
    }

    function updateRealtimeWibClock() {
        const clockElements = document.querySelectorAll('.kaiden-wib-clock');
        if (!clockElements.length) return;

        try {
            const now = new Date();
            const formatter = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Jakarta',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });
            const formatted = formatter.format(now).replace(/\./g, ':') + ' WIB';
            clockElements.forEach(el => {
                el.textContent = formatted;
            });
        } catch (e) {
            const now = new Date();
            const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
            const wibDate = new Date(utc + (3600000 * 7));
            const hh = String(wibDate.getHours()).padStart(2, '0');
            const mm = String(wibDate.getMinutes()).padStart(2, '0');
            const ss = String(wibDate.getSeconds()).padStart(2, '0');
            const formatted = `${hh}:${mm}:${ss} WIB`;
            clockElements.forEach(el => {
                el.textContent = formatted;
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        syncKaidenThemeState();
        updateRealtimeWibClock();
        setInterval(updateRealtimeWibClock, 1000);

        // User profile dropdown toggle & click-outside / escape handler
        const userMenuBtn = document.getElementById('user-menu-button');
        const userMenuDropdown = document.getElementById('user-menu-dropdown');

        if (userMenuBtn && userMenuDropdown) {
            userMenuBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const isExpanded = userMenuBtn.getAttribute('aria-expanded') === 'true';
                userMenuBtn.setAttribute('aria-expanded', !isExpanded);
                userMenuDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function(e) {
                if (!userMenuBtn.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                    userMenuDropdown.classList.add('hidden');
                    userMenuBtn.setAttribute('aria-expanded', 'false');
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !userMenuDropdown.classList.contains('hidden')) {
                    userMenuDropdown.classList.add('hidden');
                    userMenuBtn.setAttribute('aria-expanded', 'false');
                    userMenuBtn.focus();
                }
            });
        }
    });
    </script>

    @stack('scripts')
</body>
</html>
