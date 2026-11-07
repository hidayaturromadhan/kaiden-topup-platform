@extends('layouts.app')

@section('title', 'Katalog Semua Produk & Top Up Game Online - KAIDEN')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 sm:pt-8 pb-16 space-y-8">

    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-xs text-[#212121]/60 dark:text-zinc-400">
        <a href="{{ route('home') }}" class="hover:text-[#6D0808] dark:hover:text-rose-400 transition">Beranda</a>
        <x-icon name="chevron-right" class="w-3 h-3" />
        <span class="text-[#212121] dark:text-zinc-200 font-semibold">Katalog Semua Produk</span>
    </div>

    <!-- Header Banner Katalog -->
    <div class="relative overflow-hidden rounded-2xl bg-[#212121] text-white p-6 sm:p-8 border border-white/10 shadow-sm">
        <!-- Background Accent Graphic -->
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-[#6D0808]/40 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-10 top-0 bottom-0 w-1/3 opacity-15 pointer-events-none overflow-hidden hidden sm:block">
            <img src="{{ asset('images/bg/aspirant.jpg') }}" alt="Gaming Background" class="w-full h-full object-cover object-center" loading="lazy">
        </div>

        <div class="relative z-10 max-w-2xl space-y-3">
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-[11px] font-bold text-amber-300 uppercase tracking-wider">
                <x-icon name="package" class="w-3.5 h-3.5 text-amber-400" />
                <span>Katalog Resmi &amp; Terverifikasi</span>
            </div>
            <h1 class="text-xl sm:text-3xl font-black tracking-tight text-white leading-tight">
                Katalog Produk &amp; Top Up Game
            </h1>
            <p class="text-xs sm:text-sm text-white/75 leading-relaxed">
                Pilih game favoritmu atau cari langsung item diamond, membership pass, voucher, dan points resmi dengan jaminan pengiriman instan &lt; 60 detik.
            </p>

            <!-- Mode Tampilan Switch (Games vs Products) -->
            <div class="pt-3 flex flex-wrap items-center gap-2" id="catalog-tab-switch">
                <button type="button" 
                        onclick="setCatalogTab('games')"
                        id="tab-btn-games"
                        class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs cursor-pointer {{ $tab === 'games' ? 'bg-[#6D0808] text-white' : 'bg-white/10 text-white/80 hover:bg-white/20' }}">
                    <x-icon name="gamepad" class="w-4 h-4" />
                    <span id="tab-label-games">Koleksi Game ({{ $totalGamesCount }})</span>
                </button>
                <button type="button" 
                        onclick="setCatalogTab('products')"
                        id="tab-btn-products"
                        class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs cursor-pointer {{ $tab === 'products' ? 'bg-[#6D0808] text-white' : 'bg-white/10 text-white/80 hover:bg-white/20' }}">
                    <x-icon name="package" class="w-4 h-4" />
                    <span id="tab-label-products">Semua Paket Item ({{ $totalProductsCount }})</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#141416] border border-[#212121]/10 dark:border-white/10 shadow-xs space-y-4">
        
        <!-- Baris 1: Search, Filter Publisher & Sorting -->
        <form id="catalog-filter-form" onsubmit="event.preventDefault(); triggerCatalogSearch();" class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <input type="hidden" name="tab" id="filter-input-tab" value="{{ $tab }}">
            <input type="hidden" name="category" id="filter-input-category" value="{{ $category ?? 'Semua' }}">

            <!-- Search Input with Live Debounce & Loading Spinner Indicator -->
            <div class="relative flex-1 max-w-lg">
                <input type="text" 
                       name="q" 
                       id="filter-input-search"
                       value="{{ $search }}" 
                       autocomplete="off"
                       placeholder="Cari nama game, item, atau SKU..." 
                       class="w-full pl-10 pr-24 py-2.5 rounded-xl border border-[#212121]/15 dark:border-white/15 bg-white dark:bg-zinc-800 text-xs sm:text-sm text-[#212121] dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 dark:focus:ring-rose-500/30 focus:border-[#6D0808] dark:focus:border-rose-500 transition placeholder:text-[#212121]/40 dark:placeholder:text-zinc-400 font-medium">
                
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#212121]/50 dark:text-zinc-400">
                    <x-icon name="search" class="w-4 h-4" id="search-static-icon" />
                    <span id="search-spinner-icon" class="hidden animate-spin w-4 h-4 border-2 border-[#6D0808] dark:border-rose-400 border-t-transparent rounded-full"></span>
                </div>

                <button type="submit" class="absolute inset-y-1 right-1 px-3.5 rounded-lg text-xs font-bold text-white bg-[#212121] hover:bg-black dark:bg-[#6D0808] dark:hover:bg-[#540606] transition flex items-center space-x-1 shadow-xs cursor-pointer">
                    <span>Cari</span>
                </button>
            </div>

            <!-- Controls Kanan: Publisher & Sort -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Filter Publisher -->
                <select name="publisher" 
                        id="filter-select-publisher"
                        onchange="triggerCatalogFilterChange()" 
                        class="px-3 py-2 rounded-xl border border-[#212121]/15 dark:border-white/15 bg-white dark:bg-zinc-800 text-xs text-[#212121] dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 cursor-pointer">
                    <option value="">Semua Publisher</option>
                    @foreach($publishers as $pub)
                        <option value="{{ $pub }}" {{ $publisher === $pub ? 'selected' : '' }}>{{ $pub }}</option>
                    @endforeach
                </select>

                <!-- Sorting Dropdown -->
                <select name="sort" 
                        id="filter-select-sort"
                        onchange="triggerCatalogFilterChange()" 
                        class="px-3 py-2 rounded-xl border border-[#212121]/15 dark:border-white/15 bg-white dark:bg-zinc-800 text-xs text-[#212121] dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 cursor-pointer">
                    <option value="populer" {{ $sort === 'populer' ? 'selected' : '' }}>Urutan: Terpopuler</option>
                    <option value="harga_asc" {{ $sort === 'harga_asc' ? 'selected' : '' }}>Harga: Termurah</option>
                    <option value="harga_desc" {{ $sort === 'harga_desc' ? 'selected' : '' }}>Harga: Tertinggi</option>
                    <option value="nama_asc" {{ $sort === 'nama_asc' ? 'selected' : '' }}>Nama: A - Z</option>
                    <option value="nama_desc" {{ $sort === 'nama_desc' ? 'selected' : '' }}>Nama: Z - A</option>
                </select>
            </div>
        </form>

        <!-- Baris 2: Category Filter Tabs -->
        <div class="pt-2 border-t border-[#212121]/5 dark:border-white/10 flex items-center justify-between gap-3">
            <div id="catalog-category-pills" class="flex items-center space-x-1.5 overflow-x-auto pb-1 sm:pb-0 -mx-4 px-4 sm:mx-0 sm:px-0 scrollbar-none">
                <button type="button" 
                        onclick="setCatalogCategory('Semua')"
                        data-category="Semua"
                        class="catalog-cat-pill px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition cursor-pointer {{ (!$category || $category === 'Semua') ? 'bg-[#6D0808] text-white shadow-xs' : 'bg-[#F9F8F6] text-[#212121]/70 hover:text-[#212121] border border-[#212121]/10 dark:bg-zinc-800 dark:text-zinc-300 dark:border-white/10 dark:hover:text-white' }}">
                    Semua Kategori
                </button>
                @foreach($categories as $cat)
                    <button type="button" 
                            onclick="setCatalogCategory('{{ $cat }}')"
                            data-category="{{ $cat }}"
                            class="catalog-cat-pill px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition cursor-pointer {{ $category === $cat ? 'bg-[#6D0808] text-white shadow-xs' : 'bg-[#F9F8F6] text-[#212121]/70 hover:text-[#212121] border border-[#212121]/10 dark:bg-zinc-800 dark:text-zinc-300 dark:border-white/10 dark:hover:text-white' }}">
                        {{ $cat }}
                    </button>
                @endforeach
            </div>

            <button type="button" 
                    id="catalog-reset-btn"
                    onclick="resetAllCatalogFilters()" 
                    class="{{ ($search || ($category && $category !== 'Semua') || ($publisher && $publisher !== 'Semua') || ($sort && $sort !== 'populer')) ? 'inline-block' : 'hidden' }} text-xs font-semibold text-[#6D0808] dark:text-rose-400 hover:underline whitespace-nowrap shrink-0 cursor-pointer">
                Reset Filter
            </button>
        </div>

    </div>

    <!-- ======================================================== -->
    <!-- SKELETON WEB RENDER CONTAINER (Smooth Dynamic Loading)   -->
    <!-- ======================================================== -->
    <div id="catalog-skeleton" class="hidden space-y-4 animate-fade-in" aria-hidden="true">
        <!-- Skeleton Top Indicator Bar -->
        <div class="flex items-center justify-between text-xs py-1">
            <div class="h-4 w-44 bg-stone-200/80 dark:bg-zinc-800/80 rounded-md animate-pulse"></div>
            <div class="h-4 w-28 bg-stone-200/80 dark:bg-zinc-800/80 rounded-md animate-pulse"></div>
        </div>

        <!-- Skeleton Grid: Mode Games (10 Card Skeletons) -->
        <div id="skeleton-games-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-5">
            @for($i = 0; $i < 10; $i++)
                <div class="flex flex-col rounded-2xl bg-white dark:bg-[#1C1C20] border border-stone-200/80 dark:border-white/10 overflow-hidden shadow-xs animate-pulse">
                    <!-- Skeleton Poster -->
                    <div class="relative w-full aspect-[4/5] bg-stone-200/90 dark:bg-zinc-800/90 overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-t from-stone-300/60 dark:from-zinc-700/60 to-transparent"></div>
                        <div class="absolute top-2 left-2 w-12 h-3.5 rounded bg-stone-300/80 dark:bg-zinc-700/80"></div>
                        <div class="absolute bottom-0 inset-x-0 h-5 bg-stone-300 dark:bg-zinc-700"></div>
                    </div>
                    <!-- Skeleton Body -->
                    <div class="p-3 sm:p-3.5 flex-1 flex flex-col justify-between space-y-3">
                        <div class="space-y-1.5">
                            <div class="h-3.5 bg-stone-200 dark:bg-zinc-700/80 rounded-md w-4/5"></div>
                            <div class="h-3 bg-stone-100 dark:bg-zinc-800 rounded-md w-1/2"></div>
                        </div>
                        <div class="pt-2 border-t border-stone-100 dark:border-white/5 space-y-1.5">
                            <div class="flex justify-between items-center">
                                <div class="h-2.5 bg-stone-200 dark:bg-zinc-800 rounded w-12"></div>
                                <div class="h-3 bg-rose-100 dark:bg-rose-950/40 rounded w-14"></div>
                            </div>
                            <div class="h-4 bg-amber-200/80 dark:bg-amber-950/50 rounded-md w-24"></div>
                        </div>
                        <div class="pt-1.5 border-t border-stone-100 dark:border-white/5 flex items-center justify-between">
                            <div class="h-2.5 bg-stone-200 dark:bg-zinc-800 rounded w-16"></div>
                            <div class="h-2.5 bg-stone-200 dark:bg-zinc-800 rounded w-10"></div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        <!-- Skeleton Grid: Mode Products (8 Card Skeletons) -->
        <div id="skeleton-products-grid" class="hidden grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @for($i = 0; $i < 8; $i++)
                <div class="p-4 rounded-2xl bg-white dark:bg-[#18181B] border border-stone-200/80 dark:border-white/10 space-y-3.5 shadow-xs animate-pulse">
                    <!-- Skeleton Product Header -->
                    <div class="flex items-center space-x-2.5 pb-2.5 border-b border-stone-100 dark:border-white/10">
                        <div class="w-8 h-10 rounded-md bg-stone-200 dark:bg-zinc-800 shrink-0"></div>
                        <div class="min-w-0 flex-1 space-y-1.5">
                            <div class="h-2.5 bg-rose-100 dark:bg-rose-950/50 rounded w-16"></div>
                            <div class="h-3 bg-stone-200 dark:bg-zinc-700 rounded w-28"></div>
                        </div>
                    </div>
                    <!-- Skeleton Product Denom & SKU -->
                    <div class="space-y-1.5">
                        <div class="h-4 bg-stone-200 dark:bg-zinc-700/80 rounded w-3/4"></div>
                        <div class="h-3 bg-stone-100 dark:bg-zinc-800 rounded w-1/3"></div>
                    </div>
                    <!-- Skeleton Product Footer -->
                    <div class="pt-2 border-t border-stone-100 dark:border-white/10 flex items-center justify-between gap-2">
                        <div class="space-y-1">
                            <div class="h-2.5 bg-stone-200 dark:bg-zinc-800 rounded w-14"></div>
                            <div class="h-5 bg-rose-200/80 dark:bg-rose-950/60 rounded w-24"></div>
                        </div>
                        <div class="h-8 w-16 bg-stone-200 dark:bg-zinc-700 rounded-xl"></div>
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MAIN CONTENT AREA: RENDER PRODUK & GAME                  -->
    <!-- ======================================================== -->
    <div id="catalog-content-area" class="transition-opacity duration-200">
        @include('catalog.partials.content')
    </div>

    <!-- Trust Indicator Bar Sesuai RULES.md -->
    <div class="p-6 rounded-2xl bg-white dark:bg-[#141416] border border-[#212121]/10 dark:border-white/10 shadow-xs grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
        <div class="space-y-1.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center justify-center mx-auto">
                <x-icon name="shield-check" class="w-5 h-5" />
            </div>
            <h4 class="text-xs font-bold text-[#212121] dark:text-zinc-100">100% Legal &amp; Resmi</h4>
            <p class="text-[11px] text-[#212121]/60 dark:text-zinc-400">Semua voucher bersumber langsung dari distributor resmi tanpa risiko minus diamond.</p>
        </div>

        <div class="space-y-1.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 flex items-center justify-center mx-auto">
                <x-icon name="zap" class="w-5 h-5" />
            </div>
            <h4 class="text-xs font-bold text-[#212121] dark:text-zinc-100">Pengiriman Kilat &lt; 60 Detik</h4>
            <p class="text-[11px] text-[#212121]/60 dark:text-zinc-400">Sistem otomatis langsung memproses item game setelah pembayaran Midtrans terverifikasi.</p>
        </div>

        <div class="space-y-1.5">
            <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950/50 text-sky-700 dark:text-sky-400 flex items-center justify-center mx-auto">
                <x-icon name="headphones" class="w-5 h-5" />
            </div>
            <h4 class="text-xs font-bold text-[#212121] dark:text-zinc-100">Layanan CS WhatsApp 24/7</h4>
            <p class="text-[11px] text-[#212121]/60 dark:text-zinc-400">Tim Customer Care siap membantu pengecekan transaksi dan panduan top up setiap saat.</p>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function() {
    // State lokal katalog
    let catalogState = {
        tab: '{{ $tab }}',
        q: '{{ $search }}',
        category: '{{ $category ?? "Semua" }}',
        publisher: '{{ $publisher }}',
        sort: '{{ $sort ?? "populer" }}'
    };

    let searchDebounceTimer = null;
    let abortController = null;

    // Elemen DOM
    const contentArea = document.getElementById('catalog-content-area');
    const skeletonArea = document.getElementById('catalog-skeleton');
    const skeletonGames = document.getElementById('skeleton-games-grid');
    const skeletonProducts = document.getElementById('skeleton-products-grid');

    const searchInput = document.getElementById('filter-input-search');
    const publisherSelect = document.getElementById('filter-select-publisher');
    const sortSelect = document.getElementById('filter-select-sort');
    const tabInput = document.getElementById('filter-input-tab');
    const categoryInput = document.getElementById('filter-input-category');
    const resetBtn = document.getElementById('catalog-reset-btn');

    const staticSearchIcon = document.getElementById('search-static-icon');
    const spinnerSearchIcon = document.getElementById('search-spinner-icon');

    // Menampilkan Skeleton Sesuai Tab Aktif
    function showSkeleton(isSearching = false) {
        if (!contentArea || !skeletonArea) return;

        if (catalogState.tab === 'products') {
            skeletonProducts.classList.remove('hidden');
            skeletonGames.classList.add('hidden');
        } else {
            skeletonGames.classList.remove('hidden');
            skeletonProducts.classList.add('hidden');
        }

        skeletonArea.classList.remove('hidden');
        contentArea.classList.add('hidden');

        if (isSearching && staticSearchIcon && spinnerSearchIcon) {
            staticSearchIcon.classList.add('hidden');
            spinnerSearchIcon.classList.remove('hidden');
        }
    }

    // Menyembunyikan Skeleton
    function hideSkeleton() {
        if (!contentArea || !skeletonArea) return;

        skeletonArea.classList.add('hidden');
        contentArea.classList.remove('hidden');

        if (staticSearchIcon && spinnerSearchIcon) {
            staticSearchIcon.classList.remove('hidden');
            spinnerSearchIcon.classList.add('hidden');
        }
    }

    // Mengambil Data Katalog secara Asynchronous (Tanpa Reload Halaman)
    function fetchCatalogData(isSearching = false) {
        if (abortController) {
            abortController.abort();
        }
        abortController = new AbortController();

        showSkeleton(isSearching);

        // Bangun Query String URL
        const params = new URLSearchParams();
        if (catalogState.tab) params.set('tab', catalogState.tab);
        if (catalogState.q && catalogState.q.trim() !== '') params.set('q', catalogState.q.trim());
        if (catalogState.category && catalogState.category !== 'Semua') params.set('category', catalogState.category);
        if (catalogState.publisher && catalogState.publisher !== 'Semua' && catalogState.publisher !== '') params.set('publisher', catalogState.publisher);
        if (catalogState.sort && catalogState.sort !== 'populer') params.set('sort', catalogState.sort);

        const newUrl = '{{ route("catalog.index") }}' + (params.toString() ? '?' + params.toString() : '');

        // Update URL di address bar tanpa reload
        window.history.pushState(catalogState, '', newUrl);

        // Fetch Data dengan Header AJAX
        fetch(newUrl, {
            signal: abortController.signal,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response error');
            return response.json();
        })
        .then(data => {
            if (data && data.html) {
                contentArea.innerHTML = data.html;
            }

            // Update Label Total
            if (data.total_games_count !== undefined) {
                const labelGames = document.getElementById('tab-label-games');
                if (labelGames) labelGames.textContent = `Koleksi Game (${data.total_games_count})`;
            }
            if (data.total_products_count !== undefined) {
                const labelProducts = document.getElementById('tab-label-products');
                if (labelProducts) labelProducts.textContent = `Semua Paket Item (${data.total_products_count})`;
            }

            updateUIControls();
            hideSkeleton();
        })
        .catch(err => {
            if (err.name === 'AbortError') return;
            console.error('Fetch error:', err);
            hideSkeleton();
        });
    }

    // Sinkronisasi Tampilan Tombol & Tab
    function updateUIControls() {
        // 1. Tab Switcher
        const btnGames = document.getElementById('tab-btn-games');
        const btnProducts = document.getElementById('tab-btn-products');
        if (btnGames && btnProducts) {
            if (catalogState.tab === 'products') {
                btnProducts.className = "inline-flex items-center space-x-2 px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs cursor-pointer bg-[#6D0808] text-white";
                btnGames.className = "inline-flex items-center space-x-2 px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs cursor-pointer bg-white/10 text-white/80 hover:bg-white/20";
            } else {
                btnGames.className = "inline-flex items-center space-x-2 px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs cursor-pointer bg-[#6D0808] text-white";
                btnProducts.className = "inline-flex items-center space-x-2 px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs cursor-pointer bg-white/10 text-white/80 hover:bg-white/20";
            }
        }

        // 2. Category Pills
        document.querySelectorAll('.catalog-cat-pill').forEach(pill => {
            const cat = pill.getAttribute('data-category');
            const isActive = (cat === catalogState.category) || (!catalogState.category && cat === 'Semua');
            if (isActive) {
                pill.className = "catalog-cat-pill px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition cursor-pointer bg-[#6D0808] text-white shadow-xs";
            } else {
                pill.className = "catalog-cat-pill px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition cursor-pointer bg-[#F9F8F6] text-[#212121]/70 hover:text-[#212121] border border-[#212121]/10 dark:bg-zinc-800 dark:text-zinc-300 dark:border-white/10 dark:hover:text-white";
            }
        });

        // 3. Reset Button Visibility
        const hasFilters = (catalogState.q && catalogState.q.trim() !== '') || 
                            (catalogState.category && catalogState.category !== 'Semua') || 
                            (catalogState.publisher && catalogState.publisher !== 'Semua' && catalogState.publisher !== '') || 
                            (catalogState.sort && catalogState.sort !== 'populer');
        if (resetBtn) {
            resetBtn.classList.toggle('hidden', !hasFilters);
            resetBtn.classList.toggle('inline-block', hasFilters);
        }
    }

    // Fungsi Global yang Dapat Dipanggil dari Inline Event
    window.setCatalogTab = function(tabName) {
        if (catalogState.tab === tabName) return;
        catalogState.tab = tabName;
        if (tabInput) tabInput.value = tabName;
        fetchCatalogData();
    };

    window.setCatalogCategory = function(catName) {
        if (catalogState.category === catName) return;
        catalogState.category = catName;
        if (categoryInput) categoryInput.value = catName;
        fetchCatalogData();
    };

    window.triggerCatalogFilterChange = function() {
        catalogState.publisher = publisherSelect ? publisherSelect.value : '';
        catalogState.sort = sortSelect ? sortSelect.value : 'populer';
        fetchCatalogData();
    };

    window.triggerCatalogSearch = function() {
        if (searchDebounceTimer) clearTimeout(searchDebounceTimer);
        catalogState.q = searchInput ? searchInput.value : '';
        fetchCatalogData(true);
    };

    window.resetAllCatalogFilters = function() {
        catalogState.q = '';
        catalogState.category = 'Semua';
        catalogState.publisher = '';
        catalogState.sort = 'populer';

        if (searchInput) searchInput.value = '';
        if (publisherSelect) publisherSelect.value = '';
        if (sortSelect) sortSelect.value = 'populer';
        if (categoryInput) categoryInput.value = 'Semua';

        fetchCatalogData();
    };

    // Live search input dengan debounce 320ms (Pencarian real-time tanpa tekan enter)
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            if (searchDebounceTimer) clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(() => {
                catalogState.q = searchInput.value;
                fetchCatalogData(true);
            }, 320);
        });
    }

    // Support navigasi tombol Back / Forward di browser (Popstate)
    window.addEventListener('popstate', function(e) {
        if (e.state) {
            catalogState = Object.assign({}, e.state);
        } else {
            const urlParams = new URLSearchParams(window.location.search);
            catalogState.tab = urlParams.get('tab') || 'games';
            catalogState.q = urlParams.get('q') || '';
            catalogState.category = urlParams.get('category') || 'Semua';
            catalogState.publisher = urlParams.get('publisher') || '';
            catalogState.sort = urlParams.get('sort') || 'populer';
        }

        if (searchInput) searchInput.value = catalogState.q;
        if (publisherSelect) publisherSelect.value = catalogState.publisher;
        if (sortSelect) sortSelect.value = catalogState.sort;
        if (categoryInput) categoryInput.value = catalogState.category;
        if (tabInput) tabInput.value = catalogState.tab;

        fetchCatalogData();
    });
})();
</script>
@endpush
