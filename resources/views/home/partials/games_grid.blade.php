@if($games->isEmpty())
    <div class="p-12 text-center rounded-xl bg-stone-50 dark:bg-zinc-900 border border-stone-200/60 dark:border-white/10 w-full col-span-full">
        <x-icon name="alert-circle" class="w-8 h-8 text-[#212121]/30 dark:text-zinc-500 mx-auto mb-3" />
        <h3 class="text-sm font-semibold text-[#212121] dark:text-zinc-200">Game tidak ditemukan</h3>
        <p class="text-xs text-[#212121]/60 dark:text-zinc-400 mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
        <button type="button" onclick="filterHomeCategory('Semua')" class="inline-block mt-4 text-xs font-semibold text-[#6D0808] dark:text-rose-400 underline cursor-pointer">Tampilkan Semua Game</button>
    </div>
@else
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
            
            $discountPercent = match($game->slug) {
                'mobile-legends' => 11,
                'free-fire' => 15,
                'valorant' => 10,
                'call-of-duty-mobile' => 12,
                'arena-of-valor' => 10,
                default => 10,
            };
            $strikethroughPrice = (int) ceil(($minPrice / (1 - ($discountPercent / 100))) / 100) * 100;

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

            $pubTheme = match($game->slug) {
                'mobile-legends' => ['bg' => 'bg-[#1064D3]', 'label' => 'Moonton', 'sub' => 'Southeast-Asia • Top Up'],
                'free-fire' => ['bg' => 'bg-[#D32F2F]', 'label' => 'Garena', 'sub' => 'Indonesia • Top Up'],
                'valorant' => ['bg' => 'bg-[#B91C1C]', 'label' => 'Riot Games', 'sub' => 'Indonesia • Points'],
                'call-of-duty-mobile' => ['bg' => 'bg-[#1E293B]', 'label' => 'Activision', 'sub' => 'Southeast-Asia • CP'],
                'arena-of-valor' => ['bg' => 'bg-[#D97706]', 'label' => 'Garena', 'sub' => 'Indonesia • Voucher'],
                default => ['bg' => 'bg-[#1064D3]', 'label' => $game->publisher ?? 'Official', 'sub' => 'Indonesia • Top Up'],
            };
        @endphp

        <a href="{{ route('order.game', $game->slug) }}" class="group flex flex-col rounded-xl bg-white dark:bg-[#1C1C20] border border-stone-200/80 dark:border-white/10 hover:border-[#6D0808]/40 dark:hover:border-rose-500/50 hover:shadow-md transition-all duration-200 overflow-hidden text-left transform-gpu">
            
            <!-- 1. POSTER IMAGE (Portrait Aspect Ratio) -->
            <div class="relative w-full aspect-[4/5] overflow-hidden bg-stone-100 dark:bg-stone-900">
                <img src="{{ asset($thumb) }}" 
                     alt="{{ $game->name }}" 
                     loading="lazy" 
                     decoding="async"
                     class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300 transform-gpu">
                
                <!-- 2. PLATFORM / PUBLISHER BAR -->
                <div class="absolute bottom-0 inset-x-0 {{ $pubTheme['bg'] }} text-white px-2 py-0.5 flex items-center space-x-1.5 text-[10px] font-bold shadow-xs">
                    <x-icon name="gamepad" class="w-3 h-3 text-white/90 shrink-0" />
                    <span class="truncate">{{ $pubTheme['label'] }}</span>
                </div>
            </div>

            <!-- 3. CARD BODY (Informasi Produk) -->
            <div class="p-2.5 sm:p-3 flex-1 flex flex-col justify-between space-y-2 bg-white dark:bg-[#1C1C20]">
                
                <div class="space-y-0.5">
                    <!-- Game Title -->
                    <h3 class="text-xs sm:text-[13px] font-bold text-[#212121] dark:text-zinc-100 group-hover:text-[#6D0808] dark:group-hover:text-rose-400 transition leading-snug line-clamp-2 min-h-[2.1rem]">
                        {{ $game->name }}
                    </h3>

                    <!-- Subtitle -->
                    <p class="text-[10px] sm:text-[11px] text-[#212121]/50 dark:text-zinc-400 font-medium truncate">
                        {{ $pubTheme['sub'] }}
                    </p>
                </div>

                <!-- 4. HARGA & DISKON -->
                <div class="space-y-0.5 pt-0.5">
                    <div class="flex items-center space-x-1.5 text-[10px]">
                        <span class="px-1 py-0.2 rounded bg-red-600 text-white font-bold text-[9px] leading-tight">
                            {{ $discountPercent }}%
                        </span>
                        <span class="text-[#212121]/40 dark:text-zinc-500 line-through font-mono text-[10px] sm:text-[11px]">
                            {{ number_format($strikethroughPrice, 0, '', '') }}.00
                        </span>
                    </div>

                    <div class="text-[#D97706] dark:text-amber-400 font-mono font-black text-xs sm:text-[15px] tracking-tight leading-tight">
                        {{ number_format($minPrice, 0, '', '') }}.00
                    </div>
                </div>

                <!-- 5. FOOTER: Terjual & Rating Bintang -->
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
@endif

