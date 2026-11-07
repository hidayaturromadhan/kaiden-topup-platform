@extends('layouts.app')

@section('title', 'KAIDEN - Platform Top Up Game Kilat, Termurah & 100% Legal')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 sm:pt-6 pb-12 space-y-8 sm:space-y-12">

    <!-- HERO SECTION: Background images/bg/dark.jpg Sesuai Permintaan User -->
    <div class="relative overflow-hidden rounded-2xl border border-[#212121]/15 shadow-md text-[#F9F8F6] bg-[#181818]" style="background-image: url('{{ asset('images/bg/dark.jpg') }}'); background-size: cover; background-position: center;">
        
        <!-- Dark Gradient Overlay Cepat & Ringan pada dark.jpg (Tanpa Blur) -->
        <div class="absolute inset-0 bg-gradient-to-b from-[#181818]/90 via-[#181818]/70 to-[#181818]/95"></div>

        <div class="relative z-10 p-4 sm:p-7 md:p-9 space-y-5 sm:space-y-7">

            <!-- Bagian Atas Hero: Search Bar & Tag Populer seperti Itemku -->
            <div class="max-w-2xl mx-auto text-center space-y-3">
                <form action="{{ route('home') }}#katalog-game" method="GET" class="relative">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari game, diamond, voucher..." class="w-full pl-10 sm:pl-11 pr-20 sm:pr-24 py-2.5 sm:py-3 rounded-2xl bg-white/95 text-[#212121] text-xs sm:text-sm shadow-md focus:outline-none focus:ring-2 focus:ring-[#6D0808] placeholder:text-[#212121]/40 font-medium transition">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#212121]/50">
                        <x-icon name="search" class="w-4 h-4" />
                    </div>
                    <button type="submit" class="absolute inset-y-1.5 right-1.5 px-3 sm:px-4 rounded-xl text-xs font-bold text-white bg-[#6D0808] hover:bg-[#540606] transition flex items-center space-x-1 shadow-xs">
                        <span>Cari</span>
                    </button>
                </form>

                <!-- Tag Shortcut Populer (Sesuai Layout Itemku) -->
                <div class="flex flex-wrap items-center justify-center gap-1.5 text-[10px] sm:text-[11px]">
                    <span class="text-white/60">Populer:</span>
                    <a href="{{ route('home', ['q' => 'Mobile Legends']) }}#katalog-game" class="px-2.5 py-0.5 rounded-full bg-white/10 hover:bg-white/20 text-white/90 border border-white/15 transition">Mobile Legends</a>
                    <a href="{{ route('order.game', 'mobile-legends') }}" class="px-2.5 py-0.5 rounded-full bg-white/10 hover:bg-white/20 text-white/90 border border-white/15 transition">Weekly Diamond Pass</a>
                    <a href="{{ route('home', ['q' => 'Free Fire']) }}#katalog-game" class="px-2.5 py-0.5 rounded-full bg-white/10 hover:bg-white/20 text-white/90 border border-white/15 transition">Free Fire</a>
                    <a href="{{ route('home', ['q' => 'Valorant']) }}#katalog-game" class="px-2.5 py-0.5 rounded-full bg-white/10 hover:bg-white/20 text-white/90 border border-white/15 transition">Valorant Points</a>
                    <a href="{{ route('home', ['q' => 'Call of Duty']) }}#katalog-game" class="px-2.5 py-0.5 rounded-full bg-white/10 hover:bg-white/20 text-white/90 border border-white/15 transition">CODM CP</a>
                    <a href="{{ route('home', ['q' => 'Arena of Valor']) }}#katalog-game" class="px-2.5 py-0.5 rounded-full bg-white/10 hover:bg-white/20 text-white/90 border border-white/15 transition">AOV Vouchers</a>
                </div>
            </div>

            <!-- Bagian Tengah Hero: 4 Kartu Promo & Event Unggulan (Persis Layout Itemku - GPU Optimized) -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-5">
                
                <!-- Kartu 1: Mobile Legends (The Aspirants) -->
                <div class="group relative rounded-xl overflow-hidden border border-white/15 hover:border-white/35 shadow-sm transition-transform duration-200 ease-out hover:-translate-y-1 will-change-transform flex flex-col justify-end min-h-[260px] sm:min-h-[330px] p-3 sm:p-4 text-center transform-gpu">
                    <!-- Poster Background -->
                    <img src="{{ asset('images/bg/aspirant.jpg') }}" alt="Mobile Legends The Aspirants 5.0" width="736" height="736" loading="eager" fetchpriority="high" class="absolute inset-0 w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300 transform-gpu">
                    
                    <!-- Gradient Overlay (Fade to Dark at Bottom persis Itemku) -->
                    <div class="absolute inset-0 bg-gradient-to-t from-[#141414] via-[#141414]/75 via-45% to-transparent pointer-events-none"></div>
                    <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-black/40 to-transparent pointer-events-none"></div>

                    <!-- Content Layer di Bagian Bawah -->
                    <div class="relative z-10 flex flex-col items-center space-y-1.5 sm:space-y-2">
                        <div class="space-y-0.5">
                            <div class="font-black text-[11px] sm:text-sm text-white uppercase tracking-tight drop-shadow-md">KUOTA TERBATAS</div>
                            <p class="text-[9px] sm:text-[11px] text-white/80 font-medium drop-shadow-xs">Diskon s.d 15% Pass &amp; Diamond</p>
                        </div>

                        <div class="py-0.5">
                            <span class="inline-block px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-md text-[9px] sm:text-[11px] font-black uppercase tracking-wider bg-amber-400 text-stone-950 shadow-md">
                                Special Event
                            </span>
                        </div>

                        <a href="{{ route('order.game', 'mobile-legends') }}" class="w-full py-1.5 sm:py-2 px-2.5 sm:px-3 rounded-lg text-[11px] sm:text-xs font-bold text-white bg-white/15 hover:bg-[#6D0808] border border-white/25 hover:border-[#6D0808] transition duration-150 flex items-center justify-center space-x-1.5 shadow-xs">
                            <span>Top Up</span>
                            <x-icon name="chevron-right" class="w-3.5 h-3.5 opacity-80" />
                        </a>
                    </div>
                </div>

                <!-- Kartu 2: Free Fire (Booyah Deals) -->
                <div class="group relative rounded-xl overflow-hidden border border-white/15 hover:border-white/35 shadow-sm transition-transform duration-200 ease-out hover:-translate-y-1 will-change-transform flex flex-col justify-end min-h-[260px] sm:min-h-[330px] p-3 sm:p-4 text-center transform-gpu">
                    <!-- Poster Background -->
                    <img src="{{ asset('images/bg/freefire.jpg') }}" alt="Free Fire Booyah Pass" width="736" height="736" loading="eager" fetchpriority="high" class="absolute inset-0 w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300 transform-gpu">
                    
                    <!-- Gradient Overlay (Fade to Dark at Bottom persis Itemku) -->
                    <div class="absolute inset-0 bg-gradient-to-t from-[#141414] via-[#141414]/75 via-45% to-transparent pointer-events-none"></div>
                    <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-black/40 to-transparent pointer-events-none"></div>

                    <!-- Content Layer di Bagian Bawah -->
                    <div class="relative z-10 flex flex-col items-center space-y-1.5 sm:space-y-2">
                        <div class="space-y-0.5">
                            <div class="font-black text-[11px] sm:text-sm text-white uppercase tracking-tight drop-shadow-md">PALING LARIS</div>
                            <p class="text-[9px] sm:text-[11px] text-white/80 font-medium drop-shadow-xs">Bonus Diamond &amp; Kirim Kilat</p>
                        </div>

                        <div class="py-0.5">
                            <span class="inline-block px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-md text-[9px] sm:text-[11px] font-black uppercase tracking-wider bg-amber-400 text-stone-950 shadow-md">
                                Hot Game
                            </span>
                        </div>

                        <a href="{{ route('order.game', 'free-fire') }}" class="w-full py-1.5 sm:py-2 px-2.5 sm:px-3 rounded-lg text-[11px] sm:text-xs font-bold text-white bg-white/15 hover:bg-[#6D0808] border border-white/25 hover:border-[#6D0808] transition duration-150 flex items-center justify-center space-x-1.5 shadow-xs">
                            <span>Top Up</span>
                            <x-icon name="chevron-right" class="w-3.5 h-3.5 opacity-80" />
                        </a>
                    </div>
                </div>

                <!-- Kartu 3: Valorant (Points Resmi) -->
                <div class="group relative rounded-xl overflow-hidden border border-white/15 hover:border-white/35 shadow-sm transition-transform duration-200 ease-out hover:-translate-y-1 will-change-transform flex flex-col justify-end min-h-[260px] sm:min-h-[330px] p-3 sm:p-4 text-center transform-gpu">
                    <!-- Poster Background -->
                    <img src="{{ asset('images/bg/valorant.jpg') }}" alt="Valorant Riot Points" width="736" height="414" loading="eager" fetchpriority="high" class="absolute inset-0 w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300 transform-gpu">
                    
                    <!-- Gradient Overlay (Fade to Dark at Bottom persis Itemku) -->
                    <div class="absolute inset-0 bg-gradient-to-t from-[#141414] via-[#141414]/75 via-45% to-transparent pointer-events-none"></div>
                    <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-black/40 to-transparent pointer-events-none"></div>

                    <!-- Content Layer di Bagian Bawah -->
                    <div class="relative z-10 flex flex-col items-center space-y-1.5 sm:space-y-2">
                        <div class="space-y-0.5">
                            <div class="font-black text-[11px] sm:text-sm text-white uppercase tracking-tight drop-shadow-md">HARGA TERMURAH</div>
                            <p class="text-[9px] sm:text-[11px] text-white/80 font-medium drop-shadow-xs">Tanpa Biaya Admin QRIS</p>
                        </div>

                        <div class="py-0.5">
                            <span class="inline-block px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-md text-[9px] sm:text-[11px] font-black uppercase tracking-wider bg-amber-400 text-stone-950 shadow-md">
                                New Release
                            </span>
                        </div>

                        <a href="{{ route('order.game', 'valorant') }}" class="w-full py-1.5 sm:py-2 px-2.5 sm:px-3 rounded-lg text-[11px] sm:text-xs font-bold text-white bg-white/15 hover:bg-[#6D0808] border border-white/25 hover:border-[#6D0808] transition duration-150 flex items-center justify-center space-x-1.5 shadow-xs">
                            <span>Beli Sekarang</span>
                            <x-icon name="chevron-right" class="w-3.5 h-3.5 opacity-80" />
                        </a>
                    </div>
                </div>

                <!-- Kartu 4: Call of Duty Mobile (CP Points) -->
                <div class="group relative rounded-xl overflow-hidden border border-white/15 hover:border-white/35 shadow-sm transition-transform duration-200 ease-out hover:-translate-y-1 will-change-transform flex flex-col justify-end min-h-[260px] sm:min-h-[330px] p-3 sm:p-4 text-center transform-gpu">
                    <!-- Poster Background -->
                    <img src="{{ asset('images/bg/codm.jpg') }}" alt="Call of Duty Mobile CP" width="736" height="414" loading="eager" fetchpriority="high" class="absolute inset-0 w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300 transform-gpu">
                    
                    <!-- Gradient Overlay (Fade to Dark at Bottom persis Itemku) -->
                    <div class="absolute inset-0 bg-gradient-to-t from-[#141414] via-[#141414]/75 via-45% to-transparent pointer-events-none"></div>
                    <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-black/40 to-transparent pointer-events-none"></div>

                    <!-- Content Layer di Bagian Bawah -->
                    <div class="relative z-10 flex flex-col items-center space-y-1.5 sm:space-y-2">
                        <div class="space-y-0.5">
                            <div class="font-black text-[11px] sm:text-sm text-white uppercase tracking-tight drop-shadow-md">RESMI 100%</div>
                            <p class="text-[9px] sm:text-[11px] text-white/80 font-medium drop-shadow-xs">Pengiriman Kilat &lt; 60 Detik</p>
                        </div>

                        <div class="py-0.5">
                            <span class="inline-block px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-md text-[9px] sm:text-[11px] font-black uppercase tracking-wider bg-amber-400 text-stone-950 shadow-md">
                                Top Up
                            </span>
                        </div>

                        <a href="{{ route('order.game', 'call-of-duty-mobile') }}" class="w-full py-1.5 sm:py-2 px-2.5 sm:px-3 rounded-lg text-[11px] sm:text-xs font-bold text-white bg-white/15 hover:bg-[#6D0808] border border-white/25 hover:border-[#6D0808] transition duration-150 flex items-center justify-center space-x-1.5 shadow-xs">
                            <span>Top Up</span>
                            <x-icon name="chevron-right" class="w-3.5 h-3.5 opacity-80" />
                        </a>
                    </div>
                </div>

            </div>

            <!-- Bagian Bawah Hero: Trust Indicator Bar (Persis seperti Itemku) -->
            <div class="pt-6 border-t border-white/10 flex flex-wrap items-center justify-center gap-6 sm:gap-12 text-xs font-semibold text-white/90">
                <div class="flex items-center space-x-2">
                    <x-icon name="shield-check" class="w-4 h-4 text-emerald-400 shrink-0" />
                    <span>Transaksi Aman &amp; 100% Legal</span>
                </div>
                <div class="flex items-center space-x-2">
                    <x-icon name="refresh" class="w-4 h-4 text-amber-400 shrink-0" />
                    <span>Garansi Uang Kembali</span>
                </div>
                <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number', env('WHATSAPP_NUMBER', '6281234567890')) }}?text={{ urlencode('Halo Admin KAIDEN, saya butuh bantuan terkait layanan top up.') }}" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="flex items-center space-x-2 hover:text-white transition group cursor-pointer"
                   title="Hubungi CS via WhatsApp">
                    <x-icon name="headphones" class="w-4 h-4 text-emerald-400 shrink-0 group-hover:scale-110 transition-transform" />
                    <span class="group-hover:underline">Bantuan Customer Care 24/7</span>
                </a>
            </div>

        </div>
    </div>

    <!-- SECTION POSTER IKLAN & PROMO UNGGULAN (Dinamis dari Database & Tampilan Alami Bernilai Jual) -->
    <div id="promo-spesial" class="space-y-3 pt-2">
        
        <!-- Grid Banner: 1 Banner Besar Kiri (8 col) & 2 Banner Kecil Kanan (4 col) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-5 items-stretch">
            
            <!-- BANNER BESAR KIRI (lg:col-span-8) DENGAN CAROUSEL SLIDER DINAMIS -->
            <div class="lg:col-span-8 relative rounded-2xl overflow-hidden border border-[#212121]/15 shadow-sm bg-[#18181B] text-white min-h-[220px] sm:min-h-[260px] flex flex-col justify-between group">
                
                @forelse($sliderBanners as $banner)
                    <div class="promo-slide {{ $loop->first ? 'flex' : 'hidden' }} flex-1 flex flex-col justify-between p-6 sm:p-7 relative overflow-hidden transition-opacity duration-500 bg-gradient-to-r {{ $banner->gradient_class }}">
                        
                        <!-- Visual Poster Kanan: Alami, Tajam, Non-AI (Dengan Soft Vignette Mask) -->
                        @if($banner->image_url)
                            <div class="absolute right-0 top-0 bottom-0 w-3/5 sm:w-1/2 opacity-75 sm:opacity-85 pointer-events-none overflow-hidden">
                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" width="736" height="414" loading="lazy" class="w-full h-full object-cover object-center sm:object-right transition-transform duration-700 group-hover:scale-105">
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-r from-[#111827] via-[#111827]/90 via-50% to-transparent pointer-events-none"></div>

                        <!-- Slide Content -->
                        <div class="relative z-10 space-y-2 max-w-lg">
                            @if($banner->badge_text)
                                <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-white/15 text-white text-[10px] font-bold tracking-wide uppercase border border-white/20 backdrop-blur-xs">
                                    <x-icon name="tag" class="w-3 h-3 text-amber-300" />
                                    <span>{{ $banner->badge_text }}</span>
                                </div>
                            @endif

                            <h3 class="text-base sm:text-2xl font-black text-white tracking-tight leading-snug drop-shadow-xs">
                                {{ $banner->title }}
                            </h3>

                            @if($banner->discount_value)
                                <div class="flex items-baseline space-x-2 pt-1">
                                    @if($banner->discount_tag)
                                        <span class="text-xs font-bold uppercase tracking-wider text-amber-300">{{ $banner->discount_tag }}</span>
                                    @endif
                                    <span class="text-3xl sm:text-4xl font-black font-mono tracking-tight text-white drop-shadow-sm">{{ $banner->discount_value }}</span>
                                    @if($banner->discount_subtext)
                                        <span class="text-[11px] text-white/80 font-medium">{{ $banner->discount_subtext }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Footer Slide: Keterangan & Tombol Aksi -->
                        <div class="relative z-10 pt-4 flex flex-wrap items-center justify-between gap-3 text-xs border-t border-white/15">
                            <span class="text-[11px] text-white/70">{{ $banner->subtitle }}</span>
                            <a href="{{ $banner->button_url }}" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl text-xs font-bold text-[#6D0808] bg-white hover:bg-[#F9F8F6] transition shadow-sm hover:scale-[1.02]">
                                <span>{{ $banner->button_text }}</span>
                                <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                            </a>
                        </div>
                    </div>
                @empty
                    <!-- Fallback Slide jika belum ada data di database -->
                    <div class="promo-slide flex flex-1 flex flex-col justify-between p-6 sm:p-7 relative overflow-hidden bg-gradient-to-r from-[#6D0808] via-[#3B0707] to-[#18181B]">
                        <div class="relative z-10 space-y-2 max-w-lg">
                            <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-white/15 text-white text-[10px] font-bold tracking-wide uppercase border border-white/20">
                                <x-icon name="tag" class="w-3 h-3 text-amber-300" />
                                <span>Promo Weekend Gaming Murah Meriah</span>
                            </div>
                            <h3 class="text-base sm:text-2xl font-black text-white tracking-tight leading-snug">
                                Beli Diamond &amp; Voucher Pakai QRIS / E-Wallet
                            </h3>
                            <div class="flex items-baseline space-x-2 pt-1">
                                <span class="text-xs font-bold uppercase tracking-wider text-amber-300">DISKON!</span>
                                <span class="text-3xl sm:text-4xl font-black font-mono tracking-tight text-white">20 RB</span>
                                <span class="text-[11px] text-white/80 font-medium">Min. Trx 45rb</span>
                            </div>
                        </div>
                        <div class="relative z-10 pt-4 flex flex-wrap items-center justify-between gap-3 text-xs border-t border-white/15">
                            <span class="text-[11px] text-white/70">Setiap Jumat, Sabtu, Minggu • Otomatis dipotong saat checkout</span>
                            <a href="#katalog-game" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl text-xs font-bold text-[#6D0808] bg-white hover:bg-[#F9F8F6] transition shadow-sm">
                                <span>Klaim Promo</span>
                                <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                            </a>
                        </div>
                    </div>
                @endforelse

            </div>

            <!-- 2 BANNER PROMO KANAN (lg:col-span-4) DINAMIS DARI DATABASE -->
            <div class="lg:col-span-4 flex flex-col justify-between gap-4">
                
                @forelse($sideBanners as $side)
                    @php
                        $sideImg = $side->image ?? match($loop->index) {
                            0 => 'images/bg/aspirant.jpg',
                            default => 'images/banners/banner_freefire.jpg',
                        };
                    @endphp
                    <div class="relative rounded-2xl overflow-hidden border border-[#212121]/15 dark:border-white/10 shadow-sm p-4 sm:p-5 flex items-center justify-between min-h-[115px] bg-gradient-to-br {{ $side->gradient_class }} text-white group hover:border-white/30 transition duration-200">
                        <div class="space-y-1.5 max-w-[210px] relative z-10">
                            @if($side->badge_text)
                                <div class="text-[10px] font-bold tracking-wider text-amber-300 uppercase">
                                    {{ $side->badge_text }}
                                </div>
                            @endif
                            <h4 class="text-xs sm:text-sm font-extrabold text-white leading-tight">
                                {{ $side->title }}
                            </h4>
                            <p class="text-[11px] text-white/80 line-clamp-1">
                                {{ $side->subtitle }}
                            </p>
                            <div class="pt-1">
                                <a href="{{ $side->button_url }}" 
                                   @if(str_starts_with($side->button_url, 'http')) target="_blank" rel="noopener noreferrer" @endif
                                   class="inline-flex items-center px-3.5 py-1.5 rounded-lg text-xs font-bold text-[#212121] bg-white hover:bg-stone-100 transition shadow-xs">
                                    <span>{{ $side->button_text }}</span>
                                </a>
                            </div>
                        </div>
                        <div class="relative w-20 h-20 sm:w-22 sm:h-22 rounded-xl overflow-hidden shrink-0 shadow-md border border-white/20 bg-black/40">
                            <img src="{{ asset($sideImg) }}" alt="{{ $side->title }}" loading="lazy" decoding="async" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300">
                        </div>
                    </div>
                @empty
                    <!-- Fallback Side Banners jika belum ada data -->
                    <div class="relative rounded-2xl overflow-hidden border border-[#212121]/15 dark:border-white/10 shadow-sm p-4 sm:p-5 flex items-center justify-between min-h-[115px] bg-gradient-to-br from-[#212121] via-[#351016] to-[#6D0808] text-white group">
                        <div class="space-y-1.5 max-w-[210px] relative z-10">
                            <div class="text-[10px] font-bold tracking-wider text-amber-300 uppercase">PROMO SPESIAL</div>
                            <h4 class="text-xs sm:text-sm font-extrabold text-white leading-tight">Flash Promo Mobile Legends</h4>
                            <p class="text-[11px] text-white/80 line-clamp-1">Diskon s/d 15% + Bonus Diamond</p>
                            <div class="pt-1">
                                <a href="{{ route('order.game', 'mobile-legends') }}" class="inline-flex items-center px-3.5 py-1.5 rounded-lg text-xs font-bold text-[#212121] bg-white hover:bg-stone-100 transition shadow-xs">
                                    <span>Top Up MLBB</span>
                                </a>
                            </div>
                        </div>
                        <div class="relative w-20 h-20 sm:w-22 sm:h-22 rounded-xl overflow-hidden shrink-0 shadow-md border border-white/20 bg-black/40">
                            <img src="{{ asset('images/bg/aspirant.jpg') }}" alt="MLBB Promo" loading="lazy" decoding="async" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300">
                        </div>
                    </div>

                    <div class="relative rounded-2xl overflow-hidden border border-[#212121]/15 dark:border-white/10 shadow-sm p-4 sm:p-5 flex items-center justify-between min-h-[115px] bg-gradient-to-br from-[#3B2205] via-[#4A1806] to-[#212121] text-white group">
                        <div class="space-y-1.5 max-w-[210px] relative z-10">
                            <div class="text-[10px] font-bold tracking-wider text-amber-300 uppercase">FLASH SALE POPULER</div>
                            <h4 class="text-xs sm:text-sm font-extrabold text-white leading-tight">Super Deal Free Fire Diamond</h4>
                            <p class="text-[11px] text-white/80 line-clamp-1">Mulai Rp 2.869 Bebas Biaya Admin</p>
                            <div class="pt-1">
                                <a href="{{ route('order.game', 'free-fire') }}" class="inline-flex items-center px-3.5 py-1.5 rounded-lg text-xs font-bold text-[#212121] bg-white hover:bg-stone-100 transition shadow-xs">
                                    <span>Top Up Free Fire</span>
                                </a>
                            </div>
                        </div>
                        <div class="relative w-20 h-20 sm:w-22 sm:h-22 rounded-xl overflow-hidden shrink-0 shadow-md border border-white/20 bg-black/40">
                            <img src="{{ asset('images/banners/banner_freefire.jpg') }}" alt="Free Fire Promo" loading="lazy" decoding="async" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300">
                        </div>
                    </div>
                @endforelse

            </div>

        </div>

        <!-- Toolbar Bawah Banner: Titik Indikator Slider & Link "Lihat Semua >>" persis Itemku -->
        <div class="flex items-center justify-between pt-1">
            <!-- Indikator Dots Pagination Carousel Dinamis -->
            <div class="flex items-center space-x-1.5">
                @foreach($sliderBanners as $idx => $b)
                    <button type="button" onclick="goToSlide({{ $idx }})" class="promo-dot {{ $loop->first ? 'w-6 h-2 rounded-full bg-[#6D0808]' : 'w-2 h-2 rounded-full bg-[#212121]/20 hover:bg-[#212121]/40 dark:bg-white/30 dark:hover:bg-white/50' }} transition-all duration-300 cursor-pointer" title="Slide {{ $idx + 1 }}"></button>
                @endforeach
            </div>

            <!-- Link Lihat Semua Promo (Persis Itemku) -->
            <a href="{{ route('catalog.index') }}" class="text-xs font-bold text-[#6D0808] dark:text-rose-400 hover:text-[#540606] dark:hover:text-rose-300 transition flex items-center space-x-1 group">
                <span>Lihat Semua Katalog</span>
                <span class="tracking-tighter font-mono font-bold group-hover:translate-x-0.5 transition">&gt;&gt;</span>
            </a>
        </div>

    </div>

    <!-- FILTER & DAFTAR GAME KATALOG UTAMA (Persis Referensi Itemku) -->
    <div id="katalog-game" class="pt-2 space-y-4">
        
        <!-- Header Section (Icon Kunci/Game, Judul, Subjudul & Tombol Lihat Semua) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center shrink-0 shadow-2xs overflow-hidden p-1.5 dark:bg-amber-500/20 dark:border-amber-500/30">
                    <img src="{{ asset('images/icons/lighting.png') }}" alt="Petir Kilat" width="28" height="28" class="w-7 h-7 object-contain">
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-black tracking-tight text-[#212121] dark:text-[#F9F8F6]">
                        Top Up Game Resmi, Instan Klaim!
                    </h2>
                    <p class="text-xs text-[#212121]/60 dark:text-[#F9F8F6]/60">
                        Dari game mobile sampai PC, semua produk resmi &amp; legal ada di KAIDEN.
                    </p>
                </div>
            </div>

            <!-- Categories Tabs & Tombol Lihat Semua -->
            <div class="flex items-center space-x-2 overflow-x-auto pb-1 sm:pb-0">
                <div class="flex items-center space-x-1.5" id="home-category-buttons">
                    <button type="button" 
                            onclick="filterHomeCategory('Semua')" 
                            data-cat="Semua"
                            class="home-cat-btn px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition cursor-pointer {{ !$category || $category === 'Semua' ? 'bg-[#6D0808] text-white shadow-xs' : 'bg-white text-[#212121]/70 border border-[#212121]/10 hover:text-[#212121] dark:bg-[#18181B] dark:text-zinc-300 dark:border-white/10 dark:hover:text-white' }}">
                        Semua Game
                    </button>
                    @foreach($categories as $cat)
                        <button type="button" 
                                onclick="filterHomeCategory('{{ $cat }}')" 
                                data-cat="{{ $cat }}"
                                class="home-cat-btn px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition cursor-pointer {{ $category === $cat ? 'bg-[#6D0808] text-white shadow-xs' : 'bg-white text-[#212121]/70 border border-[#212121]/10 hover:text-[#212121] dark:bg-[#18181B] dark:text-zinc-300 dark:border-white/10 dark:hover:text-white' }}">
                            {{ $cat }}
                        </button>
                    @endforeach
                </div>

                <a href="{{ route('catalog.index') }}" class="hidden sm:inline-flex items-center space-x-1 px-3.5 py-1.5 rounded-xl border border-[#6D0808]/20 dark:border-rose-500/30 text-xs font-bold text-[#6D0808] dark:text-rose-400 hover:bg-[#6D0808]/5 dark:hover:bg-rose-500/10 transition shrink-0">
                    <span>Lihat Semua Katalog</span>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                </a>
            </div>
        </div>

        <!-- WHITE DECK CONTAINER (Persis Wadah Kartu Itemku dengan Skeleton Web Render) -->
        <div class="relative rounded-2xl border border-stone-200/90 dark:border-white/10 bg-white dark:bg-[#141416] p-3.5 sm:p-5 shadow-xs">
            
            <!-- Skeleton Web Render for Home Games (5 Cards Pulse Animation) -->
            <div id="home-games-skeleton" class="hidden grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4 overflow-hidden animate-pulse" aria-hidden="true">
                @for($i = 0; $i < 5; $i++)
                    <div class="flex flex-col rounded-xl bg-white dark:bg-[#1C1C20] border border-stone-200/80 dark:border-white/10 overflow-hidden shadow-xs">
                        <div class="relative w-full aspect-[4/5] bg-stone-200/90 dark:bg-zinc-800/90">
                            <div class="absolute bottom-0 inset-x-0 h-5 bg-stone-300 dark:bg-zinc-700"></div>
                        </div>
                        <div class="p-2.5 sm:p-3 space-y-2">
                            <div class="h-3.5 bg-stone-200 dark:bg-zinc-700 rounded w-4/5"></div>
                            <div class="h-3 bg-stone-100 dark:bg-zinc-800 rounded w-1/2"></div>
                            <div class="pt-1.5 space-y-1">
                                <div class="h-2.5 bg-stone-200 dark:bg-zinc-800 rounded w-1/3"></div>
                                <div class="h-4 bg-amber-200 dark:bg-amber-950/40 rounded w-20"></div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
            
            <div id="katalog-grid-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4 overflow-x-auto scroll-smooth">
                @include('home.partials.games_grid')
            </div>

            <!-- Circular Nav Arrow di Kanan (Persis Itemku) -->
            <button type="button" onclick="document.getElementById('katalog-grid-container').scrollBy({ left: 320, behavior: 'smooth' })" class="hidden lg:flex absolute -right-3.5 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white dark:bg-zinc-800 border border-stone-300 dark:border-zinc-700 shadow-md items-center justify-center text-[#212121] dark:text-zinc-200 hover:bg-stone-50 dark:hover:bg-zinc-700 z-20 cursor-pointer transition hover:scale-105" title="Geser Koleksi">
                <x-icon name="chevron-right" class="w-4 h-4" />
            </button>

        </div>
    </div>

    <!-- SECTION METODE PEMBAYARAN RESMI (Pengganti 2 Section Sesuai Permintaan User) -->
    <div id="metode-pembayaran" class="pt-8 border-t border-[#212121]/10 dark:border-white/10 space-y-6">
        
        <!-- Header Section: Bersih & Minimalis Sesuai Permintaan User -->
        <div class="text-center max-w-xl mx-auto space-y-1">
            <h2 class="text-xl sm:text-2xl font-black tracking-tight text-[#212121] dark:text-[#F9F8F6]">
                Metode Pembayaran
            </h2>
            <p class="text-xs text-[#212121]/60 dark:text-[#F9F8F6]/60">
                Pilihan metode pembayaran resmi, instan, dan terverifikasi otomatis.
            </p>
        </div>

        <!-- Shelf Icon Pembayaran: Bersih, Kosong Tanpa Clutter & Bebas Teks Berlebih -->
        <div class="p-4 sm:p-8 rounded-2xl bg-white dark:bg-[#141416] border border-[#212121]/10 dark:border-white/10 shadow-xs">
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 sm:gap-4 items-center">
                <!-- QRIS -->
                <div class="h-12 sm:h-14 flex items-center justify-center p-2 rounded-xl bg-stone-50/60 dark:bg-white border border-stone-200/50 dark:border-transparent transition duration-200 hover:opacity-80 shadow-2xs" title="QRIS">
                    <img src="{{ asset('images/payments/qris.svg') }}" alt="QRIS" class="max-h-7 max-w-[85px] w-auto object-contain" loading="lazy" decoding="async">
                </div>
                <!-- GoPay -->
                <div class="h-12 sm:h-14 flex items-center justify-center p-2 rounded-xl bg-stone-50/60 dark:bg-white border border-stone-200/50 dark:border-transparent transition duration-200 hover:opacity-80 shadow-2xs" title="GoPay">
                    <img src="{{ asset('images/payments/gopay.svg') }}" alt="GoPay" class="max-h-6 max-w-[85px] w-auto object-contain" loading="lazy" decoding="async">
                </div>
                <!-- OVO -->
                <div class="h-12 sm:h-14 flex items-center justify-center p-2 rounded-xl bg-stone-50/60 dark:bg-white border border-stone-200/50 dark:border-transparent transition duration-200 hover:opacity-80 shadow-2xs" title="OVO">
                    <img src="{{ asset('images/payments/ovo.svg') }}" alt="OVO" class="max-h-6 max-w-[75px] w-auto object-contain" loading="lazy" decoding="async">
                </div>
                <!-- DANA -->
                <div class="h-12 sm:h-14 flex items-center justify-center p-2 rounded-xl bg-stone-50/60 dark:bg-white border border-stone-200/50 dark:border-transparent transition duration-200 hover:opacity-80 shadow-2xs" title="DANA">
                    <img src="{{ asset('images/payments/dana.svg') }}" alt="DANA" class="max-h-6 max-w-[85px] w-auto object-contain" loading="lazy" decoding="async">
                </div>
                <!-- ShopeePay -->
                <div class="h-12 sm:h-14 flex items-center justify-center p-2 rounded-xl bg-stone-50/60 dark:bg-white border border-stone-200/50 dark:border-transparent transition duration-200 hover:opacity-80 shadow-2xs" title="ShopeePay">
                    <img src="{{ asset('images/payments/shopeepay.svg') }}" alt="ShopeePay" class="max-h-7 max-w-[90px] w-auto object-contain" loading="lazy" decoding="async">
                </div>
                <!-- LinkAja -->
                <div class="h-12 sm:h-14 flex items-center justify-center p-2 rounded-xl bg-stone-50/60 dark:bg-white border border-stone-200/50 dark:border-transparent transition duration-200 hover:opacity-80 shadow-2xs" title="LinkAja">
                    <img src="{{ asset('images/payments/linkaja.svg') }}" alt="LinkAja" class="max-h-7 max-w-[80px] w-auto object-contain" loading="lazy" decoding="async">
                </div>
                <!-- BCA -->
                <div class="h-12 sm:h-14 flex items-center justify-center p-2 rounded-xl bg-stone-50/60 dark:bg-white border border-stone-200/50 dark:border-transparent transition duration-200 hover:opacity-80 shadow-2xs" title="BCA Virtual Account">
                    <img src="{{ asset('images/payments/bca.svg') }}" alt="BCA" class="max-h-6 max-w-[85px] w-auto object-contain" loading="lazy" decoding="async">
                </div>
                <!-- Mandiri -->
                <div class="h-12 sm:h-14 flex items-center justify-center p-2 rounded-xl bg-stone-50/60 dark:bg-white border border-stone-200/50 dark:border-transparent transition duration-200 hover:opacity-80 shadow-2xs" title="Mandiri Virtual Account (Livin')">
                    <img src="{{ asset('images/payments/mandiri.svg') }}" alt="Mandiri" class="max-h-6 max-w-[90px] w-auto object-contain" loading="lazy" decoding="async">
                </div>
                <!-- BRI -->
                <div class="h-12 sm:h-14 flex items-center justify-center p-2 rounded-xl bg-stone-50/60 dark:bg-white border border-stone-200/50 dark:border-transparent transition duration-200 hover:opacity-80 shadow-2xs" title="BRI Virtual Account (BRIVA)">
                    <img src="{{ asset('images/payments/bri.svg') }}" alt="BRI" class="max-h-6 max-w-[90px] w-auto object-contain" loading="lazy" decoding="async">
                </div>
                <!-- BNI -->
                <div class="h-12 sm:h-14 flex items-center justify-center p-2 rounded-xl bg-stone-50/60 dark:bg-white border border-stone-200/50 dark:border-transparent transition duration-200 hover:opacity-80 shadow-2xs" title="BNI Virtual Account">
                    <img src="{{ asset('images/payments/bni.svg') }}" alt="BNI" class="max-h-6 max-w-[85px] w-auto object-contain" loading="lazy" decoding="async">
                </div>
                <!-- Permata -->
                <div class="h-12 sm:h-14 flex items-center justify-center p-2 rounded-xl bg-stone-50/60 dark:bg-white border border-stone-200/50 dark:border-transparent transition duration-200 hover:opacity-80 shadow-2xs" title="Permata Virtual Account">
                    <img src="{{ asset('images/payments/permata.svg') }}" alt="Permata" class="max-h-6 max-w-[90px] w-auto object-contain" loading="lazy" decoding="async">
                </div>
                <!-- CIMB Niaga -->
                <div class="h-12 sm:h-14 flex items-center justify-center p-2 rounded-xl bg-stone-50/60 dark:bg-white border border-stone-200/50 dark:border-transparent transition duration-200 hover:opacity-80 shadow-2xs" title="CIMB Niaga">
                    <img src="{{ asset('images/payments/cimb.svg') }}" alt="CIMB Niaga" class="max-h-5.5 max-w-[90px] w-auto object-contain" loading="lazy" decoding="async">
                </div>
                <!-- Indomaret -->
                <div class="h-12 sm:h-14 flex items-center justify-center p-2 rounded-xl bg-stone-50/60 dark:bg-white border border-stone-200/50 dark:border-transparent transition duration-200 hover:opacity-80 shadow-2xs" title="Indomaret">
                    <img src="{{ asset('images/payments/indomaret.svg') }}" alt="Indomaret" class="max-h-7 max-w-[95px] w-auto object-contain" loading="lazy" decoding="async">
                </div>
                <!-- Alfamart -->
                <div class="h-12 sm:h-14 flex items-center justify-center p-2 rounded-xl bg-stone-50/60 dark:bg-white border border-stone-200/50 dark:border-transparent transition duration-200 hover:opacity-80 shadow-2xs" title="Alfamart">
                    <img src="{{ asset('images/payments/alfamart.svg') }}" alt="Alfamart" class="max-h-7 max-w-[95px] w-auto object-contain" loading="lazy" decoding="async">
                </div>
            </div>
        </div>

        <!-- Banner Layanan CS WhatsApp -->
        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-[#212121] via-[#2a1a1c] to-[#6D0808] text-white flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center space-x-3.5 text-center sm:text-left">
                <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center text-emerald-400 shrink-0">
                    <x-icon name="headphones" class="w-5 h-5" />
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-white">Butuh Bantuan Pembayaran atau Cek Transaksi?</h4>
                    <p class="text-[11px] text-white/70 mt-0.5">Layanan bantuan Customer Care kami siap membantu kendala top up kamu setiap hari.</p>
                </div>
            </div>
            <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number', env('WHATSAPP_NUMBER', '6281234567890')) }}?text={{ urlencode('Halo Admin KAIDEN, saya butuh bantuan mengenai metode pembayaran.') }}" 
               target="_blank" 
               rel="noopener noreferrer" 
               class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 transition shadow-xs shrink-0">
                <x-icon name="headphones" class="w-4 h-4" />
                <span>Chat WhatsApp 24/7</span>
            </a>
        </div>

    </div>
</div>

<!-- Live Transaction & Testimonial Floating Popup (Nickname Masking: misal gamerabah -> ga******bah) -->
<div id="live-testimonial-popup" 
     class="fixed bottom-4 left-4 sm:bottom-6 sm:left-6 z-40 max-w-[340px] sm:max-w-sm w-[calc(100%-2rem)] sm:w-auto transform transition-all duration-500 ease-out translate-y-8 opacity-0 pointer-events-none"
     aria-live="polite">
    <div class="relative bg-white/95 dark:bg-[#18181B]/95 border border-stone-200 dark:border-white/10 rounded-2xl shadow-xl p-3 sm:p-3.5 backdrop-blur-md flex items-start space-x-3 text-left">
        <!-- Thumbnail Game -->
        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl overflow-hidden shrink-0 border border-stone-200/80 dark:border-white/10 bg-stone-100 dark:bg-zinc-800 shadow-2xs">
            <img id="popup-thumb" src="{{ asset('images/bg/freefire.jpg') }}" alt="Game" class="w-full h-full object-cover">
        </div>

        <!-- Content Area -->
        <div class="flex-1 min-w-0 pr-4">
            <div class="flex items-center space-x-1.5 flex-wrap">
                <span id="popup-nickname" class="font-mono font-bold text-xs text-[#212121] dark:text-zinc-100">
                    ga******bah
                </span>
                <span class="text-[11px] text-[#212121]/40 dark:text-zinc-500">
                    •
                </span>
                <span id="popup-time" class="text-[10px] text-[#212121]/50 dark:text-zinc-400 font-medium">
                    2 menit yang lalu
                </span>
            </div>

            <div id="popup-action" class="text-xs text-[#212121]/80 dark:text-zinc-200 mt-0.5 line-clamp-1 font-medium">
                memesan <span class="font-bold text-[#6D0808] dark:text-rose-400">70 Diamonds Free Fire</span>
            </div>

            <!-- Testimoni / Review Quote -->
            <div id="popup-review-row" class="mt-1 flex items-start space-x-1.5 text-[11px] text-amber-500 dark:text-amber-400">
                <div id="popup-stars" class="flex items-center shrink-0 pt-0.5">
                    <!-- Stars injected via JS -->
                </div>
                <p id="popup-comment" class="text-[11px] text-[#212121]/70 dark:text-zinc-400 italic line-clamp-1">
                    "Top up kilat banget langsung masuk!"
                </p>
            </div>
        </div>

        <!-- Tombol Dismiss / Close (X) -->
        <button type="button" id="popup-close-btn" class="absolute top-2.5 right-2.5 text-[#212121]/40 hover:text-[#212121] dark:text-zinc-400 dark:hover:text-white p-1 rounded-md transition" title="Tutup">
            <x-icon name="x" class="w-3.5 h-3.5" />
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentSlide = 0;
    const slides = document.querySelectorAll('.promo-slide');
    const dots = document.querySelectorAll('.promo-dot');
    let timer = null;

    window.goToSlide = function (index) {
        if (!slides.length) return;
        slides.forEach((s, i) => {
            if (i === index) {
                s.classList.remove('hidden');
                s.classList.add('flex');
            } else {
                s.classList.add('hidden');
                s.classList.remove('flex');
            }
        });
        dots.forEach((d, i) => {
            if (i === index) {
                d.className = 'promo-dot w-6 h-2 rounded-full bg-[#6D0808] transition-all duration-300 cursor-pointer';
            } else {
                d.className = 'promo-dot w-2 h-2 rounded-full bg-[#212121]/20 hover:bg-[#212121]/40 dark:bg-white/30 dark:hover:bg-white/50 transition-all duration-300 cursor-pointer';
            }
        });
        currentSlide = index;
        resetTimer();
    };

    function nextSlide() {
        if (!slides.length) return;
        let next = (currentSlide + 1) % slides.length;
        window.goToSlide(next);
    }

    function resetTimer() {
        if (timer) clearInterval(timer);
        timer = setInterval(nextSlide, 5000);
    }

    resetTimer();

    // Live Transaction & Testimonial Popup Rotator
    const liveItems = @json($liveTestimonials ?? []);
    const popupEl = document.getElementById('live-testimonial-popup');
    const popupThumb = document.getElementById('popup-thumb');
    const popupNickname = document.getElementById('popup-nickname');
    const popupTime = document.getElementById('popup-time');
    const popupAction = document.getElementById('popup-action');
    const popupStars = document.getElementById('popup-stars');
    const popupComment = document.getElementById('popup-comment');
    const popupCloseBtn = document.getElementById('popup-close-btn');

    if (popupEl && liveItems && liveItems.length > 0) {
        let currentIndex = 0;
        let isPaused = false;
        let hideTimeout = null;

        function showNextTestimonial() {
            if (isPaused) return;

            const item = liveItems[currentIndex];
            if (!item) return;

            if (popupThumb) {
                popupThumb.src = item.thumbnail;
                popupThumb.alt = item.game || 'Game';
            }
            if (popupNickname) popupNickname.textContent = item.nickname;
            if (popupTime) popupTime.textContent = item.time_ago;
            if (popupAction) popupAction.innerHTML = item.action;

            if (popupStars) {
                let starHtml = '';
                const rating = item.rating || 5;
                for (let i = 0; i < rating; i++) {
                    starHtml += '<svg class="w-3 h-3 text-amber-400 fill-current inline" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
                }
                popupStars.innerHTML = starHtml;
            }

            if (popupComment) {
                if (item.comment) {
                    popupComment.textContent = '“' + item.comment + '”';
                    popupComment.classList.remove('hidden');
                } else {
                    popupComment.classList.add('hidden');
                }
            }

            // Animate In (Fade in & Slide up)
            popupEl.classList.remove('translate-y-8', 'opacity-0', 'pointer-events-none');
            popupEl.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');

            // Stay for 5.5 seconds
            hideTimeout = setTimeout(() => {
                hideTestimonial();
            }, 5500);
        }

        function hideTestimonial() {
            popupEl.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
            popupEl.classList.add('translate-y-8', 'opacity-0', 'pointer-events-none');

            currentIndex = (currentIndex + 1) % liveItems.length;

            // Wait 5 seconds before showing next
            setTimeout(() => {
                showNextTestimonial();
            }, 5000);
        }

        popupEl.addEventListener('mouseenter', () => {
            isPaused = true;
            if (hideTimeout) clearTimeout(hideTimeout);
        });

        popupEl.addEventListener('mouseleave', () => {
            isPaused = false;
            hideTimeout = setTimeout(() => {
                hideTestimonial();
            }, 3000);
        });

        if (popupCloseBtn) {
            popupCloseBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                popupEl.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
                popupEl.classList.add('translate-y-8', 'opacity-0', 'pointer-events-none');
                isPaused = true;
                // Pause for 45 seconds if dismissed
                setTimeout(() => {
                    isPaused = false;
                    showNextTestimonial();
                }, 45000);
            });
        }

        // Initial launch after 2.5 seconds
        setTimeout(showNextTestimonial, 2500);
    }
    // Filter Kategori Game Beranda Dinamis (Tanpa Reload Halaman + Skeleton Web Render)
    window.filterHomeCategory = function(cat) {
        const gridContainer = document.getElementById('katalog-grid-container');
        const skeleton = document.getElementById('home-games-skeleton');

        if (!gridContainer || !skeleton) return;

        skeleton.classList.remove('hidden');
        gridContainer.classList.add('hidden');

        // Update active button state
        document.querySelectorAll('.home-cat-btn').forEach(btn => {
            const isTarget = btn.getAttribute('data-cat') === cat;
            if (isTarget) {
                btn.className = "home-cat-btn px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition cursor-pointer bg-[#6D0808] text-white shadow-xs";
            } else {
                btn.className = "home-cat-btn px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition cursor-pointer bg-white text-[#212121]/70 border border-[#212121]/10 hover:text-[#212121] dark:bg-[#18181B] dark:text-zinc-300 dark:border-white/10 dark:hover:text-white";
            }
        });

        const url = '{{ route("home") }}?ajax_games=1&category=' + encodeURIComponent(cat);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.html) {
                gridContainer.innerHTML = data.html;
            }
            skeleton.classList.add('hidden');
            gridContainer.classList.remove('hidden');
        })
        .catch(() => {
            skeleton.classList.add('hidden');
            gridContainer.classList.remove('hidden');
        });
    };
});
</script>
@endpush
