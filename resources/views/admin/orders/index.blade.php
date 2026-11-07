@extends('layouts.admin')

@section('title', 'Manajemen Seluruh Transaksi - KAIDEN')
@section('header_title', 'Daftar Seluruh Transaksi Pelanggan')

@section('content')
<div class="space-y-6 pt-2">

    <!-- Filter Bar -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2.5 sm:gap-3">
            <!-- Filter Status -->
            <select name="status" onchange="this.form.submit()" class="w-full sm:w-auto px-3 py-2 rounded-xl border border-[#212121]/15 bg-white text-xs text-[#212121] focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20">
                <option value="SEMUA">Semua Status</option>
                <option value="MENUNGGU_BAYAR" {{ $status === 'MENUNGGU_BAYAR' ? 'selected' : '' }}>MENUNGGU_BAYAR</option>
                <option value="DIBAYAR" {{ $status === 'DIBAYAR' ? 'selected' : '' }}>DIBAYAR</option>
                <option value="DIPROSES" {{ $status === 'DIPROSES' ? 'selected' : '' }}>DIPROSES</option>
                <option value="SUKSES" {{ $status === 'SUKSES' ? 'selected' : '' }}>SUKSES</option>
                <option value="GAGAL" {{ $status === 'GAGAL' ? 'selected' : '' }}>GAGAL</option>
                <option value="KEDALUWARSA" {{ $status === 'KEDALUWARSA' ? 'selected' : '' }}>KEDALUWARSA</option>
                <option value="DIBATALKAN" {{ $status === 'DIBATALKAN' ? 'selected' : '' }}>DIBATALKAN</option>
            </select>

            <!-- Filter Game -->
            <select name="game_id" onchange="this.form.submit()" class="w-full sm:w-auto px-3 py-2 rounded-xl border border-[#212121]/15 bg-white text-xs text-[#212121] focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20">
                <option value="">Semua Game</option>
                @foreach($games as $g)
                    <option value="{{ $g->id }}" {{ $gameId == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                @endforeach
            </select>

            <!-- Search -->
            <div class="relative w-full sm:flex-1 sm:min-w-[200px]">
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari kode order, ID game, email..." class="w-full pl-8 pr-3 py-2 rounded-xl border border-[#212121]/15 bg-white text-xs focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20">
                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-[#212121]/40">
                    <x-icon name="search" class="w-3.5 h-3.5" />
                </div>
            </div>

            <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#212121] hover:bg-black transition">
                Terapkan
            </button>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="rounded-2xl bg-white border border-[#212121]/10 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F9F8F6] border-b border-[#212121]/10 text-[#212121]/70 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-4">Kode Transaksi</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Game & Item</th>
                        <th class="py-3 px-4">ID Akun Tujuan</th>
                        <th class="py-3 px-4">Harga Terkunci</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#212121]/5">
                    @forelse($orders as $o)
                        <tr class="hover:bg-[#F9F8F6]/60 transition">
                            <td class="py-3.5 px-4">
                                <span class="font-mono tabular-nums font-semibold text-[#212121]">{{ $o->order_code }}</span>
                                <div class="text-[10px] text-[#212121]/50">{{ $o->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-[#212121]">{{ $o->user->name ?? 'Guest' }}</div>
                                <div class="text-[11px] text-[#212121]/60">{{ $o->user->email ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-[#212121]">{{ $o->product->game->name }}</div>
                                <div class="text-[#6D0808] text-[11px]">{{ $o->product->name }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-mono text-[#212121] font-semibold">{{ $o->user_id_game }} {{ $o->zone_id ? "({$o->zone_id})" : '' }}</div>
                                @if($o->confirmed_nickname)
                                    <div class="text-[10px] text-emerald-700 font-medium">IGN: {{ $o->confirmed_nickname }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono tabular-nums font-bold text-[#212121]">
                                Rp {{ number_format($o->locked_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if(in_array($o->status, ['SUKSES', 'DIBAYAR']))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200">
                                        {{ $o->status }}
                                    </span>
                                @elseif(in_array($o->status, ['MENUNGGU_BAYAR', 'DIPROSES']))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold text-amber-700 bg-amber-50 border border-amber-200">
                                        {{ $o->status }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold text-red-700 bg-red-50 border border-red-200">
                                        {{ $o->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('admin.orders.show', $o->id) }}" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-[#6D0808] bg-[#6D0808]/10 hover:bg-[#6D0808]/20 transition">
                                    <span>Periksa</span>
                                    <x-icon name="chevron-right" class="w-3 h-3" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-[#212121]/50">
                                Tidak ada transaksi yang sesuai kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-[#212121]/10 bg-[#F9F8F6]">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection

