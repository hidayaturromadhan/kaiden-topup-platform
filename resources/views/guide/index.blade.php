@extends('layouts.app')

@section('title', 'Panduan Pengisian ID Akun Game Resmi - KAIDEN')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 sm:pt-8 pb-20 space-y-6 sm:space-y-8">

    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="flex items-center space-x-2 text-xs text-stone-500 dark:text-zinc-400">
        <a href="{{ route('home') }}" class="hover:text-[#6D0808] dark:hover:text-rose-400 transition">Beranda</a>
        <x-icon name="chevron-right" class="w-3 h-3 text-stone-400 dark:text-zinc-600" />
        <span class="text-stone-800 dark:text-zinc-200 font-semibold">Panduan Pengisian ID</span>
    </nav>

    <!-- Header Banner Panduan -->
    <header class="relative overflow-hidden rounded-2xl bg-[#212121] text-white p-6 sm:p-9 border border-white/10 shadow-sm" style="background-color: #212121; color: #ffffff;">
        <div class="absolute -right-12 -bottom-12 w-80 h-80 bg-[#6D0808]/40 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl space-y-3.5">
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full text-[11px] font-bold text-amber-300 uppercase tracking-wider" style="background-color: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.15);">
                <x-icon name="book-open" class="w-3.5 h-3.5 text-amber-400" />
                <span>Pusat Bantuan &amp; Edukasi Top Up</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-black tracking-tight text-white leading-tight">
                Panduan Pengisian ID Akun Game
            </h1>
            <p class="text-xs sm:text-sm text-stone-200 leading-relaxed max-w-2xl">
                Pilih game di bawah untuk melihat cara menemukan User ID, Server / Zone ID, serta format Tagline akun Anda. Seluruh transaksi di KAIDEN dilengkapi pengecekan nama akun otomatis (<span class="text-emerald-400 font-semibold">Auto-Check Nickname</span>).
            </p>

            <!-- Quick Live Search Bar -->
            <div class="pt-1 max-w-md">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-300">
                        <x-icon name="search" class="w-4 h-4" />
                    </div>
                    <input type="text" 
                           id="guideSearchInput" 
                           placeholder="Cari panduan game (contoh: Mobile Legends, Valorant...)" 
                           style="background-color: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: #ffffff;"
                           class="w-full pl-10 pr-10 py-2.5 rounded-xl text-xs sm:text-sm text-white placeholder-stone-300 focus:outline-none focus:ring-2 focus:ring-[#6D0808] focus:border-transparent transition backdrop-blur-sm"
                           autocomplete="off">
                    <button type="button" 
                            id="clearSearchBtn" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-stone-400 hover:text-white hidden transition">
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Tombol Pilihan Game (Modern Tab Bar - Rapi, Responsif, Tidak Terpotong) -->
    <div class="space-y-3 pt-1">
        <div class="flex items-center justify-between px-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-zinc-400">
                Pilih Game untuk Melihat Panduan:
            </span>
            <span class="text-[11px] text-stone-500 dark:text-zinc-400 font-medium">
                {{ $games->count() }} Game Resmi
            </span>
        </div>

        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
            <!-- Tombol Tab Semua Game -->
            <button type="button" 
                    id="tab-btn-all"
                    onclick="selectGameTab('all')"
                    class="game-tab-btn inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-white dark:bg-[#18181B] border border-stone-200/80 dark:border-white/10 text-stone-700 dark:text-zinc-300 hover:border-[#6D0808] hover:text-[#6D0808] dark:hover:border-rose-500 dark:hover:text-rose-400 shadow-2xs transition cursor-pointer">
                <x-icon name="list" class="w-3.5 h-3.5" />
                <span>Semua Game</span>
            </button>

            <!-- Tombol Tab Per Game -->
            @foreach($games as $index => $g)
                @php
                    $thumb = $g->thumbnail ?? match($g->slug) {
                        'mobile-legends' => 'images/bg/aspirant.jpg',
                        'free-fire' => 'images/bg/freefire.jpg',
                        'valorant' => 'images/bg/valorant.jpg',
                        'call-of-duty-mobile' => 'images/bg/codm.jpg',
                        'arena-of-valor' => 'images/bg/aov.jpg',
                        default => 'images/bg/1.jpg',
                    };
                    $isActive = ($selectedGame && $selectedGame->id === $g->id) || (!$selectedGame && $index === 0);
                @endphp
                <button type="button" 
                        id="tab-btn-{{ $g->slug }}"
                        data-game-slug="{{ $g->slug }}"
                        data-game-name="{{ strtolower($g->name) }}"
                        onclick="selectGameTab('{{ $g->slug }}')"
                        class="game-tab-btn inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl text-xs transition cursor-pointer {{ $isActive ? 'active-tab bg-[#6D0808] text-white border border-[#6D0808] font-bold shadow-xs' : 'bg-white dark:bg-[#18181B] border border-stone-200/80 dark:border-white/10 text-stone-700 dark:text-zinc-300 hover:border-[#6D0808] hover:text-[#6D0808] dark:hover:border-rose-500 dark:hover:text-rose-400 font-semibold shadow-2xs' }}">
                    <img src="{{ asset($thumb) }}" 
                         alt="{{ $g->name }}" 
                         loading="lazy"
                         class="w-4 h-4 rounded-md object-cover">
                    <span>{{ $g->name }}</span>
                    @if($g->needs_zone)
                        <span class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-amber-300' : 'bg-amber-500' }}" title="Membutuhkan Zone ID"></span>
                    @endif
                </button>
            @endforeach

            <!-- Tombol Pintas FAQ -->
            <button type="button" 
                    id="tab-btn-faq"
                    onclick="scrollToFaq()"
                    class="game-tab-btn inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 shadow-2xs transition hover:bg-amber-100 dark:hover:bg-amber-900/40 cursor-pointer">
                <x-icon name="help-circle" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" />
                <span>Pertanyaan Umum (FAQ)</span>
            </button>
        </div>
    </div>

    <!-- Main Content: Area Showcase Panduan Game Terpilih -->
    <main class="space-y-8">

        <!-- Empty Search Alert -->
        <div id="noSearchResults" class="hidden p-8 sm:p-12 text-center rounded-2xl bg-white dark:bg-[#18181B] border border-stone-200/80 dark:border-white/10 shadow-xs space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-stone-100 dark:bg-zinc-800 text-stone-400 dark:text-zinc-500 flex items-center justify-center mx-auto">
                <x-icon name="search" class="w-6 h-6" />
            </div>
            <h3 class="text-base font-bold text-stone-800 dark:text-zinc-200">Panduan Game Tidak Ditemukan</h3>
            <p class="text-xs text-stone-500 dark:text-zinc-400 max-w-sm mx-auto">
                Tidak ada panduan game yang sesuai dengan kata kunci pencarian Anda. Pastikan ejaan benar atau cari nama game lain.
            </p>
            <button type="button" 
                    onclick="resetSearch()" 
                    class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl text-xs font-bold text-[#6D0808] dark:text-rose-400 bg-[#6D0808]/10 dark:bg-rose-950/40 hover:bg-[#6D0808]/20 transition">
                <x-icon name="refresh" class="w-3.5 h-3.5" />
                <span>Tampilkan Semua Panduan</span>
            </button>
        </div>

        <!-- Panel Tampilan Semua Game (Muncul Hanya Saat Tab 'Semua Game' Dipilih) -->
        <div id="panel-all" class="game-guide-panel hidden space-y-4">
            <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#18181B] border border-stone-200/80 dark:border-white/10 shadow-xs">
                <h2 class="text-sm sm:text-base font-bold text-stone-900 dark:text-zinc-100 mb-1">Daftar Seluruh Game Resmi</h2>
                <p class="text-xs text-stone-500 dark:text-zinc-400 mb-4">Klik tombol "Lihat Panduan" pada game yang ingin Anda ketahui petunjuk pengisian ID-nya.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
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
                        <button type="button" 
                                onclick="selectGameTab('{{ $g->slug }}')"
                                class="p-3 rounded-xl border border-stone-200/80 dark:border-white/10 hover:border-[#6D0808] dark:hover:border-rose-500 bg-stone-50/50 dark:bg-zinc-800/30 text-left flex items-center space-x-3 transition cursor-pointer group">
                            <img src="{{ asset($thumb) }}" alt="{{ $g->name }}" class="w-10 h-10 rounded-lg object-cover border border-stone-200 dark:border-zinc-700 shrink-0">
                            <div class="min-w-0 flex-1">
                                <div class="text-xs font-bold text-stone-800 dark:text-zinc-200 group-hover:text-[#6D0808] dark:group-hover:text-rose-400 truncate">{{ $g->name }}</div>
                                <div class="text-[10px] text-stone-400 dark:text-zinc-500 truncate">{{ $g->category }}</div>
                            </div>
                            <x-icon name="chevron-right" class="w-4 h-4 text-stone-400 group-hover:text-[#6D0808] dark:group-hover:text-rose-400 shrink-0 transition group-hover:translate-x-0.5" />
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Panel Per Game (Showcase Fokus: 1 Game Aktif Ditampilkan Secara Eksklusif & Elegan) -->
        @foreach($games as $index => $g)
            @php
                $thumb = $g->thumbnail ?? match($g->slug) {
                    'mobile-legends' => 'images/bg/aspirant.jpg',
                    'free-fire' => 'images/bg/freefire.jpg',
                    'valorant' => 'images/bg/valorant.jpg',
                    'call-of-duty-mobile' => 'images/bg/codm.jpg',
                    'arena-of-valor' => 'images/bg/aov.jpg',
                    default => 'images/bg/1.jpg',
                };
                $minProduct = $g->activeProducts->first();
                $minPrice = $minProduct ? $minProduct->sell_price : null;
                $isActive = ($selectedGame && $selectedGame->id === $g->id) || (!$selectedGame && $index === 0);

                // Ekstraksi 3 langkah tutorial spesifik per game
                $steps = match($g->slug) {
                    'mobile-legends' => [
                        ['title' => 'Buka Game & Profil', 'desc' => 'Buka aplikasi Mobile Legends di HP Anda, lalu ketuk foto avatar profil di sudut kiri atas layar utama.'],
                        ['title' => 'Lihat Nomor ID & Server', 'desc' => 'Pada menu Info Dasar di bawah nickname, temukan deretan angka: misalnya 12345678 (2001).'],
                        ['title' => 'Pisahkan User ID & Zone ID', 'desc' => 'Ketik angka luar kurung (12345678) di kolom User ID, dan angka dalam kurung (2001) di kolom Zone ID KAIDEN.'],
                    ],
                    'free-fire', 'free-fire-max' => [
                        ['title' => 'Buka Garena Free Fire', 'desc' => 'Masuk ke lobi utama game Free Fire atau Free Fire MAX di ponsel Anda.'],
                        ['title' => 'Buka Profil Pemain', 'desc' => 'Ketuk banner nama akun atau avatar profil Anda yang berada di pojok kiri atas layar.'],
                        ['title' => 'Salin Nomor Player UID', 'desc' => 'Pada tab profil utama, lihat baris UID (contoh: 87654321). Klik tombol ikon salin di sampingnya.'],
                    ],
                    'valorant' => [
                        ['title' => 'Buka Riot Client / Game', 'desc' => 'Buka game Valorant di PC Anda dan masuk ke halaman lobi utama.'],
                        ['title' => 'Cek Riot ID & Tagline', 'desc' => 'Arahkan kursor ke profil di kanan atas atau buka daftar teman. Temukan format Riot ID dan Tagline.'],
                        ['title' => 'Wajib Sertakan Tanda #', 'desc' => 'Ketik format lengkap username beserta tanda pagar # dan tagline (contoh: PlayerSatu#ID1).'],
                    ],
                    'call-of-duty-mobile' => [
                        ['title' => 'Buka Pengaturan CODM', 'desc' => 'Ketuk ikon gerigi Pengaturan (Settings) di bagian atas layar lobi Call of Duty: Mobile.'],
                        ['title' => 'Pilih Tab "Lainnya"', 'desc' => 'Geser bilah menu pengaturan paling kanan dan pilih tab "Lainnya / Others".'],
                        ['title' => 'Salin Nomor OpenID', 'desc' => 'Di sudut kanan bawah layar, temukan nomor OpenID Anda dan tekan tombol salin.'],
                    ],
                    'arena-of-valor' => [
                        ['title' => 'Buka Profil Pemain AOV', 'desc' => 'Buka game Arena of Valor dan ketuk foto avatar profil Anda di sudut kiri atas layar lobi.'],
                        ['title' => 'Buka Pengaturan Akun', 'desc' => 'Pilih tab Pengaturan Akun pada bilah menu info pemain.'],
                        ['title' => 'Salin OpenID Akun', 'desc' => 'Temukan deretan nomor OpenID Anda dan salin ke formulir pesanan KAIDEN.'],
                    ],
                    default => [
                        ['title' => 'Buka Aplikasi Game', 'desc' => 'Masuk ke dalam aplikasi game dan tunggu hingga tiba di menu utama.'],
                        ['title' => 'Masuk Menu Profil', 'desc' => $g->guide_text ?? 'Buka menu profil atau pengaturan akun untuk menemukan User ID Anda.'],
                        ['title' => 'Salin / Catat Nomor ID', 'desc' => $g->needs_zone ? 'Catat User ID serta Server / Zone ID akun Anda secara terpisah.' : 'Salin User ID dan masukkan ke formulir pemesanan KAIDEN.'],
                    ],
                };

                $sampleFormat = match($g->slug) {
                    'mobile-legends' => 'User ID: 12345678 | Zone ID: 2001 (Format Profil: 12345678 (2001))',
                    'free-fire', 'free-fire-max' => 'UID: 87654321 (8-10 digit angka)',
                    'valorant' => 'Riot ID: NamaAkun#TAG (Contoh: Kiana#001)',
                    'call-of-duty-mobile' => 'OpenID: 674891028392019',
                    'arena-of-valor' => 'OpenID: a9b8c7d6e5f4g3h2',
                    default => 'User ID: [Nomor Akun Game]' . ($g->needs_zone ? ' | Zone ID: [Nomor Server]' : ''),
                };

                $copyVal = match($g->slug) {
                    'mobile-legends' => '12345678',
                    'free-fire', 'free-fire-max' => '87654321',
                    'valorant' => 'PlayerName#ID1',
                    'call-of-duty-mobile' => '674891028392019',
                    'arena-of-valor' => 'a9b8c7d6e5f4g3h2',
                    default => '12345678',
                };
            @endphp

            <div id="panel-{{ $g->slug }}" 
                 data-game-slug="{{ $g->slug }}"
                 data-game-name="{{ strtolower($g->name) }}"
                 class="game-guide-panel {{ $isActive ? '' : 'hidden' }} space-y-6">

                <!-- Kartu Utama Panduan Game -->
                <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-[#18181B] border border-stone-200/80 dark:border-white/10 shadow-xs space-y-6">

                    <!-- Header Panduan Game -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-stone-100 dark:border-zinc-800">
                        <div class="flex items-start sm:items-center space-x-4">
                            <!-- Thumbnail Cover -->
                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl overflow-hidden bg-stone-100 dark:bg-zinc-800 shrink-0 border border-stone-200 dark:border-white/10 shadow-2xs">
                                <img src="{{ asset($thumb) }}" 
                                     alt="{{ $g->name }}" 
                                     loading="lazy" 
                                     decoding="async" 
                                     class="w-full h-full object-cover">
                            </div>

                            <!-- Info Judul & Kategori -->
                            <div class="min-w-0 space-y-1">
                                <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#6D0808] dark:text-rose-400 bg-[#6D0808]/5 dark:bg-rose-950/40 px-2.5 py-0.5 rounded-full border border-[#6D0808]/15 dark:border-rose-800/40">
                                        {{ $g->category }}
                                    </span>
                                    <span class="text-[11px] text-stone-500 dark:text-zinc-400">
                                        {{ $g->publisher ?? 'Official Publisher' }}
                                    </span>
                                </div>
                                <h2 class="text-xl sm:text-2xl font-black text-stone-900 dark:text-zinc-100 tracking-tight leading-tight">
                                    Panduan ID {{ $g->name }}
                                </h2>
                                <div class="flex items-center space-x-2 flex-wrap gap-y-1 pt-0.5 text-xs">
                                    <span class="inline-flex items-center font-semibold text-emerald-700 dark:text-emerald-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                                        Auto-Check Nickname Aktif
                                    </span>
                                    @if($g->needs_zone)
                                        <span class="inline-flex items-center font-semibold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-md border border-amber-200 dark:border-amber-800/60 text-[10px] sm:text-[11px]">
                                            Memerlukan Zone ID
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- CTA Tombol Top Up -->
                        <div class="shrink-0 self-start sm:self-center">
                            <a href="{{ route('order.game', $g->slug) }}" 
                               class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs group">
                                <span>Top Up Sekarang</span>
                                <x-icon name="arrow-right" class="w-3.5 h-3.5 transition group-hover:translate-x-0.5" />
                            </a>
                        </div>
                    </div>

                    <!-- Deskripsi Ringkas Game -->
                    @if($g->description)
                        <p class="text-xs sm:text-sm text-stone-600 dark:text-zinc-300 leading-relaxed">
                            {{ $g->description }}
                        </p>
                    @endif

                    <!-- 3 Langkah Visual (Grid 3 Kolom Rapi & Lega) -->
                    <div class="space-y-3">
                        <div class="text-xs font-bold text-stone-900 dark:text-zinc-200 flex items-center space-x-2">
                            <x-icon name="alert-circle" class="w-4 h-4 text-[#6D0808] dark:text-rose-400" />
                            <span>Langkah Menemukan ID Akun {{ $g->name }}:</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                            @foreach($steps as $sIndex => $s)
                                <div class="p-4 rounded-xl bg-stone-50/70 dark:bg-zinc-800/30 border border-stone-200/60 dark:border-white/5 space-y-2">
                                    <div class="w-6 h-6 rounded-lg bg-[#6D0808]/10 dark:bg-rose-950/50 text-[#6D0808] dark:text-rose-400 font-bold text-xs flex items-center justify-center">
                                        {{ $sIndex + 1 }}
                                    </div>
                                    <div class="text-xs font-bold text-stone-900 dark:text-zinc-100">
                                        {{ $s['title'] }}
                                    </div>
                                    <p class="text-[11px] text-stone-600 dark:text-zinc-400 leading-relaxed">
                                        {{ $s['desc'] }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Format Input Callout Box (Clean Code Snippet) -->
                    <div class="p-4 sm:p-5 rounded-xl bg-stone-50 dark:bg-[#121214] border border-stone-200/90 dark:border-white/10 space-y-2.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-stone-700 dark:text-zinc-300 uppercase tracking-wider text-[10px]">
                                Contoh Format Input di Formulir KAIDEN
                            </span>
                            <button type="button" 
                                    onclick="copyGuideSample('{{ $copyVal }}', this)" 
                                    class="inline-flex items-center space-x-1 text-[11px] font-semibold text-[#6D0808] dark:text-rose-400 hover:underline cursor-pointer">
                                <x-icon name="copy" class="w-3 h-3" />
                                <span>Salin Contoh</span>
                            </button>
                        </div>
                        <div class="p-3 rounded-lg bg-white dark:bg-zinc-900 border border-stone-200/60 dark:border-white/5 font-mono text-xs sm:text-sm text-stone-900 dark:text-zinc-100 font-semibold select-all overflow-x-auto">
                            {{ $sampleFormat }}
                        </div>
                        <p class="text-[11px] text-stone-500 dark:text-zinc-400 leading-relaxed">
                            @if($g->needs_zone)
                                Masukkan nomor <span class="font-semibold text-stone-800 dark:text-zinc-200">User ID</span> dan <span class="font-semibold text-stone-800 dark:text-zinc-200">Zone ID</span> pada dua kolom yang berbeda di formulir pemesanan.
                            @elseif($g->slug === 'valorant')
                                Pastikan tidak menghapus tanda pagar <span class="font-semibold text-stone-800 dark:text-zinc-200">#</span> antara nama dan tagline akun Riot Anda.
                            @else
                                Masukkan angka User ID tanpa spasi maupun tanda baca agar proses verifikasi nama akun berjalan instan.
                            @endif
                        </p>
                    </div>

                    <!-- Footer Artikel Game: Informasi Harga & Aksi Cepat -->
                    <div class="pt-4 border-t border-stone-100 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div class="text-stone-500 dark:text-zinc-400">
                            @if($minPrice)
                                Harga mulai dari <span class="font-bold text-[#6D0808] dark:text-rose-400">Rp {{ number_format($minPrice, 0, ',', '.') }}</span>
                                <span class="mx-1.5">•</span>
                            @endif
                            <span>{{ $g->active_products_count }} Pilihan Paket Nominal Instan</span>
                        </div>

                        <a href="{{ route('order.game', $g->slug) }}" 
                           class="inline-flex items-center space-x-1.5 font-bold text-[#6D0808] dark:text-rose-400 hover:text-[#540606] dark:hover:text-rose-300 transition">
                            <span>Beli Produk {{ $g->name }} Sekarang</span>
                            <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                        </a>
                    </div>

                </div>

            </div>
        @endforeach

        <!-- Bagian Pertanyaan Umum (FAQ) - Redesigned: Bersih, Rapi, Minimalis, Tanpa Kotak Bertumpuk -->
        <section id="faq" class="scroll-mt-24 sm:scroll-mt-28 p-6 sm:p-8 rounded-2xl bg-white dark:bg-[#18181B] border border-stone-200/80 dark:border-white/10 shadow-xs space-y-5">
            <!-- Header FAQ -->
            <div class="space-y-1.5 pb-4 border-b border-stone-100 dark:border-zinc-800">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 text-[11px] font-bold text-amber-800 dark:text-amber-300 uppercase tracking-wider">
                    <x-icon name="help-circle" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" />
                    <span>Tanya Jawab &amp; Solusi Kendala</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-stone-900 dark:text-zinc-100 tracking-tight">
                    Pertanyaan Umum Seputar ID Game (FAQ)
                </h2>
                <p class="text-xs text-stone-500 dark:text-zinc-400 leading-relaxed">
                    Jawaban ringkas atas kendala dan pertanyaan yang paling sering dialami gamer saat mengisi data akun di KAIDEN.
                </p>
            </div>

            <!-- List FAQ Bersih (Gaya Accordion Minimalis Apple / Stripe: Tanpa Kotak Tebal Bertumpuk) -->
            <div class="divide-y divide-stone-100 dark:divide-zinc-800/80">

                <!-- FAQ 1 -->
                <details class="group py-3.5 first:pt-1 last:pb-1">
                    <summary class="flex items-center justify-between cursor-pointer list-none select-none text-xs sm:text-sm font-bold text-stone-900 dark:text-zinc-100 hover:text-[#6D0808] dark:hover:text-rose-400 transition py-1">
                        <div class="flex items-center space-x-3 pr-4">
                            <span class="w-5 h-5 rounded-md bg-stone-100 dark:bg-zinc-800 text-stone-600 dark:text-zinc-400 font-bold text-[11px] flex items-center justify-center shrink-0 group-hover:bg-[#6D0808]/10 group-hover:text-[#6D0808] dark:group-hover:text-rose-400 transition">1</span>
                            <span>Bagaimana jika saya salah memasukkan User ID atau Server saat memesan?</span>
                        </div>
                        <div class="w-6 h-6 rounded-md bg-stone-100 dark:bg-zinc-800 flex items-center justify-center shrink-0 transition-transform duration-200 group-open:rotate-180">
                            <x-icon name="chevron-down" class="w-3.5 h-3.5 text-stone-500 dark:text-zinc-400" />
                        </div>
                    </summary>
                    <div class="pl-8 pr-2 pt-2 pb-1 text-xs sm:text-sm text-stone-600 dark:text-zinc-400 leading-relaxed">
                        Sistem KAIDEN dilengkapi proteksi <strong class="text-stone-800 dark:text-zinc-200">Auto-Check Nickname</strong> yang otomatis menampilkan nama akun game Anda di layar sebelum pembayaran. Jika nama akun tidak cocok, jangan lanjutkan pembayaran dan periksa kembali ID Anda. Jika pesanan sudah terlanjur dibayar namun gagal, hubungi Customer Service kami via WhatsApp untuk penanganan atau refund manual.
                    </div>
                </details>

                <!-- FAQ 2 -->
                <details class="group py-3.5 first:pt-1 last:pb-1">
                    <summary class="flex items-center justify-between cursor-pointer list-none select-none text-xs sm:text-sm font-bold text-stone-900 dark:text-zinc-100 hover:text-[#6D0808] dark:hover:text-rose-400 transition py-1">
                        <div class="flex items-center space-x-3 pr-4">
                            <span class="w-5 h-5 rounded-md bg-stone-100 dark:bg-zinc-800 text-stone-600 dark:text-zinc-400 font-bold text-[11px] flex items-center justify-center shrink-0 group-hover:bg-[#6D0808]/10 group-hover:text-[#6D0808] dark:group-hover:text-rose-400 transition">2</span>
                            <span>Apa itu Zone ID / Server ID dan mengapa beberapa game membutuhkannya?</span>
                        </div>
                        <div class="w-6 h-6 rounded-md bg-stone-100 dark:bg-zinc-800 flex items-center justify-center shrink-0 transition-transform duration-200 group-open:rotate-180">
                            <x-icon name="chevron-down" class="w-3.5 h-3.5 text-stone-500 dark:text-zinc-400" />
                        </div>
                    </summary>
                    <div class="pl-8 pr-2 pt-2 pb-1 text-xs sm:text-sm text-stone-600 dark:text-zinc-400 leading-relaxed">
                        Zone ID atau Server ID adalah kode penanda server tempat akun Anda didaftarkan (contoh: pada Mobile Legends, Zone ID berupa 4–5 digit angka di dalam kurung seperti <code class="font-mono font-bold text-stone-800 dark:text-zinc-200">2001</code>). Distributor resmi membutuhkan nomor server ini untuk mengarahkan diamond ke server regional yang tepat.
                    </div>
                </details>

                <!-- FAQ 3 -->
                <details class="group py-3.5 first:pt-1 last:pb-1">
                    <summary class="flex items-center justify-between cursor-pointer list-none select-none text-xs sm:text-sm font-bold text-stone-900 dark:text-zinc-100 hover:text-[#6D0808] dark:hover:text-rose-400 transition py-1">
                        <div class="flex items-center space-x-3 pr-4">
                            <span class="w-5 h-5 rounded-md bg-stone-100 dark:bg-zinc-800 text-stone-600 dark:text-zinc-400 font-bold text-[11px] flex items-center justify-center shrink-0 group-hover:bg-[#6D0808]/10 group-hover:text-[#6D0808] dark:group-hover:text-rose-400 transition">3</span>
                            <span>Berapa lama waktu yang dibutuhkan hingga item / diamond masuk?</span>
                        </div>
                        <div class="w-6 h-6 rounded-md bg-stone-100 dark:bg-zinc-800 flex items-center justify-center shrink-0 transition-transform duration-200 group-open:rotate-180">
                            <x-icon name="chevron-down" class="w-3.5 h-3.5 text-stone-500 dark:text-zinc-400" />
                        </div>
                    </summary>
                    <div class="pl-8 pr-2 pt-2 pb-1 text-xs sm:text-sm text-stone-600 dark:text-zinc-400 leading-relaxed">
                        Lebih dari 99% pesanan diproses otomatis dalam waktu <strong class="text-stone-800 dark:text-zinc-200">30 hingga 60 detik</strong> setelah pembayaran Anda terverifikasi oleh Midtrans (QRIS, E-Wallet, atau Virtual Account).
                    </div>
                </details>

                <!-- FAQ 4 -->
                <details class="group py-3.5 first:pt-1 last:pb-1">
                    <summary class="flex items-center justify-between cursor-pointer list-none select-none text-xs sm:text-sm font-bold text-stone-900 dark:text-zinc-100 hover:text-[#6D0808] dark:hover:text-rose-400 transition py-1">
                        <div class="flex items-center space-x-3 pr-4">
                            <span class="w-5 h-5 rounded-md bg-stone-100 dark:bg-zinc-800 text-stone-600 dark:text-zinc-400 font-bold text-[11px] flex items-center justify-center shrink-0 group-hover:bg-[#6D0808]/10 group-hover:text-[#6D0808] dark:group-hover:text-rose-400 transition">4</span>
                            <span>Apakah saya harus memberikan email atau password game saya?</span>
                        </div>
                        <div class="w-6 h-6 rounded-md bg-stone-100 dark:bg-zinc-800 flex items-center justify-center shrink-0 transition-transform duration-200 group-open:rotate-180">
                            <x-icon name="chevron-down" class="w-3.5 h-3.5 text-stone-500 dark:text-zinc-400" />
                        </div>
                    </summary>
                    <div class="pl-8 pr-2 pt-2 pb-1 text-xs sm:text-sm text-stone-600 dark:text-zinc-400 leading-relaxed">
                        <strong class="text-rose-600 dark:text-rose-400">SAMA SEKALI TIDAK PERLU.</strong> Layanan top up KAIDEN hanya membutuhkan User ID publik akun Anda. Kami tidak akan pernah meminta password, kode OTP, PIN, atau verifikasi login email. Akun Anda 100% aman.
                    </div>
                </details>

                <!-- FAQ 5 -->
                <details class="group py-3.5 first:pt-1 last:pb-1">
                    <summary class="flex items-center justify-between cursor-pointer list-none select-none text-xs sm:text-sm font-bold text-stone-900 dark:text-zinc-100 hover:text-[#6D0808] dark:hover:text-rose-400 transition py-1">
                        <div class="flex items-center space-x-3 pr-4">
                            <span class="w-5 h-5 rounded-md bg-stone-100 dark:bg-zinc-800 text-stone-600 dark:text-zinc-400 font-bold text-[11px] flex items-center justify-center shrink-0 group-hover:bg-[#6D0808]/10 group-hover:text-[#6D0808] dark:group-hover:text-rose-400 transition">5</span>
                            <span>Status transaksi sudah "SUKSES" tetapi diamond belum bertambah di game?</span>
                        </div>
                        <div class="w-6 h-6 rounded-md bg-stone-100 dark:bg-zinc-800 flex items-center justify-center shrink-0 transition-transform duration-200 group-open:rotate-180">
                            <x-icon name="chevron-down" class="w-3.5 h-3.5 text-stone-500 dark:text-zinc-400" />
                        </div>
                    </summary>
                    <div class="pl-8 pr-2 pt-2 pb-1 text-xs sm:text-sm text-stone-600 dark:text-zinc-400 leading-relaxed">
                        Silakan lakukan <strong class="text-stone-800 dark:text-zinc-200">Relog</strong>: tutup aplikasi game sepenuhnya (Force Close) lalu buka kembali agar cache aplikasi memperbarui saldo terbaru dari server game. Jika masih belum masuk setelah 5 menit, segera hubungi Customer Care WhatsApp kami.
                    </div>
                </details>

            </div>
        </section>

        <!-- Bantuan Customer Care (Footer CTA) -->
        <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-[#18181B] border border-stone-200/80 dark:border-white/10 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-5">
            <div class="flex items-start sm:items-center space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-200/70 dark:border-emerald-800/60">
                    <x-icon name="headphones" class="w-6 h-6" />
                </div>
                <div class="space-y-1">
                    <h3 class="text-sm sm:text-base font-bold text-stone-900 dark:text-zinc-100">
                        Masih Bingung Menemukan ID Akun Anda?
                    </h3>
                    <p class="text-xs text-stone-500 dark:text-zinc-400 leading-relaxed">
                        Kirim tangkapan layar (screenshot) profil game Anda ke WhatsApp CS kami, tim kami siap memandu Anda secara langsung 24/7.
                    </p>
                </div>
            </div>
            <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number', env('WHATSAPP_NUMBER', '6281234567890')) }}?text={{ urlencode('Halo Admin KAIDEN, saya butuh bantuan untuk menemukan nomor ID game saya.') }}" 
               target="_blank" 
               rel="noopener noreferrer" 
               class="inline-flex items-center justify-center space-x-2 px-5 py-3 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 transition shadow-xs shrink-0">
                <x-icon name="headphones" class="w-4 h-4" />
                <span>Chat CS WhatsApp</span>
            </a>
        </div>

    </main>

</div>

<!-- Interactive JavaScript: Tab Switcher, Live Search Filter, FAQ Scroll & Copy Sample -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('guideSearchInput');
        const clearBtn = document.getElementById('clearSearchBtn');
        const panels = document.querySelectorAll('.game-guide-panel');
        const tabBtns = document.querySelectorAll('.game-tab-btn');
        const noResults = document.getElementById('noSearchResults');

        // Fungsi Memilih Tab Game Secara Halus & Instan
        window.selectGameTab = function (slug) {
            // Update styling tombol tab
            tabBtns.forEach(function (btn) {
                btn.classList.remove('active-tab', 'bg-[#6D0808]', 'text-white', 'border-[#6D0808]', 'font-bold', 'shadow-xs');
                btn.classList.add('bg-white', 'dark:bg-[#18181B]', 'text-stone-700', 'dark:text-zinc-300', 'border-stone-200/80', 'dark:border-white/10', 'font-semibold', 'shadow-2xs');
            });

            const targetBtn = document.getElementById('tab-btn-' + slug);
            if (targetBtn) {
                targetBtn.classList.remove('bg-white', 'dark:bg-[#18181B]', 'text-stone-700', 'dark:text-zinc-300', 'border-stone-200/80', 'dark:border-white/10', 'font-semibold', 'shadow-2xs');
                targetBtn.classList.add('active-tab', 'bg-[#6D0808]', 'text-white', 'border-[#6D0808]', 'font-bold', 'shadow-xs');
            }

            // Sembunyikan semua panel dan munculkan panel yang dipilih
            panels.forEach(function (panel) {
                panel.classList.add('hidden');
            });

            const targetPanel = document.getElementById('panel-' + slug);
            if (targetPanel) {
                targetPanel.classList.remove('hidden');
            }
        };

        // Scroll mulus ke FAQ dengan jarak aman 80px dari navbar
        window.scrollToFaq = function () {
            tabBtns.forEach(function (btn) {
                btn.classList.remove('active-tab', 'bg-[#6D0808]', 'text-white', 'border-[#6D0808]', 'font-bold', 'shadow-xs');
                btn.classList.add('bg-white', 'dark:bg-[#18181B]', 'text-stone-700', 'dark:text-zinc-300', 'border-stone-200/80', 'dark:border-white/10', 'font-semibold', 'shadow-2xs');
            });
            const faqBtn = document.getElementById('tab-btn-faq');
            if (faqBtn) {
                faqBtn.classList.add('active-tab', 'bg-[#6D0808]', 'text-white', 'border-[#6D0808]', 'font-bold', 'shadow-xs');
            }

            const faq = document.getElementById('faq');
            if (faq) {
                const navOffset = 80;
                const elementPosition = faq.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - navOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        };

        // Live Search Filter: Menyesuaikan game yang dicari pengguna secara cerdas
        function filterGuides(query) {
            const cleanQuery = query.toLowerCase().trim();
            let matchedSlug = null;
            let matchCount = 0;

            if (clearBtn) {
                if (cleanQuery.length > 0) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }
            }

            tabBtns.forEach(function (btn) {
                const gameName = btn.getAttribute('data-game-name') || '';
                const gameSlug = btn.getAttribute('data-game-slug') || '';
                
                // Jangan sembunyikan tombol 'Semua Game' dan 'FAQ'
                if (btn.id === 'tab-btn-all' || btn.id === 'tab-btn-faq') {
                    return;
                }

                const matches = !cleanQuery || gameName.includes(cleanQuery) || gameSlug.includes(cleanQuery);
                if (matches) {
                    btn.style.display = '';
                    matchCount++;
                    if (!matchedSlug) {
                        matchedSlug = gameSlug;
                    }
                } else {
                    btn.style.display = 'none';
                }
            });

            if (cleanQuery.length > 0 && matchedSlug) {
                selectGameTab(matchedSlug);
            }

            if (noResults) {
                if (matchCount === 0 && cleanQuery.length > 0) {
                    noResults.classList.remove('hidden');
                    panels.forEach(p => p.classList.add('hidden'));
                } else {
                    noResults.classList.add('hidden');
                }
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', function (e) {
                filterGuides(e.target.value);
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                if (searchInput) {
                    searchInput.value = '';
                    filterGuides('');
                    searchInput.focus();
                }
            });
        }

        window.resetSearch = function () {
            if (searchInput) {
                searchInput.value = '';
                filterGuides('');
                searchInput.focus();
            }
            // Kembalikan ke game pertama
            const firstGameBtn = document.querySelector('[data-game-slug]');
            if (firstGameBtn) {
                selectGameTab(firstGameBtn.getAttribute('data-game-slug'));
            }
        };

        // Salin contoh format dengan tooltip feedback
        window.copyGuideSample = function (text, btn) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function () {
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<span class="text-emerald-600 dark:text-emerald-400 font-bold">✓ Tersalin!</span>';
                    setTimeout(function () {
                        btn.innerHTML = originalText;
                    }, 2000);
                }).catch(function () {
                    fallbackCopy(text, btn);
                });
            } else {
                fallbackCopy(text, btn);
            }
        };

        function fallbackCopy(text, btn) {
            const temp = document.createElement('textarea');
            temp.value = text;
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);

            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="text-emerald-600 dark:text-emerald-400 font-bold">✓ Tersalin!</span>';
            setTimeout(function () {
                btn.innerHTML = originalText;
            }, 2000);
        }

        // Buka game dari hash URL jika ada (misal: /panduan#guide-valorant)
        if (window.location.hash) {
            const hash = window.location.hash.replace('#guide-', '').replace('#', '');
            if (hash === 'faq') {
                scrollToFaq();
            } else {
                const targetBtn = document.getElementById('tab-btn-' + hash);
                if (targetBtn) {
                    selectGameTab(hash);
                }
            }
        }
    });
</script>
@endsection
