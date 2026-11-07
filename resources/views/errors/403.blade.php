@extends('errors.layout')

@section('title', '403 - Akses Ditolak | KAIDEN')

@section('content')
<div class="space-y-4">
    <!-- Status Code & Icon -->
    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-red-50 border border-red-200 text-[#6D0808] mb-2">
        <x-icon name="lock" class="w-7 h-7" />
    </div>

    <div class="space-y-1.5">
        <div class="font-mono tabular-nums text-xs font-bold uppercase tracking-wider text-[#6D0808]">
            HTTP 403 • Akses Tidak Diizinkan
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-[#212121] tracking-tight">
            Akses Ditolak
        </h1>
        <p class="text-xs sm:text-sm text-[#212121]/70 leading-relaxed max-w-md mx-auto">
            {{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses untuk membuka halaman atau data ini. Sesuai kebijakan Role-Based Access Control (RBAC) KAIDEN, seluruh akses diawasi dan dibatasi secara ketat.' }}
        </p>
    </div>

    <!-- Security Information Box -->
    <div class="p-3.5 rounded-xl bg-[#F9F8F6] border border-[#212121]/10 text-left text-xs text-[#212121]/70 space-y-1">
        <div class="flex items-center space-x-1.5 font-semibold text-[#212121]">
            <x-icon name="shield" class="w-3.5 h-3.5 text-[#6D0808]" />
            <span>Pemeriksaan Keamanan &amp; Otorisasi</span>
        </div>
        <p class="text-[11px] text-[#212121]/60">
            Jika Anda adalah staf operasional dan memerlukan izin ke menu ini, hubungi Superadmin untuk pembaruan izin peran.
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="{{ route('home') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs">
            <x-icon name="arrow-left" class="w-4 h-4" />
            <span>Kembali ke Beranda</span>
        </a>

        @auth
            <a href="{{ route('orders.my') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-[#212121] bg-white border border-[#212121]/15 hover:bg-stone-50 transition">
                <x-icon name="shopping-bag" class="w-4 h-4" />
                <span>Riwayat Pesanan Saya</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-[#212121] bg-white border border-[#212121]/15 hover:bg-stone-50 transition">
                <x-icon name="user" class="w-4 h-4" />
                <span>Masuk ke Akun</span>
            </a>
        @endauth
    </div>
</div>
@endsection

