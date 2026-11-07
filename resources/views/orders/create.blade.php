@extends('layouts.app')

@section('title', 'Top Up ' . $game->name . ' - KAIDEN')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 sm:pt-8">

    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-xs text-[#212121]/60 dark:text-zinc-400 mb-5 sm:mb-6">
        <a href="{{ route('home') }}" class="hover:text-[#6D0808] dark:hover:text-rose-400 transition">Katalog Game</a>
        <x-icon name="chevron-right" class="w-3 h-3" />
        <span class="text-[#212121] dark:text-zinc-200 font-semibold">{{ $game->name }}</span>
    </div>

    <!-- Layout 2 Kolom Asimetris Sesuai RULES.md Section 1.C.1 -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6">

        <!-- Kolom Kiri: Informasi Game & Status Layanan Resmi (lg:col-span-4) -->
        <div class="lg:col-span-4 space-y-4">
            <!-- Kartu Game Info & Jaminan Layanan -->
            <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#18181B] border border-[#212121]/10 dark:border-white/10 shadow-xs space-y-4">
                <div class="flex items-start space-x-3.5">
                    <div class="relative w-16 h-20 sm:w-18 sm:h-22 rounded-xl overflow-hidden border border-[#212121]/15 dark:border-white/10 shadow-xs shrink-0 bg-stone-100 dark:bg-zinc-800">
                        <img src="{{ asset($game->thumbnail ?? 'images/bg/1.jpg') }}" alt="{{ $game->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover object-center">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-[#6D0808] dark:text-rose-400">
                            {{ $game->category }}
                        </div>
                        <h1 class="text-base sm:text-lg font-bold text-[#212121] dark:text-zinc-100 mt-0.5 leading-snug truncate">{{ $game->name }}</h1>
                        <p class="text-xs text-[#212121]/60 dark:text-zinc-400 mt-0.5">
                            {{ $game->publisher ?? 'Official Publisher' }}
                        </p>
                    </div>
                </div>

                <!-- Metode Pembayaran: QRIS, GoPay, DANA, BCA, Mandiri -->
                <div class="pt-3 border-t border-[#212121]/5 dark:border-white/10">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <div class="h-6 px-2 rounded-lg bg-stone-50 dark:bg-white border border-stone-200/50 dark:border-transparent flex items-center justify-center shadow-2xs" title="QRIS">
                            <img src="{{ asset('images/payments/qris.svg') }}" alt="QRIS" class="h-3 w-auto object-contain">
                        </div>
                        <div class="h-6 px-2 rounded-lg bg-stone-50 dark:bg-white border border-stone-200/50 dark:border-transparent flex items-center justify-center shadow-2xs" title="GoPay">
                            <img src="{{ asset('images/payments/gopay.svg') }}" alt="GoPay" class="h-2.5 w-auto object-contain">
                        </div>
                        <div class="h-6 px-2 rounded-lg bg-stone-50 dark:bg-white border border-stone-200/50 dark:border-transparent flex items-center justify-center shadow-2xs" title="DANA">
                            <img src="{{ asset('images/payments/dana.svg') }}" alt="DANA" class="h-2.5 w-auto object-contain">
                        </div>
                        <div class="h-6 px-2 rounded-lg bg-stone-50 dark:bg-white border border-stone-200/50 dark:border-transparent flex items-center justify-center shadow-2xs" title="BCA">
                            <img src="{{ asset('images/payments/bca.svg') }}" alt="BCA" class="h-2 w-auto object-contain">
                        </div>
                        <div class="h-6 px-2 rounded-lg bg-stone-50 dark:bg-white border border-stone-200/50 dark:border-transparent flex items-center justify-center shadow-2xs" title="Mandiri">
                            <img src="{{ asset('images/payments/mandiri.svg') }}" alt="Mandiri" class="h-2.5 w-auto object-contain">
                        </div>
                    </div>
                </div>

                <!-- Tautan Menu Panduan Pengisian ID -->
                <div class="pt-3 border-t border-[#212121]/5 dark:border-white/10">
                    <a href="{{ route('guide.index') }}#{{ $game->slug }}" target="_blank" class="flex items-center justify-between text-xs text-[#212121]/70 dark:text-zinc-300 hover:text-[#6D0808] dark:hover:text-rose-400 group transition">
                        <span>Panduan Pengisian ID Game</span>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition" />
                    </a>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Alur Pemesanan 3 Langkah (lg:col-span-8) -->
        <div class="lg:col-span-8 space-y-6">
            <form action="{{ route('order.checkout') }}" method="POST" id="checkout-form">
                @csrf
                <input type="hidden" name="game_code" id="game_code" value="{{ $game->checker_code }}">
                <input type="hidden" name="product_id" id="selected_product_id" value="">
                <input type="hidden" name="confirmed_nickname" id="confirmed_nickname" value="">

                <!-- LANGKAH 1: Input Data Akun & Pengecekan Nickname -->
                <div class="p-4 sm:p-6 rounded-2xl bg-white dark:bg-[#18181B] border border-[#212121]/10 dark:border-white/10 shadow-xs space-y-4 sm:space-y-5">
                    <div class="flex items-center space-x-3 pb-3 border-b border-[#212121]/5 dark:border-white/10">
                        <div class="w-7 h-7 rounded-lg bg-[#6D0808] text-white flex items-center justify-center font-mono font-bold text-xs">
                            1
                        </div>
                        <h2 class="text-sm font-bold text-[#212121] dark:text-zinc-100">Masukkan Data Akun Game</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-{{ $game->needs_zone ? '12' : '1' }} gap-4">
                        <div class="{{ $game->needs_zone ? 'sm:col-span-8' : '' }} space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="user_id_game" class="block text-xs font-semibold text-[#212121] dark:text-zinc-200">
                                    User ID Game <span class="text-[#6D0808] dark:text-rose-400">*</span>
                                </label>
                                <a href="{{ route('guide.index') }}#{{ $game->slug }}" target="_blank" class="text-[11px] font-medium text-[#6D0808] dark:text-rose-400 hover:underline">
                                    Panduan Cari ID
                                </a>
                            </div>
                            <input type="text" name="user_id_game" id="user_id_game" required placeholder="Contoh: 12345678" class="w-full px-3.5 py-2.5 rounded-xl border border-[#212121]/15 dark:border-white/15 bg-white dark:bg-zinc-800 text-sm font-mono text-[#212121] dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 dark:focus:ring-rose-500/30 focus:border-[#6D0808] dark:focus:border-rose-500 transition placeholder:text-[#212121]/30 dark:placeholder:text-zinc-500">
                        </div>

                        @if($game->needs_zone)
                            <div class="sm:col-span-4 space-y-1.5">
                                <label for="zone_id" class="block text-xs font-semibold text-[#212121] dark:text-zinc-200">
                                    Zone / Server ID <span class="text-[#6D0808] dark:text-rose-400">*</span>
                                </label>
                                <input type="text" name="zone_id" id="zone_id" required placeholder="Contoh: 2001" class="w-full px-3.5 py-2.5 rounded-xl border border-[#212121]/15 dark:border-white/15 bg-white dark:bg-zinc-800 text-sm font-mono text-[#212121] dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 dark:focus:ring-rose-500/30 focus:border-[#6D0808] dark:focus:border-rose-500 transition placeholder:text-[#212121]/30 dark:placeholder:text-zinc-500">
                            </div>
                        @endif
                    </div>

                    <!-- Tombol Cek ID & Hasil Verifikasi -->
                    <div class="pt-2">
                        <div class="flex items-center space-x-3">
                            <button type="button" id="btn-check-ign" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#212121] dark:bg-zinc-800 hover:bg-black dark:hover:bg-zinc-700 transition shadow-xs border border-transparent dark:border-white/10">
                                <span>Periksa Nickname</span>
                            </button>
                            <div id="check-spinner" class="hidden text-xs text-[#212121]/60 dark:text-zinc-400 flex items-center space-x-2">
                                <span class="animate-spin inline-block w-3.5 h-3.5 border-2 border-[#6D0808] dark:border-rose-500 border-t-transparent rounded-full"></span>
                                <span>Menghubungi server game...</span>
                            </div>
                        </div>

                        <!-- Feedback Box (Dynamic via JS) -->
                        <div id="ign-result-box" class="hidden mt-3 p-3.5 rounded-xl text-xs transition"></div>

                        <!-- Fallback Checkbox (RULES.md Section 5.A.3) -->
                        <div id="manual-fallback-box" class="hidden mt-3 p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 text-xs space-y-2">
                            <div class="font-semibold flex items-center space-x-1.5 text-amber-800 dark:text-amber-300">
                                <x-icon name="alert-triangle" class="w-4 h-4 shrink-0 text-amber-700 dark:text-amber-400" />
                                <span>Pemeriksaan Nickname Otomatis Tidak Tersedia</span>
                            </div>
                            <p class="text-amber-800/80 dark:text-amber-300/80 leading-relaxed">
                                Pengecekan server game sedang mengalami kendala atau tidak merespons. Anda tetap dapat melanjutkan transaksi dengan memastikan ID sudah tepat.
                            </p>
                            <label class="flex items-start space-x-2 pt-1 cursor-pointer">
                                <input type="checkbox" name="manual_confirmation" id="manual_confirmation" value="1" class="mt-0.5 rounded border-amber-400 text-[#6D0808] focus:ring-[#6D0808]">
                                <span class="font-medium text-amber-950 dark:text-amber-200">Saya menyatakan bahwa User ID yang saya masukkan sudah benar dan siap menanggung risiko kesalahan input.</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- LANGKAH 2: Pilih Nominal Produk -->
                <div class="p-4 sm:p-6 rounded-2xl bg-white dark:bg-[#18181B] border border-[#212121]/10 dark:border-white/10 shadow-xs space-y-4 sm:space-y-5 mt-5 sm:mt-6">
                    <div class="flex items-center space-x-3 pb-3 border-b border-[#212121]/5 dark:border-white/10">
                        <div class="w-7 h-7 rounded-lg bg-[#6D0808] text-white flex items-center justify-center font-mono font-bold text-xs">
                            2
                        </div>
                        <h2 class="text-sm font-bold text-[#212121] dark:text-zinc-100">Pilih Nominal Produk</h2>
                    </div>

                    @if($products->isEmpty())
                        <div class="p-8 text-center text-xs text-[#212121]/50 dark:text-zinc-400 bg-[#F9F8F6] dark:bg-zinc-800/60 rounded-xl">
                            Belum ada produk aktif yang tersedia untuk game ini.
                        </div>
                    @else
                        <!-- Grid Kartu Nominal Sesuai RULES.md Section 1.C.1 (grid-cols-2 sm:grid-cols-3 gap-3) -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3" id="products-grid">
                            @foreach($products as $prod)
                                @php
                                    $isAvailable = !isset($prod->is_stock_available) || (bool)$prod->is_stock_available;
                                @endphp
                                <div data-product-id="{{ $prod->id }}" 
                                     data-name="{{ $prod->name }}" 
                                     data-price="{{ $prod->sell_price }}" 
                                     data-original-price="{{ $prod->original_price ?? $prod->sell_price }}"
                                     data-has-discount="{{ $prod->has_discount ? '1' : '0' }}"
                                     data-discount-percent="{{ $prod->discount_percent }}"
                                     data-discount-amount="{{ $prod->discount_amount }}"
                                     data-disabled="{{ $isAvailable ? '0' : '1' }}"
                                     data-disabled-reason="Mohon maaf, nominal ini sedang dalam pemeliharaan sistem atau pembaruan stok. Silakan pilih nominal lainnya."
                                     class="product-card relative {{ $isAvailable ? 'cursor-pointer hover:border-[#6D0808]/40 dark:hover:border-rose-500/50 hover:bg-[#6D0808]/5 dark:hover:bg-rose-950/20' : 'opacity-65 cursor-not-allowed bg-stone-100/70 dark:bg-zinc-800/40 border-dashed border-stone-300 dark:border-white/10' }} p-3.5 rounded-xl border border-[#212121]/10 dark:border-white/10 bg-white dark:bg-[#202024] transition flex flex-col justify-between space-y-2 select-none group">
                                    
                                    <div class="absolute top-2 right-2 flex items-center space-x-1">
                                        @if($prod->has_discount)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold text-[#6D0808] dark:text-rose-400 bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 shadow-2xs">
                                                -{{ $prod->discount_percent }}%
                                            </span>
                                        @endif
                                        @if(!$isAvailable)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 shadow-2xs">
                                                Pembaruan Stok
                                            </span>
                                        @endif
                                    </div>

                                    <div class="check-indicator hidden absolute top-2 {{ $prod->has_discount ? 'right-11' : 'right-2' }} w-4 h-4 rounded-full bg-[#6D0808] text-white flex items-center justify-center shadow-xs">
                                        <x-icon name="check" class="w-2.5 h-2.5" strokeWidth="2.5" />
                                    </div>

                                    <div class="text-xs font-bold text-[#212121] dark:text-zinc-100 leading-snug pr-4 {{ !$isAvailable ? 'text-stone-500 dark:text-zinc-400' : '' }}">
                                        {{ $prod->name }}
                                    </div>

                                    <div class="pt-2 border-t border-[#212121]/5 dark:border-white/10 space-y-0.5">
                                        <div class="flex items-center justify-between text-[10px] text-[#212121]/50 dark:text-zinc-400 uppercase font-mono">
                                            <span>{{ $prod->category ?? 'Item' }}</span>
                                            @if($prod->has_discount)
                                                <span class="line-through text-[#212121]/40 dark:text-zinc-500 tabular-nums">Rp {{ number_format($prod->original_price, 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                        <div class="font-mono tabular-nums text-xs font-bold {{ $isAvailable ? 'text-[#6D0808] dark:text-rose-400' : 'text-stone-400 dark:text-zinc-500' }} text-right">
                                            Rp {{ number_format($prod->sell_price, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- LANGKAH 3: Ringkasan & Checkout Midtrans -->
                <div class="p-4 sm:p-6 rounded-2xl bg-white dark:bg-[#18181B] border border-[#212121]/10 dark:border-white/10 shadow-xs space-y-4 sm:space-y-5 mt-5 sm:mt-6">
                    <div class="flex items-center space-x-3 pb-3 border-b border-[#212121]/5 dark:border-white/10">
                        <div class="w-7 h-7 rounded-lg bg-[#6D0808] text-white flex items-center justify-center font-mono font-bold text-xs">
                            3
                        </div>
                        <h2 class="text-sm font-bold text-[#212121] dark:text-zinc-100">Konfirmasi & Pembayaran</h2>
                    </div>

                    <!-- Summary Box -->
                    <div class="p-4 rounded-xl bg-[#F9F8F6] dark:bg-zinc-800/60 border border-[#212121]/10 dark:border-white/10 space-y-2.5 text-xs">
                        <div class="flex justify-between items-center text-[#212121]/70 dark:text-zinc-400">
                            <span>Game:</span>
                            <div class="flex items-center space-x-2">
                                <img src="{{ asset($game->thumbnail ?? 'images/bg/1.jpg') }}" alt="{{ $game->name }}" loading="lazy" decoding="async" class="w-4 h-5 object-cover rounded-xs border border-[#212121]/15 dark:border-white/10">
                                <span class="font-bold text-[#212121] dark:text-zinc-100">{{ $game->name }}</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center text-[#212121]/70 dark:text-zinc-400">
                            <span>Item Terpilih:</span>
                            <span id="summary-product-name" class="font-bold text-[#212121] dark:text-zinc-100">-</span>
                        </div>
                        <div class="flex justify-between items-center text-[#212121]/70 dark:text-zinc-400">
                            <span>ID Akun & Server:</span>
                            <span id="summary-user-id" class="font-mono text-[#212121] dark:text-zinc-200">-</span>
                        </div>
                        <div class="flex justify-between items-center text-[#212121]/70 dark:text-zinc-400">
                            <span>Nickname Terverifikasi:</span>
                            <span id="summary-nickname" class="font-bold text-emerald-700 dark:text-emerald-400">-</span>
                        </div>
                        <div id="summary-discount-row" class="hidden flex justify-between items-center text-[#6D0808] dark:text-rose-400 font-medium pt-1 border-t border-[#212121]/5 dark:border-white/10">
                            <span>Promo Diskon (<span id="summary-discount-percent">0%</span>):</span>
                            <span id="summary-discount-amount" class="font-mono tabular-nums font-bold">-Rp 0</span>
                        </div>
                        <div class="pt-3 border-t border-[#212121]/10 dark:border-white/10 flex justify-between items-center text-sm">
                            <span class="font-bold text-[#212121] dark:text-zinc-100">Total Pembayaran:</span>
                            <span id="summary-total-price" class="font-mono tabular-nums font-bold text-base text-[#6D0808] dark:text-rose-400">Rp 0</span>
                        </div>
                    </div>

                    <!-- Tombol Aksi Checkout Sesuai RULES.md Section 1.C.1 (bg-[#6D0808] text-[#F9F8F6]) -->
                    <div class="pt-2 space-y-3">
                        @auth
                            <button type="submit" id="btn-submit-order" class="w-full py-3.5 px-6 rounded-xl font-bold text-sm text-[#F9F8F6] bg-[#6D0808] hover:bg-[#540606] transition shadow-xs focus:ring-2 focus:ring-[#6D0808]/30 flex items-center justify-center space-x-2">
                                <x-icon name="credit-card" class="w-4 h-4" />
                                <span>Lanjut ke Pembayaran</span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="w-full py-3.5 px-6 rounded-xl font-bold text-sm text-[#F9F8F6] bg-[#6D0808] hover:bg-[#540606] transition shadow-xs text-center flex items-center justify-center space-x-2">
                                <x-icon name="lock" class="w-4 h-4" />
                                <span>Masuk untuk Melanjutkan Pemesanan</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnCheckIgn = document.getElementById('btn-check-ign');
    const inputUserId = document.getElementById('user_id_game');
    const inputZoneId = document.getElementById('zone_id');
    const gameCode = document.getElementById('game_code').value;
    const spinner = document.getElementById('check-spinner');
    const resultBox = document.getElementById('ign-result-box');
    const fallbackBox = document.getElementById('manual-fallback-box');
    const confirmedNickInput = document.getElementById('confirmed_nickname');
    const manualCheckbox = document.getElementById('manual_confirmation');

    const summaryProduct = document.getElementById('summary-product-name');
    const summaryUserId = document.getElementById('summary-user-id');
    const summaryNickname = document.getElementById('summary-nickname');
    const summaryPrice = document.getElementById('summary-total-price');
    const selectedProductIdInput = document.getElementById('selected_product_id');

    let selectedPrice = 0;
    let isIgnVerified = false;

    // Update Ringkasan ID
    function updateUserIdSummary() {
        const uid = inputUserId.value.trim();
        const zid = inputZoneId ? inputZoneId.value.trim() : '';
        if (uid) {
            summaryUserId.textContent = zid ? `${uid} (${zid})` : uid;
        } else {
            summaryUserId.textContent = '-';
        }
    }

    inputUserId.addEventListener('input', updateUserIdSummary);
    if (inputZoneId) {
        inputZoneId.addEventListener('input', updateUserIdSummary);
    }

    // Pengecekan Nickname Game via Fetch Server-Side
    btnCheckIgn.addEventListener('click', async function () {
        const userId = inputUserId.value.trim();
        const zoneId = inputZoneId ? inputZoneId.value.trim() : null;

        if (!userId) {
            if (window.kaidenToast) {
                window.kaidenToast.warning('Silakan masukkan User ID Game terlebih dahulu.');
            } else {
                alert('Silakan masukkan User ID Game terlebih dahulu.');
            }
            inputUserId.focus();
            return;
        }

        if (inputZoneId && !zoneId) {
            if (window.kaidenToast) {
                window.kaidenToast.warning('Zone / Server ID wajib diisi untuk game ini.');
            } else {
                alert('Zone / Server ID wajib diisi untuk game ini.');
            }
            inputZoneId.focus();
            return;
        }

        btnCheckIgn.disabled = true;
        spinner.classList.remove('hidden');
        resultBox.classList.add('hidden');
        fallbackBox.classList.add('hidden');

        try {
            const response = await fetch('{{ route('order.check_ign') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    game_code: gameCode,
                    user_id: userId,
                    zone_id: zoneId
                })
            });

            const data = await response.json();

            if (data.success && data.nickname) {
                isIgnVerified = true;
                confirmedNickInput.value = data.nickname;
                summaryNickname.textContent = data.nickname;

                if (window.kaidenToast) {
                    window.kaidenToast.success('Nickname terverifikasi: ' + data.nickname);
                }

                resultBox.className = 'mt-3 p-3.5 rounded-xl text-xs bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between';
                resultBox.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <div>
                            <span class="text-emerald-700/80 dark:text-emerald-400/80">Nickname Terverifikasi:</span>
                            <span class="font-bold text-emerald-950 dark:text-emerald-200 font-mono text-sm ml-1">${data.nickname}</span>
                            ${data.cached ? '<span class="text-[10px] text-emerald-600/70 dark:text-emerald-400/70 ml-1.5">(Cache Valid)</span>' : ''}
                        </div>
                    </div>
                `;
                resultBox.classList.remove('hidden');
            } else {
                // Skenario Checker Gagal / Server Sibuk / Fallback
                isIgnVerified = false;
                confirmedNickInput.value = '';
                summaryNickname.textContent = 'Menunggu Konfirmasi Manual';

                if (window.kaidenToast) {
                    window.kaidenToast.warning('Pemeriksaan otomatis tidak tersedia. Silakan centang konfirmasi manual.');
                }

                fallbackBox.classList.remove('hidden');
            }
        } catch (e) {
            isIgnVerified = false;
            fallbackBox.classList.remove('hidden');
            summaryNickname.textContent = 'Menunggu Konfirmasi Manual';
        } finally {
            btnCheckIgn.disabled = false;
            spinner.classList.add('hidden');
        }
    });

    // Pemilihan Kartu Nominal Produk Sesuai RULES.md Section 1.C.1
    // Selected card: ring-2 ring-[#6D0808] bg-[#6D0808]/5
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.addEventListener('click', function () {
            // Cegah pemilihan jika produk sedang dalam pemeliharaan/stok belum siap
            if (this.getAttribute('data-disabled') === '1') {
                const reason = this.getAttribute('data-disabled-reason') || 'Mohon maaf, nominal ini sedang dalam pemeliharaan sistem atau pembaruan stok. Silakan pilih nominal lainnya.';
                if (window.kaidenToast) {
                    window.kaidenToast.warning(reason);
                } else {
                    alert(reason);
                }
                return;
            }

            productCards.forEach(c => {
                c.classList.remove('ring-2', 'ring-[#6D0808]', 'bg-[#6D0808]/5', 'dark:bg-rose-950/20', 'dark:border-rose-500');
                c.classList.add('border-[#212121]/10', 'dark:border-white/10', 'bg-white', 'dark:bg-[#202024]');
                const chk = c.querySelector('.check-indicator');
                if (chk) chk.classList.add('hidden');
            });

            this.classList.remove('border-[#212121]/10', 'dark:border-white/10', 'bg-white', 'dark:bg-[#202024]');
            this.classList.add('ring-2', 'ring-[#6D0808]', 'bg-[#6D0808]/5', 'dark:bg-rose-950/20', 'dark:border-rose-500');
            const myChk = this.querySelector('.check-indicator');
            if (myChk) myChk.classList.remove('hidden');

            const prodId = this.getAttribute('data-product-id');
            const prodName = this.getAttribute('data-name');
            const prodPrice = parseInt(this.getAttribute('data-price'));
            const hasDiscount = this.getAttribute('data-has-discount') === '1';
            const discountPercent = this.getAttribute('data-discount-percent');
            const discountAmount = parseInt(this.getAttribute('data-discount-amount') || '0');

            selectedProductIdInput.value = prodId;
            selectedPrice = prodPrice;

            summaryProduct.textContent = prodName;
            summaryPrice.textContent = 'Rp ' + prodPrice.toLocaleString('id-ID');

            const discountRow = document.getElementById('summary-discount-row');
            const discountPercentSpan = document.getElementById('summary-discount-percent');
            const discountAmountSpan = document.getElementById('summary-discount-amount');

            if (discountRow) {
                if (hasDiscount && discountAmount > 0) {
                    discountRow.classList.remove('hidden');
                    discountPercentSpan.textContent = '-' + discountPercent + '%';
                    discountAmountSpan.textContent = '-Rp ' + discountAmount.toLocaleString('id-ID');
                } else {
                    discountRow.classList.add('hidden');
                }
            }
        });
    });

    // Auto-select product jika diarahkan dari Katalog Produk (?product_id=...)
    const urlParams = new URLSearchParams(window.location.search);
    const preselectedId = urlParams.get('product_id');
    if (preselectedId) {
        const targetCard = document.querySelector(`.product-card[data-product-id="${preselectedId}"]`);
        if (targetCard) {
            targetCard.click();
            targetCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    // Validasi Form Sebelum Submit
    const checkoutForm = document.getElementById('checkout-form');
    checkoutForm.addEventListener('submit', function (e) {
        if (!selectedProductIdInput.value) {
            e.preventDefault();
            if (window.kaidenToast) {
                window.kaidenToast.warning('Silakan pilih salah satu nominal produk pada Langkah 2.');
            } else {
                alert('Silakan pilih salah satu nominal produk pada Langkah 2.');
            }
            return;
        }

        if (!inputUserId.value.trim()) {
            e.preventDefault();
            if (window.kaidenToast) {
                window.kaidenToast.warning('User ID Game wajib diisi.');
            } else {
                alert('User ID Game wajib diisi.');
            }
            inputUserId.focus();
            return;
        }

        // Jika IGN belum terverifikasi, wajibkan centang konfirmasi manual (RULES.md Section 5.A.3)
        if (!isIgnVerified) {
            if (!manualCheckbox || !manualCheckbox.checked) {
                e.preventDefault();
                if (window.kaidenAlert) {
                    window.kaidenAlert.warning(
                        'Konfirmasi ID Diperlukan',
                        'Pengecekan nickname belum terverifikasi. Harap lakukan "Periksa Nickname" atau centang pernyataan konfirmasi manual pada Langkah 1 sebelum melanjutkan.',
                        'Saya Mengerti'
                    );
                } else {
                    alert('Pengecekan nickname belum terverifikasi. Harap lakukan "Periksa Nickname" atau centang pernyataan konfirmasi manual pada Langkah 1 sebelum melanjutkan.');
                }
                fallbackBox.classList.remove('hidden');
                return;
            }
        }
    });
});
</script>
@endpush

