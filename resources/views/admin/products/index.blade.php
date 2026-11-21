@extends('layouts.admin')

@section('title', 'Manajemen Produk & Margin Harga - KAIDEN')
@section('header_title', 'Katalog Produk & Proteksi Margin Harga')

@section('content')
<div class="space-y-6 pt-2">

    <!-- Flash Messages (Success & Errors) -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-2.5">
                <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                    <x-icon name="check" class="w-3.5 h-3.5" strokeWidth="2.5" />
                </div>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">
                <x-icon name="x" class="w-4 h-4" />
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium space-y-1.5 shadow-xs">
            <div class="flex items-center space-x-2 text-red-900 font-bold">
                <div class="w-6 h-6 rounded-full bg-red-600 text-white flex items-center justify-center shrink-0">
                    <x-icon name="alert-circle" class="w-3.5 h-3.5" strokeWidth="2.5" />
                </div>
                <span>Peringatan Proteksi Anti-Rugi & Validasi:</span>
            </div>
            <ul class="list-disc pl-9 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Banner Edukasi Mekanisme Proteksi & Diskon Anti-Boncos -->
    <div class="p-4 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
        <div class="flex items-start sm:items-center space-x-3">
            <div class="w-8 h-8 rounded-xl bg-[#6D0808]/10 text-[#6D0808] flex items-center justify-center shrink-0">
                <x-icon name="info" class="w-4 h-4" />
            </div>
            <div>
                <div class="font-bold text-[#212121]">Mekanisme Proteksi Anti-Rugi Modal Digiflazz & Transparansi Diskon</div>
                <p class="text-[#212121]/70 leading-relaxed">
                    Setiap harga jual (<code class="font-mono text-[#6D0808]">sell_price</code>) wajib bernilai sama atau lebih besar dari harga modal Digiflazz (<code class="font-mono text-stone-700">cost_price</code>). Jika Anda memasukkan harga promo di bawah modal, sistem akan secara otomatis menolak transaksi untuk memastikan keuntungan bisnis Anda tetap aman.
                </p>
            </div>
        </div>
    </div>

    <!-- Action & Filter Bar -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <!-- Form Filter -->
        <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2.5 sm:gap-3 flex-1">
            <!-- Filter Game -->
            <select name="game_id" onchange="this.form.submit()" class="w-full sm:w-auto px-3 py-2 rounded-xl border border-[#212121]/15 bg-white text-xs text-[#212121] focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20">
                <option value="">Semua Game</option>
                @foreach($games as $g)
                    <option value="{{ $g->id }}" {{ $gameId == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                @endforeach
            </select>

            <!-- Filter Status -->
            <select name="status" onchange="this.form.submit()" class="w-full sm:w-auto px-3 py-2 rounded-xl border border-[#212121]/15 bg-white text-xs text-[#212121] focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20">
                <option value="">Semua Status</option>
                <option value="1" {{ $status === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ $status === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <!-- Search -->
            <div class="relative w-full sm:flex-1 sm:min-w-[200px]">
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama produk / SKU..." class="w-full pl-8 pr-3 py-2 rounded-xl border border-[#212121]/15 bg-white text-xs focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20">
                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-[#212121]/40">
                    <x-icon name="search" class="w-3.5 h-3.5" />
                </div>
            </div>

            <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#212121] hover:bg-black transition">
                Terapkan
            </button>

            @if($gameId || $status !== null || $search)
                <a href="{{ route('admin.products.index') }}" class="w-full sm:w-auto px-3 py-2 rounded-xl text-xs font-medium text-[#212121]/70 hover:text-[#212121] bg-stone-100 hover:bg-stone-200 transition text-center">
                    Reset Filter
                </a>
            @endif
        </form>

        <div class="flex items-center gap-2.5 flex-wrap">
            <!-- Tombol Kelola Gambar Game -->
            @if(auth()->user()->hasPermission('produk.ubah'))
                <button type="button" onclick="openAllGamesModal()" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-[#212121] bg-stone-100 hover:bg-stone-200 border border-[#212121]/15 transition shadow-2xs cursor-pointer">
                    <x-icon name="image" class="w-4 h-4 text-[#6D0808]" />
                    <span>Kelola Gambar Game</span>
                </button>
            @endif

            <!-- Tombol Sinkronisasi Digiflazz (produk.sinkron) -->
            @if(auth()->user()->hasPermission('produk.sinkron'))
                <form action="{{ route('admin.products.sync') }}" method="POST" id="sync-digiflazz-form">
                    @csrf
                    <button type="button" onclick="confirmSyncDigiflazz()" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs cursor-pointer">
                        <x-icon name="refresh" class="w-4 h-4" />
                        <span>Tarik Data Digiflazz (API Sync)</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Table of Products -->
    <div class="rounded-2xl bg-white border border-[#212121]/10 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F9F8F6] border-b border-[#212121]/10 text-[#212121]/70 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-4">Game & Kategori</th>
                        <th class="py-3 px-4">SKU & Nama Produk</th>
                        <th class="py-3 px-4">Harga Modal (DF)</th>
                        <th class="py-3 px-4">Harga Normal (Coret)</th>
                        <th class="py-3 px-4">Diskon Promo</th>
                        <th class="py-3 px-4">Harga Jual (KAIDEN)</th>
                        <th class="py-3 px-4">Margin Untung Bersih</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Kelola Harga & Diskon</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#212121]/5">
                    @forelse($products as $prod)
                        @php
                            $marginStatus = $prod->margin_status;
                        @endphp
                        <tr class="hover:bg-[#F9F8F6]/60 transition">
                            <!-- 1. Game & Kategori -->
                            <td class="py-3.5 px-4 font-semibold text-[#212121]">
                                <div class="flex items-center space-x-2.5">
                                    <div class="relative group w-8 h-10 rounded-lg bg-stone-100 overflow-hidden shrink-0 border border-[#212121]/10 cursor-pointer shadow-2xs"
                                         @if(auth()->user()->hasPermission('produk.ubah'))
                                             onclick="openEditGameImageModal({{ $prod->game->id }}, '{{ addslashes($prod->game->name) }}', '{{ asset($prod->game->thumbnail ?? 'images/bg/1.jpg') }}', '{{ asset($prod->game->banner_image ?? '') }}', '{{ addslashes($prod->game->publisher ?? '') }}', '{{ addslashes($prod->game->category ?? '') }}')"
                                             title="Klik untuk ubah poster game {{ $prod->game->name }}"
                                         @endif>
                                        <img src="{{ asset($prod->game->thumbnail ?? 'images/bg/1.jpg') }}" alt="{{ $prod->game->name }}" class="w-full h-full object-cover">
                                        @if(auth()->user()->hasPermission('produk.ubah'))
                                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                                                <x-icon name="edit" class="w-3.5 h-3.5" />
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-[#212121] flex items-center gap-1.5">
                                            <span>{{ $prod->game->name }}</span>
                                            @if(auth()->user()->hasPermission('produk.ubah'))
                                                <button type="button" 
                                                        onclick="openEditGameImageModal({{ $prod->game->id }}, '{{ addslashes($prod->game->name) }}', '{{ asset($prod->game->thumbnail ?? 'images/bg/1.jpg') }}', '{{ asset($prod->game->banner_image ?? '') }}', '{{ addslashes($prod->game->publisher ?? '') }}', '{{ addslashes($prod->game->category ?? '') }}')"
                                                        class="text-stone-400 hover:text-[#6D0808] transition p-0.5 rounded hover:bg-stone-100" 
                                                        title="Ubah Poster & Gambar {{ $prod->game->name }}">
                                                    <x-icon name="image" class="w-3.5 h-3.5" />
                                                </button>
                                            @endif
                                        </div>
                                        <span class="text-[10px] text-[#212121]/50 uppercase font-mono">{{ $prod->category ?? 'Top Up' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- 2. SKU & Nama Produk -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#212121] leading-snug">{{ $prod->name }}</div>
                                <div class="font-mono text-[10px] text-[#212121]/60">SKU: {{ $prod->digiflazz_sku }}</div>
                            </td>

                            <!-- 3. Modal Digiflazz -->
                            <td class="py-3.5 px-4 font-mono tabular-nums text-stone-700 font-medium">
                                Rp {{ number_format($prod->cost_price, 0, ',', '.') }}
                            </td>

                            <!-- 4. Harga Normal (Coret) -->
                            <td class="py-3.5 px-4 font-mono tabular-nums">
                                @if($prod->has_discount)
                                    <span class="line-through text-[#212121]/50 text-xs">
                                        Rp {{ number_format($prod->original_price, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-[#212121]/40 text-xs">-</span>
                                @endif
                            </td>

                            <!-- 5. Diskon Promo -->
                            <td class="py-3.5 px-4">
                                @if($prod->has_discount)
                                    <div class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-full text-[10px] font-bold text-[#6D0808] bg-rose-50 border border-rose-200">
                                        <x-icon name="tag" class="w-3 h-3 text-[#6D0808]" />
                                        <span>Diskon {{ $prod->discount_percent }}%</span>
                                    </div>
                                    <div class="text-[10px] text-[#6D0808]/70 font-mono mt-0.5">
                                        Hemat Rp {{ number_format($prod->discount_amount, 0, ',', '.') }}
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium text-stone-600 bg-stone-100 border border-stone-200">
                                        Reguler
                                    </span>
                                @endif
                            </td>

                            <!-- 6. Harga Jual KAIDEN -->
                            <td class="py-3.5 px-4 font-mono tabular-nums font-bold text-sm text-[#6D0808]">
                                Rp {{ number_format($prod->sell_price, 0, ',', '.') }}
                            </td>

                            <!-- 7. Margin Bersih & Persentase Keuntungan -->
                            <td class="py-3.5 px-4">
                                <div class="font-mono tabular-nums font-semibold {{ $marginStatus['level'] === 'danger' ? 'text-red-700' : 'text-emerald-700' }}">
                                    +Rp {{ number_format($prod->margin, 0, ',', '.') }}
                                </div>
                                <div class="flex items-center space-x-1.5 mt-0.5">
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold border {{ $marginStatus['badge_class'] }}">
                                        +{{ $prod->margin_percent }}% ({{ $marginStatus['label'] }})
                                    </span>
                                </div>
                            </td>

                            <!-- 8. Status Aktif / Nonaktif -->
                            <td class="py-3.5 px-4">
                                @if($prod->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold text-red-700 bg-red-50 border border-red-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <!-- 9. Kelola Harga & Stok -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center space-x-2">
                                    <!-- Tombol Buka Modal Edit Harga & Diskon (harga.ubah) -->
                                    @if(auth()->user()->hasPermission('harga.ubah'))
                                        <button type="button" 
                                                onclick="openPricingModal({{ json_encode([
                                                    'id' => $prod->id,
                                                    'name' => $prod->name,
                                                    'game' => $prod->game->name,
                                                    'sku' => $prod->digiflazz_sku,
                                                    'cost_price' => $prod->cost_price,
                                                    'original_price' => $prod->original_price,
                                                    'sell_price' => $prod->sell_price,
                                                    'action_url' => route('admin.products.update_price', $prod->id),
                                                ]) }})" 
                                                class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-[#212121] text-white hover:bg-[#6D0808] transition shadow-xs">
                                            <x-icon name="edit" class="w-3.5 h-3.5" />
                                            <span>Atur Harga</span>
                                        </button>
                                    @endif

                                    <!-- Form Toggle Status/Stok (stok.ubah & audit log) -->
                                    @if(auth()->user()->hasPermission('stok.ubah'))
                                        <form action="{{ route('admin.products.toggle', $prod->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1.5 rounded-lg text-xs font-medium border {{ $prod->is_active ? 'border-red-200 text-red-700 hover:bg-red-50' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }} transition">
                                                {{ $prod->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-[#212121]/50">
                                Tidak ada produk yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-[#212121]/10 bg-[#F9F8F6]">
            {{ $products->links() }}
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL PENGATURAN HARGA, DISKON & PROTEKSI ANTI-RUGI MODAL      -->
<!-- ============================================================== -->
<div id="pricing-modal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-[#212121]/15 shadow-2xl max-w-lg w-full overflow-hidden transition-all transform scale-95 opacity-0 duration-200" id="pricing-modal-box">
        
        <!-- Header Modal -->
        <div class="px-6 py-4 bg-[#F9F8F6] border-b border-[#212121]/10 flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-xl bg-[#6D0808]/10 text-[#6D0808] flex items-center justify-center shrink-0">
                    <x-icon name="dollar-sign" class="w-4 h-4" />
                </div>
                <div>
                    <h3 class="text-sm font-bold text-[#212121]">Atur Harga & Diskon Produk</h3>
                    <p class="text-[11px] text-[#212121]/60">Sistem Proteksi Anti-Rugi Modal Digiflazz</p>
                </div>
            </div>
            <button type="button" onclick="closePricingModal()" class="p-1 rounded-lg text-[#212121]/50 hover:text-[#212121] hover:bg-stone-200 transition">
                <x-icon name="x" class="w-4 h-4" />
            </button>
        </div>

        <!-- Form Edit Harga -->
        <form id="pricing-form" method="POST" action="" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            <!-- Info Produk -->
            <div class="p-3.5 rounded-xl bg-stone-50 border border-stone-200/80 space-y-1">
                <div class="text-[10px] text-[#212121]/50 uppercase font-mono tracking-wider" id="modal-product-game">GAME</div>
                <div class="text-xs font-bold text-[#212121]" id="modal-product-name">Nama Produk</div>
                <div class="text-[10px] font-mono text-[#212121]/60" id="modal-product-sku">SKU: -</div>
            </div>

            <!-- Harga Modal Digiflazz (Locked Reference) -->
            <div class="p-3.5 rounded-xl bg-amber-50/70 border border-amber-200 flex items-center justify-between">
                <div class="space-y-0.5">
                    <div class="text-[11px] font-bold text-amber-900 flex items-center space-x-1.5">
                        <x-icon name="lock" class="w-3.5 h-3.5 text-amber-700" />
                        <span>Harga Modal Supplier (Digiflazz):</span>
                    </div>
                    <div class="text-[10px] text-amber-800/80">Batas bawah proteksi anti-rugi otomatis</div>
                </div>
                <div class="font-mono tabular-nums text-sm font-bold text-amber-950" id="modal-cost-price-display">
                    Rp 0
                </div>
            </div>

            <!-- Input 1: Harga Normal (Coret) -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="modal-input-original" class="block text-xs font-semibold text-[#212121]">
                        Harga Normal / Sebelum Diskon (Opsional)
                    </label>
                    <span class="text-[10px] text-[#212121]/50">Akan dicoret jika ada promo</span>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-xs font-mono text-[#212121]/50 font-bold">
                        Rp
                    </div>
                    <input type="number" 
                           name="original_price" 
                           id="modal-input-original" 
                           step="100" 
                           min="0" 
                           placeholder="Kosongkan jika bukan produk promo" 
                           class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-[#212121]/15 bg-white text-xs font-mono tabular-nums focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 focus:border-[#6D0808] transition">
                </div>
                <p class="text-[10px] text-[#212121]/50">Jika diisi lebih tinggi dari harga jual, badge diskon persentase akan otomatis muncul di toko.</p>
            </div>

            <!-- Input 2: Harga Jual / Promo Aktif -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="modal-input-sell" class="block text-xs font-semibold text-[#212121]">
                        Harga Jual / Promo KAIDEN <span class="text-[#6D0808]">*</span>
                    </label>
                    <span class="text-[10px] font-semibold text-emerald-700">Wajib ≥ Modal Digiflazz</span>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-xs font-mono text-[#212121]/50 font-bold">
                        Rp
                    </div>
                    <input type="number" 
                           name="sell_price" 
                           id="modal-input-sell" 
                           required 
                           step="100" 
                           min="0" 
                           class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-[#212121]/15 bg-white text-xs font-mono tabular-nums font-bold text-[#6D0808] focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 focus:border-[#6D0808] transition">
                </div>
            </div>

            <!-- Live Calculator: Perhitungan Persentase Diskon & Margin Bersih -->
            <div class="p-4 rounded-xl bg-[#F9F8F6] border border-[#212121]/10 space-y-2.5">
                <div class="text-[11px] font-bold text-[#212121] uppercase tracking-wider flex items-center justify-between">
                    <span>Kalkulasi Live Margin & Diskon</span>
                    <span id="calc-badge-status" class="px-2 py-0.5 rounded text-[10px] font-bold bg-stone-200 text-stone-700">Menghitung...</span>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-1 border-t border-[#212121]/5">
                    <!-- Diskon Pelanggan -->
                    <div class="space-y-0.5">
                        <span class="text-[10px] text-[#212121]/60">Diskon Pelanggan:</span>
                        <div class="font-mono text-xs font-bold text-[#6D0808]" id="calc-discount-display">
                            0% (Tidak ada promo)
                        </div>
                    </div>

                    <!-- Keuntungan Bersih KAIDEN -->
                    <div class="space-y-0.5">
                        <span class="text-[10px] text-[#212121]/60">Margin Untung Bersih:</span>
                        <div class="font-mono text-xs font-bold text-emerald-700" id="calc-margin-display">
                            +Rp 0 (+0%)
                        </div>
                    </div>
                </div>

                <!-- Alert Boncos / Rugi Modal Real-time -->
                <div id="calc-loss-alert" class="hidden p-3 rounded-lg bg-red-100 border border-red-300 text-red-900 text-xs font-medium space-y-1">
                    <div class="font-bold flex items-center space-x-1.5 text-red-950">
                        <x-icon name="alert-triangle" class="w-4 h-4 text-red-700 shrink-0" />
                        <span>Peringatan Proteksi Anti-Rugi Modal!</span>
                    </div>
                    <p id="calc-loss-text" class="text-[11px] leading-relaxed">
                        Harga jual di bawah modal!
                    </p>
                </div>

                <!-- Alert Original Price < Sell Price -->
                <div id="calc-original-alert" class="hidden p-3 rounded-lg bg-amber-100 border border-amber-300 text-amber-900 text-xs font-medium space-y-1">
                    <p id="calc-original-text" class="text-[11px] leading-relaxed">
                        Harga normal tidak boleh lebih rendah dari harga jual diskon.
                    </p>
                </div>
            </div>

            <!-- Footer Modal Buttons -->
            <div class="pt-2 flex items-center justify-end space-x-2.5">
                <button type="button" onclick="closePricingModal()" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-[#212121]/70 hover:text-[#212121] bg-stone-100 hover:bg-stone-200 transition">
                    Batal
                </button>
                <button type="submit" id="btn-save-pricing" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-[#6D0808] hover:bg-[#540606] disabled:opacity-50 disabled:cursor-not-allowed transition shadow-xs">
                    Simpan Perubahan Harga
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Kelola / Upload Gambar Game & Poster -->
<div id="game-image-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs hidden transition-all duration-200">
    <div id="game-image-modal-box" class="w-full max-w-xl bg-white rounded-3xl shadow-2xl border border-[#212121]/10 overflow-hidden transform scale-95 opacity-0 transition-all duration-200">
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-[#F9F8F6] border-b border-[#212121]/10 flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-xl bg-[#6D0808]/10 text-[#6D0808] flex items-center justify-center shrink-0">
                    <x-icon name="image" class="w-4 h-4" />
                </div>
                <div>
                    <h3 class="text-sm font-bold text-[#212121]">Kelola Gambar & Poster Game</h3>
                    <p class="text-[11px] text-[#212121]/60 font-medium" id="modal-game-title">Nama Game</p>
                </div>
            </div>
            <button type="button" onclick="closeEditGameImageModal()" class="w-7 h-7 rounded-lg text-[#212121]/50 hover:text-[#212121] hover:bg-stone-200/50 flex items-center justify-center transition">
                <x-icon name="x" class="w-4 h-4" />
            </button>
        </div>

        <!-- Modal Form -->
        <form id="game-image-form" action="" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf

            <!-- Preview Gambar Poster Saat Ini & File Upload -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-[#212121]">
                    Thumbnail Poster Game
                    <span class="text-[10px] font-normal text-[#212121]/60 block mt-0.5">Gambar ini akan tampil pada katalog game, beranda, dan halaman order pelanggan.</span>
                </label>

                <div class="flex items-start gap-4 p-3.5 rounded-2xl bg-[#F9F8F6] border border-[#212121]/10">
                    <!-- Preview Box -->
                    <div class="w-20 h-28 rounded-xl bg-stone-200 overflow-hidden shrink-0 border border-[#212121]/15 shadow-xs relative">
                        <img id="modal-thumb-preview" src="{{ asset('images/bg/1.jpg') }}" alt="Preview" class="w-full h-full object-cover">
                        <span class="absolute bottom-1 right-1 bg-black/70 text-white text-[9px] px-1 py-0.5 rounded font-mono">Preview</span>
                    </div>

                    <!-- File Input & Upload -->
                    <div class="flex-1 space-y-2">
                        <div class="space-y-1">
                            <input type="file" name="thumbnail_file" id="modal-input-thumb" accept="image/png,image/jpeg,image/webp,image/jpg" onchange="previewThumbnail(this)" class="block w-full text-xs text-stone-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#6D0808] file:text-white hover:file:bg-[#540606] file:cursor-pointer transition">
                            <p class="text-[10px] text-stone-500">Format: JPG, PNG, WEBP. Maksimal 4 MB.</p>
                        </div>

                        <!-- Atau Preset Pilihan Cepat -->
                        <div class="pt-2 border-t border-[#212121]/5">
                            <span class="text-[10px] font-semibold text-[#212121]/70 block mb-1.5">Atau pilih Preset Gambar Bawaan:</span>
                            <input type="hidden" name="preset_image" id="modal-input-preset" value="">
                            <div class="grid grid-cols-4 gap-1.5">
                                <button type="button" onclick="selectPresetImage('images/bg/aspirant.jpg')" class="group relative rounded-lg overflow-hidden h-9 border border-stone-200 hover:border-[#6D0808] focus:ring-2 focus:ring-[#6D0808] transition" title="MLBB Aspirants">
                                    <img src="{{ asset('images/bg/aspirant.jpg') }}" alt="MLBB" class="w-full h-full object-cover">
                                </button>
                                <button type="button" onclick="selectPresetImage('images/bg/freefire.jpg')" class="group relative rounded-lg overflow-hidden h-9 border border-stone-200 hover:border-[#6D0808] focus:ring-2 focus:ring-[#6D0808] transition" title="Free Fire">
                                    <img src="{{ asset('images/bg/freefire.jpg') }}" alt="Free Fire" class="w-full h-full object-cover">
                                </button>
                                <button type="button" onclick="selectPresetImage('images/bg/valorant.jpg')" class="group relative rounded-lg overflow-hidden h-9 border border-stone-200 hover:border-[#6D0808] focus:ring-2 focus:ring-[#6D0808] transition" title="Valorant">
                                    <img src="{{ asset('images/bg/valorant.jpg') }}" alt="Valorant" class="w-full h-full object-cover">
                                </button>
                                <button type="button" onclick="selectPresetImage('images/bg/codm.jpg')" class="group relative rounded-lg overflow-hidden h-9 border border-stone-200 hover:border-[#6D0808] focus:ring-2 focus:ring-[#6D0808] transition" title="CODM">
                                    <img src="{{ asset('images/bg/codm.jpg') }}" alt="CODM" class="w-full h-full object-cover">
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metadata Tambahan: Publisher & Kategori -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-[#212121]/10">
                <div>
                    <label for="modal-input-publisher" class="block text-[11px] font-bold text-[#212121] mb-1">Publisher Resmi</label>
                    <input type="text" name="publisher" id="modal-input-publisher" placeholder="Contoh: Moonton, Garena, Riot..." class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 text-xs text-[#212121] focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 focus:border-[#6D0808] transition">
                </div>
                <div>
                    <label for="modal-input-category" class="block text-[11px] font-bold text-[#212121] mb-1">Kategori Game</label>
                    <input type="text" name="category" id="modal-input-category" placeholder="Contoh: Mobile Game, PC Game..." class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 text-xs text-[#212121] focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 focus:border-[#6D0808] transition">
                </div>
            </div>

            <!-- Modal Footer Buttons -->
            <div class="pt-3 border-t border-[#212121]/10 flex items-center justify-end space-x-2.5">
                <button type="button" onclick="closeEditGameImageModal()" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-[#212121]/70 hover:text-[#212121] bg-stone-100 hover:bg-stone-200 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs flex items-center space-x-1.5">
                    <x-icon name="check" class="w-4 h-4" />
                    <span>Simpan Gambar Game</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Galeri Semua Game (Batch Game Image Management) -->
<div id="all-games-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs hidden transition-all duration-200">
    <div id="all-games-modal-box" class="w-full max-w-3xl bg-white rounded-3xl shadow-2xl border border-[#212121]/10 overflow-hidden transform scale-95 opacity-0 transition-all duration-200 max-h-[90vh] flex flex-col">
        <!-- Header -->
        <div class="px-6 py-4 bg-[#F9F8F6] border-b border-[#212121]/10 flex items-center justify-between shrink-0">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-xl bg-[#6D0808]/10 text-[#6D0808] flex items-center justify-center shrink-0">
                    <x-icon name="image" class="w-4 h-4" />
                </div>
                <div>
                    <h3 class="text-sm font-bold text-[#212121]">Daftar Poster & Gambar Game</h3>
                    <p class="text-[11px] text-[#212121]/60 font-medium">Klik pada poster atau tombol "Ubah Gambar" untuk mengganti gambar game</p>
                </div>
            </div>
            <button type="button" onclick="closeAllGamesModal()" class="w-7 h-7 rounded-lg text-[#212121]/50 hover:text-[#212121] hover:bg-stone-200/50 flex items-center justify-center transition">
                <x-icon name="x" class="w-4 h-4" />
            </button>
        </div>

        <!-- Search Bar -->
        <div class="px-6 py-3 border-b border-[#212121]/10 bg-white shrink-0">
            <div class="relative">
                <input type="text" id="game-gallery-search" oninput="filterGalleryGames(this.value)" placeholder="Cari nama game..." class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-[#212121]/15 text-xs text-[#212121] focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 focus:border-[#6D0808]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#212121]/40">
                    <x-icon name="search" class="w-3.5 h-3.5" />
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="p-6 overflow-y-auto">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3.5" id="gallery-games-list">
                @foreach($games as $gm)
                    <div class="gallery-game-item p-3 rounded-2xl border border-stone-200 hover:border-[#6D0808]/40 hover:shadow-xs transition bg-[#F9F8F6]/40 flex flex-col items-center text-center group" data-name="{{ strtolower($gm->name) }}">
                        <div class="w-16 h-22 rounded-xl overflow-hidden bg-stone-200 border border-[#212121]/10 relative shrink-0 shadow-2xs">
                            <img src="{{ asset($gm->thumbnail ?? 'images/bg/1.jpg') }}" alt="{{ $gm->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="mt-2.5 w-full">
                            <h4 class="text-xs font-bold text-[#212121] truncate" title="{{ $gm->name }}">{{ $gm->name }}</h4>
                            <p class="text-[10px] text-[#212121]/50 truncate">{{ $gm->publisher ?? 'Publisher Resmi' }}</p>
                        </div>
                        <button type="button" 
                                onclick="openEditGameImageModal({{ $gm->id }}, '{{ addslashes($gm->name) }}', '{{ asset($gm->thumbnail ?? 'images/bg/1.jpg') }}', '{{ asset($gm->banner_image ?? '') }}', '{{ addslashes($gm->publisher ?? '') }}', '{{ addslashes($gm->category ?? '') }}'); closeAllGamesModal();"
                                class="mt-2.5 w-full py-1.5 px-2 rounded-lg text-[11px] font-semibold text-white bg-[#6D0808] hover:bg-[#540606] transition flex items-center justify-center space-x-1 shadow-2xs cursor-pointer">
                            <x-icon name="edit" class="w-3 h-3" />
                            <span>Ubah Gambar</span>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentModalProduct = null;

    function openPricingModal(product) {
        currentModalProduct = product;

        const modal = document.getElementById('pricing-modal');
        const modalBox = document.getElementById('pricing-modal-box');
        const form = document.getElementById('pricing-form');

        form.action = product.action_url;
        document.getElementById('modal-product-game').innerText = product.game;
        document.getElementById('modal-product-name').innerText = product.name;
        document.getElementById('modal-product-sku').innerText = 'SKU: ' + product.sku;
        document.getElementById('modal-cost-price-display').innerText = 'Rp ' + Number(product.cost_price).toLocaleString('id-ID');

        const originalInput = document.getElementById('modal-input-original');
        const sellInput = document.getElementById('modal-input-sell');

        originalInput.value = product.original_price ? product.original_price : '';
        sellInput.value = product.sell_price;

        calculateLivePricing();

        modal.classList.remove('hidden');
        setTimeout(() => {
            modalBox.classList.remove('scale-95', 'opacity-0');
            modalBox.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closePricingModal() {
        const modal = document.getElementById('pricing-modal');
        const modalBox = document.getElementById('pricing-modal-box');

        modalBox.classList.remove('scale-100', 'opacity-100');
        modalBox.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            currentModalProduct = null;
        }, 200);
    }

    function calculateLivePricing() {
        if (!currentModalProduct) return;

        const costPrice = Number(currentModalProduct.cost_price) || 0;
        const sellInput = document.getElementById('modal-input-sell');
        const originalInput = document.getElementById('modal-input-original');
        const saveBtn = document.getElementById('btn-save-pricing');

        const sellPrice = Number(sellInput.value) || 0;
        const originalPrice = originalInput.value ? Number(originalInput.value) : null;

        const discountDisplay = document.getElementById('calc-discount-display');
        const marginDisplay = document.getElementById('calc-margin-display');
        const badgeStatus = document.getElementById('calc-badge-status');
        const lossAlert = document.getElementById('calc-loss-alert');
        const lossText = document.getElementById('calc-loss-text');
        const originalAlert = document.getElementById('calc-original-alert');

        let isLoss = false;
        let isOriginalInvalid = false;

        // 1. Cek Proteksi Anti-Rugi: sellPrice vs costPrice
        if (sellPrice < costPrice) {
            isLoss = true;
            const deficit = costPrice - sellPrice;
            lossAlert.classList.remove('hidden');
            lossText.innerText = 'BAHAYA: Harga jual (Rp ' + sellPrice.toLocaleString('id-ID') + ') lebih rendah dari modal Digiflazz (Rp ' + costPrice.toLocaleString('id-ID') + '). Anda akan rugi -Rp ' + deficit.toLocaleString('id-ID') + ' per transaksi! Tombol simpan dinonaktifkan.';
        } else {
            lossAlert.classList.add('hidden');
        }

        // 2. Cek Logika Diskon: originalPrice vs sellPrice
        if (originalPrice !== null && originalPrice < sellPrice) {
            isOriginalInvalid = true;
            originalAlert.classList.remove('hidden');
        } else {
            originalAlert.classList.add('hidden');
        }

        // 3. Hitung Margin
        const marginNominal = sellPrice - costPrice;
        let marginPercent = costPrice > 0 ? ((marginNominal / costPrice) * 100).toFixed(1) : 0;

        if (isLoss) {
            marginDisplay.className = 'font-mono text-xs font-bold text-red-700';
            marginDisplay.innerText = '-Rp ' + Math.abs(marginNominal).toLocaleString('id-ID') + ' (' + marginPercent + '%)';
            badgeStatus.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800';
            badgeStatus.innerText = 'RUGI MODAL';
        } else {
            marginDisplay.className = 'font-mono text-xs font-bold text-emerald-700';
            marginDisplay.innerText = '+Rp ' + marginNominal.toLocaleString('id-ID') + ' (+' + marginPercent + '%)';

            if (marginPercent < 3.0) {
                badgeStatus.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800';
                badgeStatus.innerText = 'MARGIN TIPIS (<3%)';
            } else {
                badgeStatus.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800';
                badgeStatus.innerText = 'MARGIN SEHAT';
            }
        }

        // 4. Hitung Diskon
        if (originalPrice && originalPrice > sellPrice) {
            const discountNominal = originalPrice - sellPrice;
            const discountPercent = Math.round((discountNominal / originalPrice) * 100);
            discountDisplay.className = 'font-mono text-xs font-bold text-[#6D0808]';
            discountDisplay.innerText = discountPercent + '% (Hemat Rp ' + discountNominal.toLocaleString('id-ID') + ')';
        } else {
            discountDisplay.className = 'font-mono text-xs font-medium text-stone-500';
            discountDisplay.innerText = '0% (Harga Normal / Tidak Ada Promo)';
        }

        // 5. Disable/Enable Tombol Simpan
        if (isLoss || isOriginalInvalid || sellPrice <= 0) {
            saveBtn.disabled = true;
        } else {
            saveBtn.disabled = false;
        }
    }

    document.getElementById('modal-input-sell').addEventListener('input', calculateLivePricing);
    document.getElementById('modal-input-original').addEventListener('input', calculateLivePricing);

    function confirmSyncDigiflazz() {
        if (window.kaidenAlert) {
            window.kaidenAlert.confirm(
                'Sinkronisasi Katalog Digiflazz',
                'Mulai sinkronisasi katalog & update harga modal terbaru dari Digiflazz? Proses ini memakan waktu beberapa detik.',
                function() {
                    if (window.kaidenLoading) window.kaidenLoading.show('Menghubungi API Digiflazz...');
                    document.getElementById('sync-digiflazz-form').submit();
                },
                null,
                'Mulai Sinkronisasi',
                'Batal'
            );
        } else {
            if (confirm('Mulai sinkronisasi katalog & update harga modal terbaru dari Digiflazz?')) {
                document.getElementById('sync-digiflazz-form').submit();
            }
        }
    }

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePricingModal();
            closeEditGameImageModal();
            closeAllGamesModal();
        }
    });

    // Close on click outside modal box
    document.getElementById('pricing-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePricingModal();
        }
    });

    document.getElementById('game-image-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditGameImageModal();
        }
    });

    document.getElementById('all-games-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAllGamesModal();
        }
    });

    // ==========================================
    // LOGIKA KELOLA GAMBAR GAME & PRODUK
    // ==========================================
    function openEditGameImageModal(gameId, gameName, currentThumb, currentBanner, publisher, category) {
        const modal = document.getElementById('game-image-modal');
        const modalBox = document.getElementById('game-image-modal-box');
        const form = document.getElementById('game-image-form');

        form.action = "{{ url('admin/games') }}/" + gameId + "/update-image";
        document.getElementById('modal-game-title').innerText = gameName;
        document.getElementById('modal-thumb-preview').src = currentThumb;
        document.getElementById('modal-input-thumb').value = '';
        document.getElementById('modal-input-preset').value = '';
        document.getElementById('modal-input-publisher').value = publisher || '';
        document.getElementById('modal-input-category').value = category || '';

        modal.classList.remove('hidden');
        setTimeout(() => {
            modalBox.classList.remove('scale-95', 'opacity-0');
            modalBox.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeEditGameImageModal() {
        const modal = document.getElementById('game-image-modal');
        const modalBox = document.getElementById('game-image-modal-box');

        modalBox.classList.remove('scale-100', 'opacity-100');
        modalBox.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    function previewThumbnail(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('modal-thumb-preview').src = e.target.result;
                document.getElementById('modal-input-preset').value = '';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function selectPresetImage(presetPath) {
        document.getElementById('modal-input-preset').value = presetPath;
        document.getElementById('modal-input-thumb').value = '';
        document.getElementById('modal-thumb-preview').src = "{{ asset('') }}" + presetPath;
    }

    function openAllGamesModal() {
        const modal = document.getElementById('all-games-modal');
        const modalBox = document.getElementById('all-games-modal-box');
        document.getElementById('game-gallery-search').value = '';
        filterGalleryGames('');

        modal.classList.remove('hidden');
        setTimeout(() => {
            modalBox.classList.remove('scale-95', 'opacity-0');
            modalBox.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeAllGamesModal() {
        const modal = document.getElementById('all-games-modal');
        const modalBox = document.getElementById('all-games-modal-box');

        modalBox.classList.remove('scale-100', 'opacity-100');
        modalBox.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    function filterGalleryGames(query) {
        const q = (query || '').toLowerCase().trim();
        const items = document.querySelectorAll('.gallery-game-item');
        items.forEach(item => {
            const name = item.getAttribute('data-name') || '';
            if (!q || name.includes(q)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }
</script>
@endpush
@endsection
