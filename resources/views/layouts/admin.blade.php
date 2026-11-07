<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard - KAIDEN')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo2.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-[#F9F8F6] text-[#212121] font-sans antialiased flex relative">

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-black/60 z-40 hidden md:hidden transition-opacity" onclick="toggleAdminSidebar()"></div>

    <!-- Sidebar Gelap Sesuai RULES.md Section 1.C.2 -->
    <aside id="admin-sidebar" class="fixed md:static inset-y-0 left-0 z-50 w-64 bg-[#212121] text-[#F9F8F6] flex-shrink-0 flex flex-col min-h-screen border-r border-[#212121]/20 -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out">
        <!-- Brand Header -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-white/10">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2.5">
                <img src="{{ asset('images/logo.png') }}" alt="KAIDEN" class="h-9 w-auto object-contain">
                <span class="text-[10px] text-white/50 tracking-widest uppercase font-mono border-l border-white/15 pl-2.5">Panel</span>
            </a>
            <button type="button" onclick="toggleAdminSidebar()" class="md:hidden p-1.5 rounded-lg text-white/50 hover:text-white hover:bg-white/10" title="Tutup Menu">
                <x-icon name="x" class="w-4 h-4" />
            </button>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 px-3 py-6 space-y-6 overflow-y-auto">
            <!-- Bagian Operasional Admin -->
            <div>
                <div class="px-3 mb-2 text-[11px] font-bold tracking-wider text-white/40 uppercase">Operasional</div>
                <div class="space-y-1">
                    @if(auth()->user()->hasPermission('transaksi.lihat'))
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#6D0808] text-white shadow-xs' : 'text-white/70 hover:text-white hover:bg-white/5' }}">
                            <x-icon name="activity" class="w-4 h-4" />
                            <span>Dashboard Ringkasan</span>
                        </a>
                    @endif

                    @if(auth()->user()->hasPermission('produk.lihat'))
                        <a href="{{ route('admin.products.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('admin.products.*') ? 'bg-[#6D0808] text-white shadow-xs' : 'text-white/70 hover:text-white hover:bg-white/5' }}">
                            <x-icon name="package" class="w-4 h-4" />
                            <span>Katalog & Harga</span>
                        </a>
                    @endif

                    @if(auth()->user()->hasPermission('banner.kelola'))
                        <a href="{{ route('admin.banners.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('admin.banners.*') ? 'bg-[#6D0808] text-white shadow-xs' : 'text-white/70 hover:text-white hover:bg-white/5' }}">
                            <x-icon name="image" class="w-4 h-4" />
                            <span>Banner & Promo</span>
                        </a>
                    @endif

                    @if(auth()->user()->hasPermission('transaksi.lihat'))
                        <a href="{{ route('admin.orders.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('admin.orders.*') ? 'bg-[#6D0808] text-white shadow-xs' : 'text-white/70 hover:text-white hover:bg-white/5' }}">
                            <x-icon name="shopping-bag" class="w-4 h-4" />
                            <span>Semua Transaksi</span>
                        </a>
                    @endif

                    @if(auth()->user()->hasPermission('pelanggan.lihat'))
                        <a href="{{ route('admin.customers.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('admin.customers.*') ? 'bg-[#6D0808] text-white shadow-xs' : 'text-white/70 hover:text-white hover:bg-white/5' }}">
                            <x-icon name="users" class="w-4 h-4" />
                            <span>Data Pelanggan</span>
                        </a>
                    @endif

                    @if(auth()->user()->hasPermission('laporan.lihat'))
                        <a href="{{ route('admin.reports.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('admin.reports.*') ? 'bg-[#6D0808] text-white shadow-xs' : 'text-white/70 hover:text-white hover:bg-white/5' }}">
                            <x-icon name="file-text" class="w-4 h-4" />
                            <span>Laporan & Ekspor CSV</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Bagian Tata Kelola Superadmin (RBAC) -->
            @if(auth()->user()->isSuperadmin())
                <div class="pt-4 border-t border-white/10">
                    <div class="px-3 mb-2 text-[11px] font-bold tracking-wider text-[#6D0808] bg-[#6D0808]/15 px-2 py-0.5 rounded w-fit uppercase">
                        Superadmin
                    </div>
                    <div class="space-y-1">
                        @if(auth()->user()->hasPermission('pengguna.kelola'))
                            <a href="{{ route('superadmin.users.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('superadmin.users.*') ? 'bg-[#6D0808] text-white shadow-xs' : 'text-white/70 hover:text-white hover:bg-white/5' }}">
                                <x-icon name="user" class="w-4 h-4" />
                                <span>Kelola Pengguna Internal</span>
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('role.kelola'))
                            <a href="{{ route('superadmin.roles.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('superadmin.roles.*') ? 'bg-[#6D0808] text-white shadow-xs' : 'text-white/70 hover:text-white hover:bg-white/5' }}">
                                <x-icon name="shield" class="w-4 h-4" />
                                <span>Peran & Matriks Izin</span>
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('integrasi.atur') || auth()->user()->hasPermission('ign.konfigurasi'))
                            <a href="{{ route('superadmin.settings.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('superadmin.settings.*') ? 'bg-[#6D0808] text-white shadow-xs' : 'text-white/70 hover:text-white hover:bg-white/5' }}">
                                <x-icon name="settings" class="w-4 h-4" />
                                <span>Kredensial API & Feature Flag</span>
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('audit.lihat'))
                            <a href="{{ route('superadmin.audit_logs.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('superadmin.audit_logs.*') ? 'bg-[#6D0808] text-white shadow-xs' : 'text-white/70 hover:text-white hover:bg-white/5' }}">
                                <x-icon name="history" class="w-4 h-4" />
                                <span>Audit Log (Append-Only)</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer Profile Sidebar -->
        <div class="p-4 border-t border-white/10 bg-black/20">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-[#6D0808] text-white flex items-center justify-center font-bold text-xs uppercase">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-xs font-semibold text-white truncate max-w-[120px]">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-white/50 truncate">{{ auth()->user()->roles->first()->display_name ?? 'Staf' }}</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Keluar" class="p-1.5 text-white/50 hover:text-[#6D0808] hover:bg-white/10 rounded-lg transition">
                        <x-icon name="log-out" class="w-4 h-4" />
                    </button>
                </form>
            </div>
            <div class="mt-3 pt-2 border-t border-white/5 text-center">
                <a href="{{ route('home') }}" class="text-[11px] text-white/60 hover:text-white transition flex items-center justify-center space-x-1">
                    <x-icon name="arrow-left" class="w-3 h-3" />
                    <span>Kembali ke Website</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Topbar -->
        <header class="h-16 bg-[#FFFFFF] border-b border-[#212121]/10 px-4 sm:px-6 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <button type="button" onclick="toggleAdminSidebar()" class="md:hidden p-2 rounded-lg text-[#212121]/70 hover:text-[#212121] hover:bg-[#212121]/5" title="Buka Menu Navigasi">
                    <x-icon name="menu" class="w-5 h-5" />
                </button>
                <h1 class="text-base font-bold tracking-tight text-[#212121]">@yield('header_title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center space-x-3">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-lg text-xs font-mono font-medium bg-[#212121]/5 text-[#212121]/80 border border-[#212121]/10">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>{{ now()->translatedFormat('d F Y') }}</span>
                    <span class="text-[#212121]/30">•</span>
                    <span class="kaiden-wib-clock font-semibold text-[#212121]">--:--:-- WIB</span>
                </div>
            </div>
        </header>

        <!-- Alerts -->
        <div class="px-3 sm:px-6 pt-4">
            @if(session('success'))
                <div class="p-3.5 mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <x-icon name="check-circle" class="w-4 h-4 text-emerald-600 shrink-0" />
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error') || $errors->any())
                <div class="p-3.5 mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-xs">
                    <div class="flex items-center space-x-2 font-semibold mb-1">
                        <x-icon name="alert-circle" class="w-4 h-4 text-red-600 shrink-0" />
                        <span>Terjadi Kesalahan:</span>
                    </div>
                    <ul class="list-disc list-inside pl-6 space-y-0.5 text-xs text-red-700">
                        @if(session('error'))
                            <li>{{ session('error') }}</li>
                        @endif
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
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

            @if(session('error'))
                if (window.kaidenAlert) {
                    window.kaidenAlert.error('Pemberitahuan Sistem', @json(session('error')));
                } else if (window.kaidenToast) {
                    window.kaidenToast.error(@json(session('error')));
                }
            @endif

            @if(session('warning'))
                if (window.kaidenToast) {
                    window.kaidenToast.warning(@json(session('warning')));
                }
            @endif

            @if(session('info'))
                if (window.kaidenToast) {
                    window.kaidenToast.info(@json(session('info')));
                }
            @endif

            @if($errors->any() && !session('error'))
                if (window.kaidenAlert) {
                    window.kaidenAlert.error('Validasi Gagal', {!! json_encode(implode('<br>', $errors->all())) !!});
                }
            @endif
        });
        </script>

        <!-- Body Content -->
        <main class="flex-1 px-3 sm:px-6 pb-12">
            @yield('content')
        </main>
    </div>

    <script>
    function toggleAdminSidebar() {
        const sidebar = document.getElementById('admin-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        if (sidebar && backdrop) {
            const isClosed = sidebar.classList.contains('-translate-x-full');
            if (isClosed) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden', 'md:overflow-auto');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden', 'md:overflow-auto');
            }
        }
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
        updateRealtimeWibClock();
        setInterval(updateRealtimeWibClock, 1000);
    });
    </script>

    @stack('scripts')
</body>
</html>

