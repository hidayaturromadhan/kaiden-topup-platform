@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_code . ' - KAIDEN')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 sm:pt-8">

    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-xs text-[#212121]/60 dark:text-zinc-400 mb-5 sm:mb-6">
        <a href="{{ route('home') }}" class="hover:text-[#6D0808] dark:hover:text-rose-400 transition">Beranda</a>
        <x-icon name="chevron-right" class="w-3 h-3" />
        <a href="{{ route('orders.my') }}" class="hover:text-[#6D0808] dark:hover:text-rose-400 transition">Riwayat Pesanan</a>
        <x-icon name="chevron-right" class="w-3 h-3" />
        <span class="text-[#212121] dark:text-zinc-200 font-mono font-semibold">{{ $order->order_code }}</span>
    </div>

    <!-- Flash Session Messages -->
    @if(session('success'))
        <div class="mb-5 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-300 flex items-center space-x-2">
            <x-icon name="check-circle" class="w-4 h-4 text-emerald-600 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 p-4 rounded-xl bg-red-50 dark:bg-rose-950/40 border border-red-200 dark:border-rose-800 text-xs text-red-800 dark:text-rose-300 flex items-center space-x-2">
            <x-icon name="alert-circle" class="w-4 h-4 text-red-600 shrink-0" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Main Card -->
    <div class="rounded-2xl bg-white dark:bg-[#18181B] border border-[#212121]/10 dark:border-white/10 shadow-xs overflow-hidden">
        <!-- Header Pesanan -->
        <div class="p-4 sm:p-6 border-b border-[#212121]/10 dark:border-white/10 bg-[#F9F8F6] dark:bg-zinc-800/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
            <div>
                <div class="text-xs text-[#212121]/60 dark:text-zinc-400">Kode Transaksi</div>
                <div class="font-mono tabular-nums text-lg font-bold text-[#212121] dark:text-zinc-100 flex items-center space-x-2">
                    <span>{{ $order->order_code }}</span>
                    <button type="button" 
                            onclick="copyOrderCode('{{ $order->order_code }}')" 
                            class="p-1 rounded-md hover:bg-stone-200 dark:hover:bg-zinc-700 text-stone-500 hover:text-stone-800 dark:hover:text-white transition cursor-pointer" 
                            title="Salin Kode Transaksi">
                        <x-icon name="copy" class="w-3.5 h-3.5" />
                    </button>
                </div>
                <div class="text-[11px] text-[#212121]/50 dark:text-zinc-400 mt-0.5">{{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</div>
            </div>

            <!-- Status Badge Sesuai RULES.md Section 1.A -->
            <div>
                @if(in_array($order->status, ['SUKSES', 'DIBAYAR']))
                    <span class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-full text-xs font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800">
                        <x-icon name="check-circle" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                        <span>{{ $order->status }}</span>
                    </span>
                @elseif(in_array($order->status, ['MENUNGGU_BAYAR', 'DIPROSES']))
                    <span class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-full text-xs font-semibold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800">
                        <x-icon name="clock" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                        <span>{{ $order->status }}</span>
                    </span>
                @else
                    <span class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-full text-xs font-semibold text-red-700 dark:text-rose-400 bg-red-50 dark:bg-rose-950/50 border border-red-200 dark:border-rose-800">
                        <x-icon name="alert-circle" class="w-4 h-4 text-red-600 dark:text-rose-400" />
                        <span>{{ $order->status }}</span>
                    </span>
                @endif
            </div>
        </div>

        <!-- Detail Pesanan -->
        <div class="p-4 sm:p-6 space-y-5 sm:space-y-6">
            <!-- Informasi Akun Game & Item -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-[#F9F8F6] dark:bg-zinc-800/40 border border-[#212121]/10 dark:border-white/10 text-xs">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-14 rounded-lg overflow-hidden border border-[#212121]/15 dark:border-white/10 bg-stone-100 dark:bg-zinc-800 shrink-0">
                        <img src="{{ asset($order->product->game->thumbnail ?? 'images/bg/1.jpg') }}" alt="{{ $order->product->game->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <div class="text-[#212121]/60 dark:text-zinc-400">Game & Item</div>
                        <div class="font-bold text-sm text-[#212121] dark:text-zinc-100 mt-0.5">{{ $order->product->game->name }}</div>
                        <div class="text-[#6D0808] dark:text-rose-400 font-semibold mt-0.5">{{ $order->product->name }}</div>
                    </div>
                </div>

                <div>
                    <div class="text-[#212121]/60 dark:text-zinc-400">Tujuan Akun</div>
                    <div class="font-mono text-sm text-[#212121] dark:text-zinc-100 font-semibold mt-0.5">
                        {{ $order->user_id_game }} {{ $order->zone_id ? "({$order->zone_id})" : '' }}
                    </div>
                    <div class="text-[11px] text-[#212121]/70 dark:text-zinc-400 mt-0.5">
                        Nickname: <span class="font-bold text-emerald-700 dark:text-emerald-400">{{ $order->confirmed_nickname ?? '(Konfirmasi Manual)' }}</span>
                    </div>
                </div>
            </div>

            <!-- Rincian Harga Terkunci (Locked Price) -->
            <div class="space-y-3 pt-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#212121] dark:text-zinc-200">Rincian Pembayaran</h3>
                <div class="space-y-2 text-xs border-b border-[#212121]/10 dark:border-white/10 pb-4">
                    <div class="flex justify-between items-center text-[#212121]/70 dark:text-zinc-400">
                        <span>Harga Produk (Locked Price):</span>
                        <span class="font-mono tabular-nums font-semibold text-[#212121] dark:text-zinc-100">
                            Rp {{ number_format($order->locked_price, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-[#212121]/70 dark:text-zinc-400">
                        <span>Biaya Layanan Gerbang Pembayaran:</span>
                        <span class="font-mono tabular-nums text-emerald-700 dark:text-emerald-400 font-semibold">Gratis (Rp 0)</span>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-1">
                    <span class="text-sm font-bold text-[#212121] dark:text-zinc-100">Total Tagihan:</span>
                    <span class="font-mono tabular-nums text-xl font-bold text-[#6D0808] dark:text-rose-400">
                        Rp {{ number_format($order->locked_price, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Tombol Bayar / Integrasi Snap Midtrans -->
            @if($order->status === 'MENUNGGU_BAYAR')
                <div class="p-4 sm:p-6 rounded-xl bg-[#6D0808]/5 dark:bg-rose-950/20 border border-[#6D0808]/20 dark:border-rose-900/40 space-y-4 text-center">
                    <div>
                        <h4 class="text-sm font-bold text-[#212121] dark:text-zinc-100">Menunggu Pembayaran</h4>
                        <p class="text-xs text-[#212121]/70 dark:text-zinc-400 mt-1">
                            Selesaikan pembayaran melalui berbagai channel resmi Midtrans (QRIS, GoPay, ShopeePay, Virtual Account).
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-center gap-3">
                        <button type="button" id="pay-button" class="inline-flex items-center justify-center space-x-2 px-6 py-3 rounded-xl font-bold text-sm text-[#F9F8F6] bg-[#6D0808] hover:bg-[#540606] transition shadow-xs focus:ring-2 focus:ring-[#6D0808]/30">
                            <x-icon name="credit-card" class="w-4 h-4" />
                            <span>Bayar Sekarang via Midtrans</span>
                        </button>
                        <a href="{{ route('order.show', $order->order_code) }}" class="inline-flex items-center justify-center space-x-1.5 px-4 py-3 rounded-xl font-semibold text-xs text-[#212121] dark:text-zinc-200 bg-white dark:bg-zinc-800 border border-[#212121]/15 dark:border-white/10 hover:bg-[#212121]/5 dark:hover:bg-zinc-700 transition shadow-xs">
                            <x-icon name="refresh" class="w-3.5 h-3.5" />
                            <span>Cek Status Pembayaran</span>
                        </a>
                    </div>

                    <div class="flex flex-wrap items-center justify-center gap-3 pt-1">
                        <img src="{{ asset('images/payments/qris.svg') }}" alt="QRIS" class="h-3.5 w-auto object-contain bg-white dark:bg-white/95 px-1 py-0.5 rounded shadow-2xs" loading="lazy">
                        <img src="{{ asset('images/payments/gopay.svg') }}" alt="GoPay" class="h-3 w-auto object-contain bg-white dark:bg-white/95 px-1 py-0.5 rounded shadow-2xs" loading="lazy">
                        <img src="{{ asset('images/payments/dana.svg') }}" alt="DANA" class="h-3 w-auto object-contain bg-white dark:bg-white/95 px-1 py-0.5 rounded shadow-2xs" loading="lazy">
                        <img src="{{ asset('images/payments/bca.svg') }}" alt="BCA" class="h-2.5 w-auto object-contain bg-white dark:bg-white/95 px-1 py-0.5 rounded shadow-2xs" loading="lazy">
                        <img src="{{ asset('images/payments/mandiri.svg') }}" alt="Mandiri" class="h-2.5 w-auto object-contain bg-white dark:bg-white/95 px-1 py-0.5 rounded shadow-2xs" loading="lazy">
                        <img src="{{ asset('images/payments/indomaret.svg') }}" alt="Indomaret" class="h-3 w-auto object-contain bg-white dark:bg-white/95 px-1 py-0.5 rounded shadow-2xs" loading="lazy">
                    </div>
                </div>
            @endif

            <!-- Riwayat Pemenuhan Digiflazz -->
            @if($order->fulfillments->isNotEmpty())
                <div class="pt-4 border-t border-[#212121]/10 dark:border-white/10 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#212121] dark:text-zinc-200">Informasi Pengiriman Item (Digiflazz)</h3>
                    @foreach($order->fulfillments as $ful)
                        <div class="p-3.5 rounded-xl bg-[#F9F8F6] dark:bg-zinc-800/60 border border-[#212121]/10 dark:border-white/10 text-xs space-y-1.5 font-mono">
                            <div class="flex justify-between">
                                <span class="text-[#212121]/60 dark:text-zinc-400">Ref ID Digiflazz:</span>
                                <span class="font-bold text-[#212121] dark:text-zinc-100">{{ $ful->digiflazz_ref_id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#212121]/60 dark:text-zinc-400">Status Item:</span>
                                <span class="font-bold {{ $ful->status === 'sukses' ? 'text-emerald-700 dark:text-emerald-400' : ($ful->status === 'gagal' ? 'text-red-700 dark:text-rose-400' : 'text-amber-700 dark:text-amber-400') }}">
                                    {{ strtoupper($ful->status) }}
                                </span>
                            </div>
                            @if($ful->response_summary)
                                <div class="text-[11px] text-[#212121]/70 dark:text-zinc-400 pt-1 border-t border-[#212121]/5 dark:border-white/10">
                                    Catatan: {{ $ful->response_summary }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Notes jika ada -->
            @if($order->notes)
                <div class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-xs text-amber-900 dark:text-amber-200 whitespace-pre-line">
                    <div class="font-bold text-amber-950 dark:text-amber-100 mb-1">Catatan Tindak Lanjut:</div>
                    {{ $order->notes }}
                </div>
            @endif

            <!-- Ulasan & Rating Pelanggan Setelah Pesanan Selesai -->
            @if(in_array($order->status, ['SUKSES', 'DIBAYAR']))
                <div class="pt-4 border-t border-[#212121]/10 dark:border-white/10 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#212121] dark:text-zinc-200">
                            Feedback &amp; Rating Layanan
                        </h3>
                        @if($order->review)
                            <span class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 flex items-center space-x-1">
                                <x-icon name="check-circle" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                                <span>Sudah Diulas</span>
                            </span>
                        @endif
                    </div>

                    @if($order->review)
                        <!-- Tampilan Review yang Sudah Dikirim -->
                        <div class="p-4 sm:p-5 rounded-2xl bg-[#F9F8F6] dark:bg-zinc-800/60 border border-[#212121]/10 dark:border-white/10 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-1 text-amber-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <x-icon name="star" class="w-4 h-4 {{ $i <= $order->review->rating ? 'text-amber-400' : 'text-stone-300 dark:text-zinc-600' }}" fill="currentColor" />
                                    @endfor
                                    <span class="ml-2 font-mono text-xs font-bold text-[#212121] dark:text-zinc-200">{{ $order->review->rating }}.0 / 5.0</span>
                                </div>
                                <span class="text-[11px] text-[#212121]/50 dark:text-zinc-400">
                                    {{ $order->review->created_at->diffForHumans() }}
                                </span>
                            </div>

                            @if($order->review->comment)
                                <p class="text-xs text-[#212121]/80 dark:text-zinc-300 leading-relaxed italic">
                                    “{{ $order->review->comment }}”
                                </p>
                            @else
                                <p class="text-xs text-[#212121]/50 dark:text-zinc-400 italic">
                                    (Pelanggan hanya memberikan rating bintang tanpa komentar tertulis)
                                </p>
                            @endif

                            @if($order->review->admin_reply)
                                <div class="p-3 rounded-xl bg-white dark:bg-zinc-900 border border-[#212121]/10 dark:border-white/10 space-y-1 text-xs">
                                    <span class="font-bold text-[#6D0808] dark:text-rose-400">Tanggapan Admin KAIDEN:</span>
                                    <p class="text-[#212121]/70 dark:text-zinc-400">{{ $order->review->admin_reply }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Form Kirim Review Baru untuk Pemilik Pesanan -->
                        <form action="{{ route('orders.review.store', $order->order_code) }}" method="POST" class="p-4 sm:p-5 rounded-2xl bg-[#F9F8F6] dark:bg-zinc-800/60 border border-[#212121]/10 dark:border-white/10 space-y-4">
                            @csrf
                            <div>
                                <h4 class="text-sm font-bold text-[#212121] dark:text-zinc-100">Bagaimana Pengalaman Top Up Anda?</h4>
                                <p class="text-xs text-[#212121]/60 dark:text-zinc-400 mt-0.5">
                                    Pesanan Anda telah selesai. Berikan rating bintang dan ulasan untuk membantu meningkatkan kualitas layanan kami.
                                </p>
                            </div>

                            <!-- Interactive Star Rating Input -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-semibold text-[#212121] dark:text-zinc-200">
                                    Pilih Rating Bintang <span class="text-[#6D0808] dark:text-rose-400">*</span>
                                </label>
                                <div class="flex items-center space-x-1.5" id="rating-stars-wrapper">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" class="star-btn cursor-pointer p-0.5 transition" data-star="{{ $i }}" title="{{ $i }} Bintang">
                                            <x-icon name="star" class="w-6 h-6 star-svg text-amber-400" fill="currentColor" />
                                        </button>
                                    @endfor
                                    <input type="hidden" name="rating" id="rating-val" value="{{ old('rating', 5) }}">
                                    <span id="rating-label" class="ml-2 font-mono font-bold text-xs text-amber-600 dark:text-amber-400">Sangat Puas (5 Bintang)</span>
                                </div>
                                @error('rating')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Comment Input -->
                            <div class="space-y-1.5">
                                <label for="comment" class="block text-xs font-semibold text-[#212121] dark:text-zinc-200">
                                    Ulasan Pengalaman (Opsional)
                                </label>
                                <textarea name="comment" id="comment" rows="3" maxlength="500" placeholder="Ceritakan kecepatan proses, kemudahan pembayaran, atau tanggapan Anda..." class="w-full px-3.5 py-2.5 rounded-xl border border-[#212121]/15 dark:border-white/15 bg-white dark:bg-zinc-800 text-xs text-[#212121] dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 dark:focus:ring-rose-500/30 focus:border-[#6D0808] dark:focus:border-rose-500 transition placeholder:text-[#212121]/30 dark:placeholder:text-zinc-500">{{ old('comment') }}</textarea>
                                @error('comment')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Anonymous Toggle & Submit Button -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
                                <label class="inline-flex items-center space-x-2 text-xs text-[#212121]/70 dark:text-zinc-400 cursor-pointer">
                                    <input type="checkbox" name="is_anonymous" value="1" class="rounded border-stone-300 text-[#6D0808] focus:ring-[#6D0808]/30">
                                    <span>Tampilkan ulasan sebagai anonim (disamarkan)</span>
                                </label>

                                <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl font-bold text-xs text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs">
                                    <span>Kirim Ulasan &amp; Rating</span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            @endif

            <!-- Bantuan Layanan Pelanggan WhatsApp (wa.me) -->
            <div class="p-4 rounded-xl bg-[#F9F8F6] dark:bg-zinc-800/60 border border-[#212121]/10 dark:border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                <div class="flex items-center space-x-3 text-left w-full sm:w-auto">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <x-icon name="headphones" class="w-4 h-4" />
                    </div>
                    <div>
                        <div class="font-bold text-[#212121] dark:text-zinc-100">Butuh Bantuan Terkait Pesanan Ini?</div>
                        <div class="text-[11px] text-[#212121]/60 dark:text-zinc-400">Tim Customer Care siap membantu pengecekan status secara langsung.</div>
                    </div>
                </div>
                <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number', env('WHATSAPP_NUMBER', '6281234567890')) }}?text={{ urlencode('Halo Admin KAIDEN, saya butuh bantuan untuk pesanan #' . $order->order_code . ' (' . $order->product->game->name . ' - ' . $order->product->name . '). Status: ' . $order->status) }}" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="w-full sm:w-auto px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-emerald-700 hover:bg-emerald-800 transition flex items-center justify-center space-x-1.5 shrink-0 shadow-xs">
                    <x-icon name="headphones" class="w-3.5 h-3.5" />
                    <span>Chat WhatsApp CS</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.copyOrderCode = function(code) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(code).then(() => {
            if (window.kaidenToast) {
                window.kaidenToast.success('Kode pesanan ' + code + ' berhasil disalin!');
            }
        }).catch(() => {
            if (window.kaidenToast) window.kaidenToast.info('Kode: ' + code);
        });
    } else {
        if (window.kaidenToast) window.kaidenToast.info('Kode: ' + code);
    }
};

document.addEventListener('DOMContentLoaded', function () {
    const starBtns = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('rating-val');
    const ratingLabel = document.getElementById('rating-label');
    const labels = {
        1: 'Kecewa (1 Bintang)',
        2: 'Kurang Puas (2 Bintang)',
        3: 'Cukup (3 Bintang)',
        4: 'Puas (4 Bintang)',
        5: 'Sangat Puas (5 Bintang)'
    };

    function updateStars(val) {
        if (!ratingInput) return;
        ratingInput.value = val;
        if (ratingLabel && labels[val]) {
            ratingLabel.textContent = labels[val];
        }
        starBtns.forEach(btn => {
            const starVal = parseInt(btn.getAttribute('data-star'));
            const svg = btn.querySelector('.star-svg');
            if (svg) {
                if (starVal <= val) {
                    svg.classList.remove('text-stone-300', 'dark:text-zinc-600');
                    svg.classList.add('text-amber-400');
                } else {
                    svg.classList.remove('text-amber-400');
                    svg.classList.add('text-stone-300', 'dark:text-zinc-600');
                }
            }
        });
    }

    starBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const val = parseInt(this.getAttribute('data-star'));
            updateStars(val);
        });
    });

    if (ratingInput && ratingInput.value) {
        updateStars(parseInt(ratingInput.value));
    }
});
</script>
@if($order->status === 'MENUNGGU_BAYAR' && $order->payment && $order->payment->snap_token)
    <script src="{{ $snapJsUrl }}" data-client-key="{{ $clientKey }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const payButton = document.getElementById('pay-button');
        if (payButton) {
            payButton.addEventListener('click', function () {
                window.snap.pay('{{ $order->payment->snap_token }}', {
                    onSuccess: function (result) {
                        window.location.reload();
                    },
                    onPending: function (result) {
                        window.location.reload();
                    },
                    onError: function (result) {
                        if (window.kaidenAlert) {
                            window.kaidenAlert.error('Pembayaran Gagal', 'Pembayaran transaksi gagal atau dibatalkan.', 'Tutup');
                        } else {
                            alert('Pembayaran gagal atau dibatalkan.');
                        }
                        setTimeout(() => window.location.reload(), 1500);
                    },
                    onClose: function () {
                        // User menutup popup tanpa bayar
                    }
                });
            });
        }
    });
    </script>
@endif
@endpush

