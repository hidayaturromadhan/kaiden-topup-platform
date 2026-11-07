@extends('layouts.admin')

@section('title', 'Laporan Transaksi & Penjualan - KAIDEN')
@section('header_title', 'Laporan Rekapitulasi Penjualan')

@section('content')
<div class="space-y-6 pt-2">

    <!-- Filter & Export Controls -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs space-y-4">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
            <div>
                <label for="start_date" class="block text-[11px] font-semibold text-[#212121]/70 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 text-xs text-[#212121] bg-white">
            </div>

            <div>
                <label for="end_date" class="block text-[11px] font-semibold text-[#212121]/70 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 text-xs text-[#212121] bg-white">
            </div>

            <div>
                <label for="game_id" class="block text-[11px] font-semibold text-[#212121]/70 mb-1">Game</label>
                <select name="game_id" id="game_id" class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 text-xs text-[#212121] bg-white">
                    <option value="">Semua Game</option>
                    @foreach($games as $g)
                        <option value="{{ $g->id }}" {{ $gameId == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-[11px] font-semibold text-[#212121]/70 mb-1">Status</label>
                <select name="status" id="status" class="w-full px-3 py-2 rounded-xl border border-[#212121]/15 text-xs text-[#212121] bg-white">
                    <option value="SEMUA">Semua Status</option>
                    <option value="SUKSES" {{ $status === 'SUKSES' ? 'selected' : '' }}>SUKSES</option>
                    <option value="DIBAYAR" {{ $status === 'DIBAYAR' ? 'selected' : '' }}>DIBAYAR</option>
                    <option value="DIPROSES" {{ $status === 'DIPROSES' ? 'selected' : '' }}>DIPROSES</option>
                    <option value="MENUNGGU_BAYAR" {{ $status === 'MENUNGGU_BAYAR' ? 'selected' : '' }}>MENUNGGU_BAYAR</option>
                    <option value="GAGAL" {{ $status === 'GAGAL' ? 'selected' : '' }}>GAGAL</option>
                    <option value="DIBATALKAN" {{ $status === 'DIBATALKAN' ? 'selected' : '' }}>DIBATALKAN</option>
                </select>
            </div>

            <div class="sm:col-span-2 lg:col-span-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-1">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#212121] hover:bg-black transition text-center">
                    Terapkan Filter
                </button>

                @if(auth()->user()->hasPermission('laporan.ekspor'))
                    <a href="{{ route('admin.reports.export', request()->all()) }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-[#F9F8F6] bg-[#6D0808] hover:bg-[#540606] transition shadow-xs text-center">
                        <x-icon name="download" class="w-3.5 h-3.5" />
                        <span>Ekspor CSV</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Summary Statistics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-[#212121]/10 shadow-xs space-y-1">
            <span class="text-[11px] font-semibold text-[#212121]/60 uppercase tracking-wider">Total Transaksi</span>
            <div class="font-mono tabular-nums text-xl font-bold text-[#212121]">{{ $summary['total_transactions'] }}</div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-[#212121]/10 shadow-xs space-y-1">
            <span class="text-[11px] font-semibold text-[#6D0808] uppercase tracking-wider">Total Omzet (Sah)</span>
            <div class="font-mono tabular-nums text-xl font-bold text-[#6D0808]">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-[#212121]/10 shadow-xs space-y-1">
            <span class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider">Transaksi Sukses</span>
            <div class="font-mono tabular-nums text-xl font-bold text-emerald-800">{{ $summary['total_success'] }}</div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-[#212121]/10 shadow-xs space-y-1">
            <span class="text-[11px] font-semibold text-red-700 uppercase tracking-wider">Transaksi Gagal / Batal</span>
            <div class="font-mono tabular-nums text-xl font-bold text-red-800">{{ $summary['total_failed'] }}</div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="rounded-2xl bg-white border border-[#212121]/10 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F9F8F6] border-b border-[#212121]/10 text-[#212121]/70 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Kode Transaksi</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Game & Produk</th>
                        <th class="py-3 px-4">User ID Game</th>
                        <th class="py-3 px-4">Harga Terkunci</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#212121]/5">
                    @forelse($orders as $o)
                        <tr class="hover:bg-[#F9F8F6]/60 transition">
                            <td class="py-3.5 px-4 font-mono text-[11px] text-[#212121]/60">
                                {{ $o->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-semibold text-[#212121]">
                                {{ $o->order_code }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-[#212121]">{{ $o->user->name ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-[#212121]">{{ $o->product->game->name }}</span> -
                                <span class="text-[#6D0808]">{{ $o->product->name }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-[#212121]">
                                {{ $o->user_id_game }} {{ $o->zone_id ? "({$o->zone_id})" : '' }}
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-[#212121]/50">
                                Tidak ada data transaksi pada rentang periode ini.
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

