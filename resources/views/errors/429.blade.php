@extends('errors.layout')

@section('title', '429 - Terlalu Banyak Permintaan | KAIDEN')

@section('content')
<div class="space-y-4">
    <!-- Status Code & Icon -->
    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 mb-2">
        <x-icon name="clock" class="w-7 h-7 text-amber-700" />
    </div>

    <div class="space-y-1.5">
        <div class="font-mono tabular-nums text-xs font-bold uppercase tracking-wider text-amber-800">
            HTTP 429 • Batas Frekuensi Terlampaui
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-[#212121] tracking-tight">
            Terlalu Banyak Permintaan
        </h1>
        <p class="text-xs sm:text-sm text-[#212121]/70 leading-relaxed max-w-md mx-auto">
            Sistem mendeteksi terlalu banyak permintaan dalam waktu singkat dari perangkat Anda. Sesuai kebijakan keamanan KAIDEN, permintaan dibatasi sementara untuk melindungi sistem.
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="{{ route('home') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs">
            <x-icon name="arrow-left" class="w-4 h-4" />
            <span>Kembali ke Beranda</span>
        </a>

        <button type="button" onclick="window.location.reload()" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-[#212121] bg-white border border-[#212121]/15 hover:bg-stone-50 transition cursor-pointer">
            <x-icon name="refresh" class="w-4 h-4" />
            <span>Coba Lagi</span>
        </button>
    </div>
</div>
@endsection

