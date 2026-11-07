@extends('layouts.admin')

@section('title', 'Data Pelanggan - KAIDEN')
@section('header_title', 'Daftar Akun Pelanggan Terdaftar')

@section('content')
<div class="space-y-6 pt-2">

    <!-- Search Bar -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-[#212121]/10 shadow-xs">
        <form action="{{ route('admin.customers.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3 flex-1 max-w-md">
            <div class="relative w-full">
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama atau email pelanggan..." class="w-full pl-8 pr-3 py-2 rounded-xl border border-[#212121]/15 bg-white text-xs focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20">
                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-[#212121]/40">
                    <x-icon name="search" class="w-3.5 h-3.5" />
                </div>
            </div>
            <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#212121] hover:bg-black transition text-center">
                Cari
            </button>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="rounded-2xl bg-white border border-[#212121]/10 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F9F8F6] border-b border-[#212121]/10 text-[#212121]/70 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-4">Nama Pelanggan</th>
                        <th class="py-3 px-4">Alamat Email</th>
                        <th class="py-3 px-4">Tanggal Bergabung</th>
                        <th class="py-3 px-4">Total Pesanan</th>
                        <th class="py-3 px-4">Total Belanja (Sah)</th>
                        <th class="py-3 px-4">Status Akun</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#212121]/5">
                    @forelse($customers as $c)
                        <tr class="hover:bg-[#F9F8F6]/60 transition">
                            <td class="py-3.5 px-4 font-semibold text-[#212121]">
                                {{ $c->name }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-[#212121]/80">
                                {{ $c->email }}
                            </td>
                            <td class="py-3.5 px-4 text-[#212121]/60">
                                {{ $c->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-mono tabular-nums font-semibold text-[#212121]">
                                <a href="{{ route('admin.orders.index', ['q' => $c->email]) }}" class="hover:text-[#6D0808] hover:underline inline-flex items-center space-x-1" title="Lihat semua transaksi milik {{ $c->name }}">
                                    <span>{{ $c->orders_count }} Pesanan</span>
                                    <x-icon name="chevron-right" class="w-3 h-3 text-[#212121]/40" />
                                </a>
                            </td>
                            <td class="py-3.5 px-4 font-mono tabular-nums font-bold text-[#6D0808]">
                                Rp {{ number_format($c->total_spent ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($c->status === 'aktif')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold text-red-700 bg-red-50 border border-red-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-[#212121]/50">
                                Belum ada pelanggan yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-[#212121]/10 bg-[#F9F8F6]">
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection

