@if($tab === 'games')
    <!-- ========================================== -->
    <!-- TAB 1: DAFTAR KATALOG BERDASARKAN GAME     -->
    <!-- ========================================== -->
    <div class="space-y-4">
        <div class="flex items-center justify-between text-xs text-[#212121]/60 dark:text-zinc-400">
            <span>Menampilkan <strong class="text-[#212121] dark:text-zinc-200">{{ $games->count() }}</strong> game aktif</span>
            <span>Mode: <strong class="text-[#6D0808] dark:text-rose-400">Koleksi Game</strong></span>
        </div>

        @if($games->isEmpty())
            <div class="p-12 text-center rounded-2xl bg-white dark:bg-[#141416] border border-[#212121]/10 dark:border-white/10 shadow-xs space-y-3">
                <x-icon name="alert-circle" class="w-10 h-10 text-[#212121]/30 dark:text-zinc-500 mx-auto" />
                <h3 class="text-sm font-bold text-[#212121] dark:text-zinc-200">Game Tidak Ditemukan</h3>
                <p class="text-xs text-[#212121]/60 dark:text-zinc-400 max-w-sm mx-auto">
                    Tidak ada game yang sesuai dengan kata kunci pencarian atau filter yang Anda pilih.
                </p>
                <button type="button" onclick="resetAllCatalogFilters()" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs cursor-pointer">
                    <x-icon name="refresh" class="w-3.5 h-3.5" />
                    <span>Lihat Semua Game</span>
                </button>
            </div>
        @else
            <!-- Grid Kartu Game (Responsive 2 cols on mobile, up to 5 cols on desktop) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-5">
                @foreach($games as $game)
                    @php
                        $thumb = $game->thumbnail ?? match($game->slug) {
                            'mobile-legends' => 'images/bg/aspirant.jpg',
                            'free-fire' => 'images/bg/freefire.jpg',
                            'valorant' => 'images/bg/valorant.jpg',
                            'call-of-duty-mobile' => 'images/bg/codm.jpg',
                            'arena-of-valor' => 'images/bg/aov.jpg',
                            default => 'images/bg/1.jpg',
                        };

                        $minPrice = $game->activeProducts->min('sell_price') ?? 10000;
                        
                        $pubTheme = match($game->slug) {
                            'mobile-legends' => ['bg' => 'bg-[#1064D3]', 'label' => 'Moonton', 'sub' => 'Southeast-Asia • Top Up'],
                            'free-fire' => ['bg' => 'bg-[#D32F2F]', 'label' => 'Garena', 'sub' => 'Indonesia • Top Up'],
                            'valorant' => ['bg' => 'bg-[#B91C1C]', 'label' => 'Riot Games', 'sub' => 'Indonesia • Points'],
                            'call-of-duty-mobile' => ['bg' => 'bg-[#1E293B]', 'label' => 'Activision', 'sub' => 'Southeast-Asia • CP'],
                            'arena-of-valor' => ['bg' => 'bg-[#D97706]', 'label' => 'Garena', 'sub' => 'Indonesia • Voucher'],
                            default => ['bg' => 'bg-[#1064D3]', 'label' => $game->publisher ?? 'Official', 'sub' => 'Indonesia • Top Up'],
                        };

                        $salesCount = match($game->slug) {
                            'mobile-legends' => '4.8k',
                            'free-fire' => '3.5k',
                            'valorant' => '2.1k',
                            'call-of-duty-mobile' => '1.2k',
                            'arena-of-valor' => '840',
                            default => '500',
                        };
                        $rating = match($game->slug) {
                            'mobile-legends' => '4.98',
                            'free-fire' => '4.96',
                            'valorant' => '4.97',
                            'call-of-duty-mobile' => '4.94',
                            'arena-of-valor' => '4.92',
                            default => '4.95',
                        };
                    @endphp

                    <a href="{{ route('order.game', $game->slug) }}" class="group flex flex-col rounded-2xl bg-white dark:bg-[#1C1C20] border border-stone-200/80 dark:border-white/10 hover:border-[#6D0808]/40 dark:hover:border-rose-500/50 hover:shadow-md transition-all duration-200 overflow-hidden text-left transform-gpu">
                        <!-- 1. POSTER IMAGE (Portrait Aspect Ratio) -->
                        <div class="relative w-full aspect-[4/5] overflow-hidden bg-stone-100 dark:bg-stone-900">
                            <img src="{{ asset($thumb) }}" 
                                 alt="{{ $game->name }}" 
                                 loading="lazy" 
                                 decoding="async"
                                 class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300 transform-gpu">
                            
                            <!-- PLATFORM / PUBLISHER BAR -->
                            <div class="absolute bottom-0 inset-x-0 {{ $pubTheme['bg'] }} text-white px-2 py-0.5 flex items-center space-x-1.5 text-[10px] font-bold shadow-xs">
                                <x-icon name="gamepad" class="w-3 h-3 text-white/90 shrink-0" />
                                <span class="truncate">{{ $pubTheme['label'] }}</span>
                            </div>

                            <!-- Category Badge di Kiri Atas -->
                            <div class="absolute top-2 left-2 px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider bg-black/60 text-white backdrop-blur-xs">
                                {{ $game->category }}
                            </div>
                        </div>

                        <!-- 2. CARD BODY -->
                        <div class="p-3 sm:p-3.5 flex-1 flex flex-col justify-between space-y-2 bg-white dark:bg-[#1C1C20]">
                            <div class="space-y-0.5">
                                <h3 class="text-xs sm:text-[13px] font-bold text-[#212121] dark:text-zinc-100 group-hover:text-[#6D0808] dark:group-hover:text-rose-400 transition leading-snug line-clamp-2 min-h-[2.1rem]">
                                    {{ $game->name }}
                                </h3>
                                <p class="text-[10px] sm:text-[11px] text-[#212121]/50 dark:text-zinc-400 font-medium truncate">
                                    {{ $pubTheme['sub'] }}
                                </p>
                            </div>

                            <!-- Price & Denominations Count -->
                            <div class="space-y-1 pt-1 border-t border-stone-100 dark:border-white/10">
                                <div class="flex items-center justify-between text-[10px] text-[#212121]/60 dark:text-zinc-400">
                                    <span>Mulai dari:</span>
                                    <span class="px-1.5 py-0.2 rounded bg-[#6D0808]/10 dark:bg-rose-950/40 text-[#6D0808] dark:text-rose-400 font-bold text-[9px]">
                                        {{ $game->active_products_count }} Paket
                                    </span>
                                </div>
                                <div class="text-[#D97706] dark:text-amber-400 font-mono font-black text-xs sm:text-[15px] tracking-tight leading-tight">
                                    Rp {{ number_format($minPrice, 0, ',', '.') }}
                                </div>
                            </div>

                            <!-- Footer: Sales & Star Rating -->
                            <div class="pt-1.5 border-t border-stone-100 dark:border-white/10 flex items-center justify-between text-[10px]">
                                <span class="text-[#212121]/50 dark:text-zinc-400 font-medium">
                                    {{ $salesCount }} terjual
                                </span>
                                <div class="flex items-center space-x-0.5 text-[#212121]/80 dark:text-zinc-300 font-bold">
                                    <x-icon name="star" class="w-3 h-3 text-amber-400" fill="currentColor" />
                                    <span class="font-mono text-[10px]">{{ $rating }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@else
    <!-- ============================================== -->
    <!-- TAB 2: DAFTAR SEMUA PAKET ITEM & VOUCHER       -->
    <!-- ============================================== -->
    <div class="space-y-4">
        <div class="flex items-center justify-between text-xs text-[#212121]/60 dark:text-zinc-400">
            <span>Menampilkan <strong class="text-[#212121] dark:text-zinc-200">{{ $products->count() }}</strong> produk item siap dipesan</span>
            <span>Mode: <strong class="text-[#6D0808] dark:text-rose-400">Semua Paket Item</strong></span>
        </div>

        @if($products->isEmpty())
            <div class="p-12 text-center rounded-2xl bg-white dark:bg-[#141416] border border-[#212121]/10 dark:border-white/10 shadow-xs space-y-3">
                <x-icon name="alert-circle" class="w-10 h-10 text-[#212121]/30 dark:text-zinc-500 mx-auto" />
                <h3 class="text-sm font-bold text-[#212121] dark:text-zinc-200">Produk Tidak Ditemukan</h3>
                <p class="text-xs text-[#212121]/60 dark:text-zinc-400 max-w-sm mx-auto">
                    Tidak ada item atau nominal produk yang sesuai dengan kriteria filter Anda.
                </p>
                <button type="button" onclick="resetAllCatalogFilters()" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs cursor-pointer">
                    <x-icon name="refresh" class="w-3.5 h-3.5" />
                    <span>Lihat Semua Produk</span>
                </button>
            </div>
        @else
            <!-- Grid Kartu Item Produk Individu -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                @foreach($products as $prod)
                    <div class="p-4 rounded-2xl bg-white dark:bg-[#18181B] border border-[#212121]/10 dark:border-white/10 hover:border-[#6D0808]/40 dark:hover:border-rose-500/50 hover:shadow-md transition-all duration-200 flex flex-col justify-between space-y-3">
                        
                        <!-- Header Item: Game Mini Badge -->
                        <div class="flex items-center justify-between pb-2.5 border-b border-[#212121]/5 dark:border-white/10">
                            <div class="flex items-center space-x-2.5 min-w-0">
                                <div class="w-8 h-10 rounded-md overflow-hidden bg-stone-100 dark:bg-zinc-800 shrink-0 border border-[#212121]/10 dark:border-white/10">
                                    <img src="{{ asset($prod->game->thumbnail ?? 'images/bg/1.jpg') }}" alt="{{ $prod->game->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-[#6D0808] dark:text-rose-400">
                                        {{ $prod->game->category }}
                                    </div>
                                    <div class="text-xs font-bold text-[#212121] dark:text-zinc-100 truncate mt-0.5">{{ $prod->game->name }}</div>
                                </div>
                            </div>
                            @if($prod->has_discount)
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold text-[#6D0808] dark:text-rose-400 bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 shrink-0">
                                    -{{ $prod->discount_percent }}%
                                </span>
                            @endif
                        </div>

                        <!-- Body Item: Denominasi & SKU -->
                        <div class="space-y-1">
                            <h4 class="text-xs sm:text-sm font-bold text-[#212121] dark:text-zinc-100 leading-snug">
                                {{ $prod->name }}
                            </h4>
                            <div class="flex items-center space-x-2 text-[10px] text-[#212121]/50 dark:text-zinc-400 font-mono">
                                <span>SKU: {{ $prod->digiflazz_sku }}</span>
                                <span>•</span>
                                <span>{{ $prod->category ?? 'Item' }}</span>
                            </div>
                        </div>

                        <!-- Footer Item: Harga & Tombol Beli Langsung -->
                        <div class="pt-2 border-t border-[#212121]/5 dark:border-white/10 flex items-center justify-between gap-2">
                            <div>
                                @if($prod->has_discount)
                                    <div class="flex items-center space-x-1.5 text-[10px] text-[#212121]/50 dark:text-zinc-400">
                                        <span>Normal:</span>
                                        <span class="line-through font-mono tabular-nums text-[#212121]/40 dark:text-zinc-500">Rp {{ number_format($prod->original_price, 0, ',', '.') }}</span>
                                    </div>
                                @else
                                    <div class="text-[9px] text-[#212121]/50 dark:text-zinc-400 font-medium">Harga Resmi:</div>
                                @endif
                                <div class="font-mono tabular-nums text-sm sm:text-base font-bold text-[#6D0808] dark:text-rose-400">
                                    Rp {{ number_format($prod->sell_price, 0, ',', '.') }}
                                </div>
                            </div>

                            @if(!isset($prod->is_stock_available) || $prod->is_stock_available)
                                <a href="{{ route('order.game', [$prod->game->slug, 'product_id' => $prod->id]) }}" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs shrink-0">
                                    <span>Beli</span>
                                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                                </a>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-bold text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 shadow-2xs shrink-0" title="Item sedang dalam pembaruan berkala">
                                    Pembaruan Stok
                                </span>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endif

