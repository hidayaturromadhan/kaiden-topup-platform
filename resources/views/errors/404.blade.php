@extends('errors.layout')

@section('title', '404 - Halaman Tidak Ditemukan | KAIDEN')

@section('content')
<div class="space-y-4">
    <!-- Status Code & Icon -->
    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-stone-100 border border-stone-200 text-[#212121] mb-2">
        <x-icon name="search" class="w-7 h-7 text-[#212121]/70" />
    </div>

    <div class="space-y-1.5">
        <div class="font-mono tabular-nums text-xs font-bold uppercase tracking-wider text-[#212121]/60">
            HTTP 404 • Not Found
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-[#212121] tracking-tight">
            Halaman Tidak Ditemukan
        </h1>
        <p class="text-xs sm:text-sm text-[#212121]/70 leading-relaxed max-w-md mx-auto">
            Halaman, pesanan, atau data yang Anda cari tidak tersedia, telah dihapus, atau tautan yang Anda masukkan salah.
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="{{ route('home') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs">
            <x-icon name="arrow-left" class="w-4 h-4" />
            <span>Kembali ke Beranda</span>
        </a>

        <a href="{{ route('home') }}#katalog-game" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-[#212121] bg-white border border-[#212121]/15 hover:bg-stone-50 transition">
            <x-icon name="gamepad" class="w-4 h-4" />
            <span>Katalog Game Populer</span>
        </a>
    </div>
</div>
@endsection

