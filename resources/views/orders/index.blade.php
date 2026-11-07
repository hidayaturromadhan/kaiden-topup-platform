@extends('layouts.app')

@section('title', 'Riwayat Pesanan Saya - KAIDEN')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#212121]/10 dark:border-white/10">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-[#212121] dark:text-zinc-100">Riwayat Pesanan Saya</h1>
            <p class="text-xs text-[#212121]/60 dark:text-zinc-400">Daftar seluruh transaksi top up yang Anda lakukan di KAIDEN.</p>
        </div>

        <!-- Filter Status Tab -->
        <div class="flex items-center space-x-1.5 overflow-x-auto pb-1 sm:pb-0 -mx-4 px-4 sm:mx-0 sm:px-0">
            @php
                $statusTabs = ['SEMUA', 'MENUNGGU_BAYAR', 'DIPROSES', 'SUKSES', 'GAGAL'];
            @endphp
            @foreach($statusTabs as $tab)
                <a href="{{ route('orders.my', $tab !== 'SEMUA' ? ['status' => $tab] : []) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition {{ ($status === $tab || (!$status && $tab === 'SEMUA')) ? 'bg-[#6D0808] text-white shadow-xs' : 'bg-white text-[#212121]/70 border border-[#212121]/10 hover:text-[#212121] dark:bg-zinc-800 dark:text-zinc-300 dark:border-white/10 dark:hover:text-white' }}">
                    {{ $tab === 'SEMUA' ? 'Semua Status' : $tab }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Orders Table & Mobile Cards Card -->
    <div class="rounded-2xl bg-white dark:bg-[#18181B] border border-[#212121]/10 dark:border-white/10 shadow-xs overflow-hidden">
        @if($orders->isEmpty())
            <div class="p-8 sm:p-12 text-center space-y-3">
                <x-icon name="shopping-bag" class="w-8 h-8 text-[#212121]/30 dark:text-zinc-500 mx-auto" />
                <div class="text-sm font-semibold text-[#212121] dark:text-zinc-200">Belum ada pesanan</div>
                <p class="text-xs text-[#212121]/60 dark:text-zinc-400">Anda belum memiliki riwayat pesanan dengan status ini.</p>
                <a href="{{ route('home') }}" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs">
                    <x-icon name="gamepad" class="w-3.5 h-3.5" />
                    <span>Mulai Top Up Game</span>
                </a>
            </div>
        @else
            <!-- 1. Tampilan Mobile: Kartu Transaksi Responsif (block sm:hidden) -->
            <div class="block sm:hidden divide-y divide-[#212121]/10 dark:divide-white/10">
                @foreach($orders as $order)
                    <div class="p-4 space-y-3 hover:bg-[#F9F8F6]/60 dark:hover:bg-white/5 transition">
                        <!-- Header Kartu: Kode & Status -->
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <span class="font-mono text-xs font-bold text-[#212121] dark:text-zinc-100">{{ $order->order_code }}</span>
                                <div class="text-[10px] text-[#212121]/50 dark:text-zinc-400">{{ $order->created_at->format('d/m/Y H:i') }} WIB</div>
                            </div>
                            @if(in_array($order->status, ['SUKSES', 'DIBAYAR']))
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800">
                                    {{ $order->status }}
                                </span>
                            @elseif(in_array($order->status, ['MENUNGGU_BAYAR', 'DIPROSES']))
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800">
                                    {{ $order->status }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold text-red-700 dark:text-rose-400 bg-red-50 dark:bg-rose-950/50 border border-red-200 dark:border-rose-800">
                                    {{ $order->status }}
                                </span>
                            @endif
                        </div>

                        <!-- Info Produk & Akun Game -->
                        <div class="flex items-start space-x-3 p-2.5 rounded-xl bg-[#F9F8F6] dark:bg-zinc-800/60 border border-[#212121]/5 dark:border-white/10 text-xs">
                            <div class="w-10 h-12 rounded-lg overflow-hidden bg-stone-100 dark:bg-zinc-800 shrink-0 border border-[#212121]/10 dark:border-white/10">
                                <img src="{{ asset($order->product->game->thumbnail ?? 'images/bg/1.jpg') }}" alt="{{ $order->product->game->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-[#212121] dark:text-zinc-100 truncate">{{ $order->product->game->name }}</div>
                                <div class="text-[#6D0808] dark:text-rose-400 font-semibold text-[11px] truncate">{{ $order->product->name }}</div>
                                <div class="text-[10px] text-[#212121]/60 dark:text-zinc-400 font-mono mt-0.5">
                                    ID: {{ $order->user_id_game }} {{ $order->zone_id ? "({$order->zone_id})" : '' }}
                                    @if($order->confirmed_nickname)
                                        • <span class="text-emerald-700 dark:text-emerald-400 font-semibold">{{ $order->confirmed_nickname }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Footer Kartu: Total Harga & Tombol Detail -->
                        <div class="flex items-center justify-between pt-1">
                            <div>
                                <span class="text-[10px] text-[#212121]/50 dark:text-zinc-400 block">Total Pembayaran</span>
                                <span class="font-mono tabular-nums text-sm font-bold text-[#6D0808] dark:text-rose-400">
                                    Rp {{ number_format($order->locked_price, 0, ',', '.') }}
                                </span>
                            </div>
                            <a href="{{ route('order.show', $order->order_code) }}" class="inline-flex items-center space-x-1 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs">
                                <span>Lihat Detail</span>
                                <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- 2. Tampilan Desktop & Tablet: Tabel Lengkap (hidden sm:block) -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F9F8F6] dark:bg-zinc-800/80 border-b border-[#212121]/10 dark:border-white/10 text-[#212121]/70 dark:text-zinc-400 font-semibold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3.5 px-4">Kode Transaksi</th>
                            <th class="py-3.5 px-4">Game & Produk</th>
                            <th class="py-3.5 px-4">ID Akun Tujuan</th>
                            <th class="py-3.5 px-4">Nominal</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#212121]/5 dark:divide-white/5 font-sans">
                        @foreach($orders as $order)
                            <tr class="hover:bg-[#F9F8F6]/60 dark:hover:bg-white/5 transition">
                                <td class="py-3.5 px-4">
                                    <span class="font-mono tabular-nums font-semibold text-[#212121] dark:text-zinc-100">{{ $order->order_code }}</span>
                                    <div class="text-[10px] text-[#212121]/50 dark:text-zinc-400">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center space-x-2.5">
                                        <div class="w-8 h-10 rounded-md overflow-hidden bg-stone-100 dark:bg-zinc-800 shrink-0 border border-[#212121]/10 dark:border-white/10">
                                            <img src="{{ asset($order->product->game->thumbnail ?? 'images/bg/1.jpg') }}" alt="{{ $order->product->game->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <div class="font-semibold text-[#212121] dark:text-zinc-100">{{ $order->product->game->name }}</div>
                                            <div class="text-[#6D0808] dark:text-rose-400 text-[11px] font-medium">{{ $order->product->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-mono text-[#212121] dark:text-zinc-200">{{ $order->user_id_game }}</span>
                                    @if($order->zone_id)
                                        <span class="font-mono text-[#212121]/60 dark:text-zinc-400">({{ $order->zone_id }})</span>
                                    @endif
                                    @if($order->confirmed_nickname)
                                        <div class="text-[10px] text-emerald-700 dark:text-emerald-400 font-semibold">{{ $order->confirmed_nickname }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-mono tabular-nums font-bold text-[#212121] dark:text-zinc-100">
                                    Rp {{ number_format($order->locked_price, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if(in_array($order->status, ['SUKSES', 'DIBAYAR']))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800">
                                            {{ $order->status }}
                                        </span>
                                    @elseif(in_array($order->status, ['MENUNGGU_BAYAR', 'DIPROSES']))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800">
                                            {{ $order->status }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold text-red-700 dark:text-rose-400 bg-red-50 dark:bg-rose-950/50 border border-red-200 dark:border-rose-800">
                                            {{ $order->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('order.show', $order->order_code) }}" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-[#6D0808] dark:text-rose-400 bg-[#6D0808]/10 dark:bg-rose-950/40 hover:bg-[#6D0808]/20 transition">
                                        <span>Detail</span>
                                        <x-icon name="chevron-right" class="w-3 h-3" />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-[#212121]/10 dark:border-white/10 bg-[#F9F8F6] dark:bg-zinc-800/60">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

