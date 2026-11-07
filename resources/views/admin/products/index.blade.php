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

    <!-- 1. Ringkasan Metrik & Status Proteksi Anti-Rugi -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Produk -->
        <div class="p-4 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-semibold text-[#212121]/60 uppercase tracking-wider">Total Produk</span>
                <div class="font-mono text-xl sm:text-2xl font-bold text-[#212121]">
                    {{ number_format($stats['total'] ?? 0) }}
                </div>
                <span class="text-[10px] text-[#212121]/50">Terdaftar di database</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-stone-100 flex items-center justify-center text-[#212121]/70">
                <x-icon name="database" class="w-5 h-5" />
            </div>
        </div>

        <!-- Produk Aktif -->
        <div class="p-4 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-semibold text-emerald-800/80 uppercase tracking-wider">Produk Aktif</span>
                <div class="font-mono text-xl sm:text-2xl font-bold text-emerald-700">
                    {{ number_format($stats['active'] ?? 0) }}
                </div>
                <span class="text-[10px] text-emerald-600/70">Siap dibeli pelanggan</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <x-icon name="check-circle" class="w-5 h-5" />
            </div>
        </div>

        <!-- Promo Diskon Aktif -->
        <div class="p-4 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-semibold text-[#6D0808]/80 uppercase tracking-wider">Promo Diskon</span>
                <div class="font-mono text-xl sm:text-2xl font-bold text-[#6D0808]">
                    {{ number_format($stats['discounted'] ?? 0) }}
                </div>
                <span class="text-[10px] text-[#6D0808]/70">Harga promo aktif</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#6D0808]/10 flex items-center justify-center text-[#6D0808]">
                <x-icon name="tag" class="w-5 h-5" />
            </div>
        </div>

        <!-- Anti-Loss Guard Protection -->
        <div class="p-4 rounded-2xl bg-white border border-emerald-300 shadow-xs flex items-center justify-between bg-gradient-to-br from-emerald-50/50 to-white">
            <div class="space-y-1">
                <span class="text-[11px] font-semibold text-emerald-900 uppercase tracking-wider">Proteksi Anti-Rugi</span>
                <div class="flex items-center space-x-1.5 text-emerald-700 font-bold text-base sm:text-lg">
                    <x-icon name="shield-check" class="w-5 h-5 text-emerald-600 shrink-0" />
                    <span>Aktif 100%</span>
                </div>
                <span class="text-[10px] text-emerald-800/80">Modal Digiflazz Terkunci</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-100/80 flex items-center justify-center text-emerald-700">
                <x-icon name="lock" class="w-5 h-5" />
            </div>
        </div>
    </div>

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

        <!-- Tombol Sinkronisasi Digiflazz (produk.sinkron) -->
        @if(auth()->user()->hasPermission('produk.sinkron'))
            <form action="{{ route('admin.products.sync') }}" method="POST" id="sync-digiflazz-form">
                @csrf
                <button type="button" onclick="confirmSyncDigiflazz()" class="w-full lg:w-auto inline-flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs cursor-pointer">
                    <x-icon name="refresh" class="w-4 h-4" />
                    <span>Tarik Data Digiflazz (API Sync)</span>
                </button>
            </form>
        @endif
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
                                <div class="flex items-center space-x-2">
                                    <div class="w-7 h-9 rounded bg-stone-100 overflow-hidden shrink-0 border border-[#212121]/10">
                                        <img src="{{ asset($prod->game->thumbnail ?? 'images/bg/1.jpg') }}" alt="{{ $prod->game->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <div class="font-bold text-[#212121]">{{ $prod->game->name }}</div>
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
        }
    });

    // Close on click outside modal box
    document.getElementById('pricing-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePricingModal();
        }
    });
</script>
@endpush
@endsection
