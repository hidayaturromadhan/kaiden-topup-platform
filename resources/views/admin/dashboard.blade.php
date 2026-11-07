@extends('layouts.admin')

@section('title', 'Dashboard Operasional - KAIDEN')
@section('header_title', 'Ringkasan Operasional & Keuangan')

@section('content')
<div class="space-y-6 pt-2">

    <!-- Saldo Deposit Digiflazz Alert / Card (RULES.md Section 5.C.4) -->
    @if($digiflazzBalance)
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start space-x-3 sm:space-x-4">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl {{ $digiflazzBalance['is_low'] ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }} flex items-center justify-center shrink-0">
                    <x-icon name="wallet" class="w-5 h-5 sm:w-6 sm:h-6" />
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                        <span class="text-xs font-semibold text-[#212121]/60 uppercase tracking-wider">Saldo Deposit Digiflazz</span>
                        @if($digiflazzBalance['is_low'])
                            <!-- Warning Badge jika saldo di bawah ambang batas (RULES.md Section 5.C.4) -->
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-300 animate-pulse">
                                <x-icon name="alert-triangle" class="w-3 h-3 text-red-700" />
                                <span>SALDO KRITIS DI BAWAH AMBANG</span>
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                Saldo Aman
                            </span>
                        @endif
                    </div>
                    <div class="font-mono tabular-nums text-xl sm:text-2xl font-bold text-[#212121] mt-1">
                        Rp {{ number_format($digiflazzBalance['deposit'], 0, ',', '.') }}
                    </div>
                    <div class="text-[11px] text-[#212121]/50 mt-0.5">
                        Ambang Batas Minimum: <span class="font-mono font-semibold">Rp {{ number_format($digiflazzBalance['threshold'], 0, ',', '.') }}</span>
                        @if(!empty($digiflazzBalance['is_mock']))
                            <span class="ml-2 text-amber-700">(Mode Simulasi Kunci Tes)</span>
                        @endif
                    </div>
                </div>
            </div>

            @if(auth()->user()->hasPermission('produk.sinkron'))
                <form action="{{ route('admin.products.sync') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-[#212121] bg-[#F9F8F6] border border-[#212121]/15 hover:bg-[#212121]/5 transition shadow-xs text-center">
                        <x-icon name="refresh" class="w-4 h-4 text-[#6D0808]" />
                        <span>Sinkron Katalog Digiflazz</span>
                    </button>
                </form>
            @endif
        </div>
    @endif

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Pendapatan Hari Ini -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-[#212121]/60">
                <span>Pendapatan Hari Ini</span>
                <x-icon name="credit-card" class="w-4 h-4 text-[#6D0808]" />
            </div>
            <div class="font-mono tabular-nums text-xl font-bold text-[#6D0808]">
                Rp {{ number_format($todayRevenue, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-[#212121]/50">Transaksi berbayar sah</div>
        </div>

        <!-- Transaksi Hari Ini -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-[#212121]/60">
                <span>Pesanan Hari Ini</span>
                <x-icon name="shopping-bag" class="w-4 h-4 text-[#212121]" />
            </div>
            <div class="font-mono tabular-nums text-xl font-bold text-[#212121]">
                {{ $todayOrdersCount }}
            </div>
            <div class="text-[11px] text-[#212121]/50">Total pesanan dibuat</div>
        </div>

        <!-- Dalam Proses / Pending -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-amber-700">
                <span>Menunggu / Proses</span>
                <x-icon name="clock" class="w-4 h-4 text-amber-600" />
            </div>
            <div class="font-mono tabular-nums text-xl font-bold text-amber-800">
                {{ $pendingCount }}
            </div>
            <div class="text-[11px] text-[#212121]/50">Perlu dipantau</div>
        </div>

        <!-- Sukses Terkirim -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-emerald-700">
                <span>Total Sukses</span>
                <x-icon name="check-circle" class="w-4 h-4 text-emerald-600" />
            </div>
            <div class="font-mono tabular-nums text-xl font-bold text-emerald-800">
                {{ $successCount }}
            </div>
            <div class="text-[11px] text-[#212121]/50">Item berhasil terkirim</div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="rounded-2xl bg-white border border-[#212121]/10 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-[#212121]/10 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-[#212121]">Transaksi Terbaru</h3>
                <p class="text-xs text-[#212121]/60">Aktivitas pemesanan teraktual yang masuk ke dalam sistem.</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-[#6D0808] hover:underline flex items-center space-x-1">
                <span>Lihat Semua</span>
                <x-icon name="chevron-right" class="w-3.5 h-3.5" />
            </a>
        </div>

        @if($recentOrders->isEmpty())
            <div class="p-8 text-center text-xs text-[#212121]/50">
                Belum ada transaksi yang tercatat.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F9F8F6] border-b border-[#212121]/10 text-[#212121]/70 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3 px-4">Kode</th>
                            <th class="py-3 px-4">Pelanggan</th>
                            <th class="py-3 px-4">Game & Produk</th>
                            <th class="py-3 px-4">Nominal</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#212121]/5">
                        @foreach($recentOrders as $order)
                            <tr class="hover:bg-[#F9F8F6]/60 transition">
                                <td class="py-3 px-4">
                                    <span class="font-mono tabular-nums font-semibold text-[#212121]">{{ $order->order_code }}</span>
                                    <div class="text-[10px] text-[#212121]/50">{{ $order->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-[#212121]">{{ $order->user->name ?? 'Guest' }}</div>
                                    <div class="text-[11px] text-[#212121]/60 font-mono">{{ $order->user_id_game }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-[#212121]">{{ $order->product->game->name }}</div>
                                    <div class="text-[11px] text-[#6D0808]">{{ $order->product->name }}</div>
                                </td>
                                <td class="py-3 px-4 font-mono tabular-nums font-bold text-[#212121]">
                                    Rp {{ number_format($order->locked_price, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4">
                                    @if(in_array($order->status, ['SUKSES', 'DIBAYAR']))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200">
                                            {{ $order->status }}
                                        </span>
                                    @elseif(in_array($order->status, ['MENUNGGU_BAYAR', 'DIPROSES']))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold text-amber-700 bg-amber-50 border border-amber-200">
                                            {{ $order->status }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold text-red-700 bg-red-50 border border-red-200">
                                            {{ $order->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold text-[#6D0808] bg-[#6D0808]/10 hover:bg-[#6D0808]/20 transition">
                                        Periksa
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

