@extends('layouts.admin')

@section('title', 'Detail Transaksi #' . $order->order_code . ' - KAIDEN')
@section('header_title', 'Detail Transaksi: ' . $order->order_code)

@section('content')
<div class="space-y-6 pt-2 max-w-5xl">

    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-[#212121]/70 hover:text-[#6D0808] transition">
            <x-icon name="arrow-left" class="w-4 h-4" />
            <span>Kembali ke Daftar Transaksi</span>
        </a>
    </div>

    <!-- Overview Card -->
    <div class="rounded-2xl bg-white border border-[#212121]/10 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-[#212121]/10 bg-[#F9F8F6] flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
            <div>
                <div class="text-xs text-[#212121]/60">Kode Transaksi</div>
                <div class="font-mono tabular-nums text-xl font-bold text-[#212121]">{{ $order->order_code }}</div>
                <div class="text-[11px] text-[#212121]/50 mt-0.5">Dibuat: {{ $order->created_at->format('d/m/Y H:i:s') }}</div>
            </div>

            <div class="flex items-center space-x-3">
                <span class="text-xs font-semibold text-[#212121]/60">Status Saat Ini:</span>
                @if(in_array($order->status, ['SUKSES', 'DIBAYAR']))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200">
                        {{ $order->status }}
                    </span>
                @elseif(in_array($order->status, ['MENUNGGU_BAYAR', 'DIPROSES']))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200">
                        {{ $order->status }}
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold text-red-700 bg-red-50 border border-red-200">
                        {{ $order->status }}
                    </span>
                @endif
            </div>
        </div>

        <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 text-xs">
            <!-- Data Pelanggan & Game -->
            <div class="space-y-4">
                <h3 class="font-bold uppercase tracking-wider text-[#212121] pb-2 border-b border-[#212121]/10">Data Pelanggan & Akun Game</h3>
                <div class="space-y-2">
                    <div class="flex justify-between py-1 border-b border-[#212121]/5">
                        <span class="text-[#212121]/60">Nama Pelanggan:</span>
                        <span class="font-semibold text-[#212121]">{{ $order->user->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#212121]/5">
                        <span class="text-[#212121]/60">Email:</span>
                        <span class="font-mono text-[#212121]">{{ $order->user->email ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#212121]/5">
                        <span class="text-[#212121]/60">Game:</span>
                        <span class="font-semibold text-[#212121]">{{ $order->product->game->name }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#212121]/5">
                        <span class="text-[#212121]/60">Item yang Dipesan:</span>
                        <span class="font-semibold text-[#6D0808]">{{ $order->product->name }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#212121]/5">
                        <span class="text-[#212121]/60">User ID Game:</span>
                        <span class="font-mono font-bold text-[#212121]">{{ $order->user_id_game }}</span>
                    </div>
                    @if($order->zone_id)
                        <div class="flex justify-between py-1 border-b border-[#212121]/5">
                            <span class="text-[#212121]/60">Zone / Server ID:</span>
                            <span class="font-mono font-bold text-[#212121]">{{ $order->zone_id }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between py-1 border-b border-[#212121]/5">
                        <span class="text-[#212121]/60">Nickname Terkonfirmasi:</span>
                        <span class="font-bold text-emerald-700">{{ $order->confirmed_nickname ?? '(Konfirmasi Manual Pelanggan)' }}</span>
                    </div>
                </div>
            </div>

            <!-- Data Finansial & Pembayaran Midtrans -->
            <div class="space-y-4">
                <h3 class="font-bold uppercase tracking-wider text-[#212121] pb-2 border-b border-[#212121]/10">Rincian Finansial & Midtrans</h3>
                <div class="space-y-2">
                    <div class="flex justify-between py-1 border-b border-[#212121]/5">
                        <span class="text-[#212121]/60">Harga Modal (Digiflazz):</span>
                        <span class="font-mono tabular-nums text-[#212121]">Rp {{ number_format($order->product->cost_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#212121]/5">
                        <span class="text-[#212121]/60">Harga Jual Terkunci (Locked):</span>
                        <span class="font-mono tabular-nums font-bold text-[#6D0808]">Rp {{ number_format($order->locked_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#212121]/5">
                        <span class="text-[#212121]/60">Midtrans Order ID:</span>
                        <span class="font-mono text-[11px] text-[#212121]">{{ $order->payment->midtrans_order_id ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#212121]/5">
                        <span class="text-[#212121]/60">Status Pembayaran:</span>
                        <span class="font-semibold capitalize text-[#212121]">{{ $order->payment->status ?? 'Belum ada' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#212121]/5">
                        <span class="text-[#212121]/60">Metode Bayar:</span>
                        <span class="font-semibold uppercase text-[#212121]">{{ $order->payment->payment_method ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#212121]/5">
                        <span class="text-[#212121]/60">Waktu Bayar:</span>
                        <span class="font-mono text-[#212121]">{{ $order->payment->paid_at ? $order->payment->paid_at->format('d/m/Y H:i:s') : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rekaman Pemenuhan Digiflazz -->
        <div class="p-4 sm:p-6 border-t border-[#212121]/10 bg-[#F9F8F6]/40 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[#212121]">Riwayat Pemenuhan Digiflazz (Fulfillments)</h3>
            @if($order->fulfillments->isEmpty())
                <div class="p-4 rounded-xl bg-white border border-[#212121]/10 text-xs text-[#212121]/50 text-center">
                    Belum ada proses pemenuhan ke Digiflazz (transaksi belum berstatus DIBAYAR).
                </div>
            @else
                <div class="space-y-2">
                    @foreach($order->fulfillments as $f)
                        <div class="p-3 sm:p-3.5 rounded-xl bg-white border border-[#212121]/10 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="font-mono font-bold text-[#212121]">{{ $f->digiflazz_ref_id }}</span>
                                <div class="text-[11px] text-[#212121]/60 mt-0.5">{{ $f->response_summary }}</div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="font-mono text-[11px] text-[#212121]/50">{{ $f->created_at->format('d/m/Y H:i') }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $f->status === 'sukses' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($f->status === 'gagal' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                                    {{ $f->status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Panel Tindak Lanjut Admin (Retry & Refund Manual via RULES.md Section 5.C.3) -->
        <div class="p-4 sm:p-6 border-t border-[#212121]/10 bg-white flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
            <div class="text-xs text-[#212121]/60">
                Aksi penanganan kegagalan atau pemenuhan ulang:
            </div>

            <div class="flex items-center space-x-3">
                @if(in_array($order->status, ['DIBAYAR', 'DIPROSES', 'GAGAL']))
                    <!-- Tombol Kirim Ulang ke Digiflazz -->
                    <form action="{{ route('admin.orders.retry', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-[#212121] bg-[#F9F8F6] border border-[#212121]/15 hover:bg-[#212121]/5 transition shadow-xs">
                            <x-icon name="refresh" class="w-3.5 h-3.5 text-[#6D0808]" />
                            <span>Kirim Ulang ke Digiflazz</span>
                        </button>
                    </form>

                    <!-- Tombol Refund Manual (PRD FR-FUL-3) -->
                    <button type="button" onclick="document.getElementById('modal-refund').classList.remove('hidden')" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-red-700 hover:bg-red-800 transition shadow-xs">
                        <x-icon name="alert-triangle" class="w-3.5 h-3.5" />
                        <span>Tandai Refund Manual Midtrans</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Refund Manual -->
<div id="modal-refund" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 border border-[#212121]/10 shadow-xl">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#212121]">Konfirmasi Refund Manual Midtrans</h3>
            <button type="button" onclick="document.getElementById('modal-refund').classList.add('hidden')" class="p-1 text-[#212121]/50 hover:text-[#212121]">
                <x-icon name="x" class="w-4 h-4" />
            </button>
        </div>

        <p class="text-xs text-[#212121]/70 leading-relaxed">
            Sesuai PRD FR-FUL-3 & RULES.md Section 5.C.3, jika Digiflazz gagal mengirim item, Admin melakukan refund melalui dashboard Midtrans lalu mencatat alasannya di sini. Tindakan ini akan dicatat secara permanen di <strong>audit_logs</strong>.
        </p>

        <form action="{{ route('admin.orders.refund', $order->id) }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label for="reason" class="block text-xs font-semibold text-[#212121] mb-1">Alasan / No. Referensi Refund</label>
                <textarea name="reason" id="reason" required rows="3" placeholder="Contoh: Stok Digiflazz gangguan permanen, refund ID: REF-99238 diproses via Midtrans Dashboard." class="w-full p-3 rounded-xl border border-[#212121]/15 text-xs focus:ring-2 focus:ring-[#6D0808]/20 focus:border-[#6D0808]"></textarea>
            </div>

            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-refund').classList.add('hidden')" class="px-3.5 py-2 rounded-xl text-xs font-semibold border border-[#212121]/15 text-[#212121]">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-red-700 text-white hover:bg-red-800">
                    Simpan & Catat Audit Log
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

