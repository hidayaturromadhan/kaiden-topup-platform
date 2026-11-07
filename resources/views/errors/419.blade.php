@extends('errors.layout')

@section('title', '419 - Sesi Kedaluwarsa | KAIDEN')

@section('content')
<div class="space-y-4">
    <!-- Status Code & Icon -->
    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 mb-2">
        <x-icon name="refresh" class="w-7 h-7 text-amber-700" />
    </div>

    <div class="space-y-1.5">
        <div class="font-mono tabular-nums text-xs font-bold uppercase tracking-wider text-amber-800">
            HTTP 419 • Sesi Kedaluwarsa
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-[#212121] tracking-tight">
            Sesi Keamanan Telah Berakhir
        </h1>
        <p class="text-xs sm:text-sm text-[#212121]/70 leading-relaxed max-w-md mx-auto">
            Halaman ini tidak aktif dalam waktu yang lama sehingga token keamanan CSRF kedaluwarsa. Silakan muat ulang halaman untuk memperbarui sesi.
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
        <button type="button" onclick="window.location.reload()" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs cursor-pointer">
            <x-icon name="refresh" class="w-4 h-4" />
            <span>Muat Ulang Halaman</span>
        </button>

        <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-[#212121] bg-white border border-[#212121]/15 hover:bg-stone-50 transition">
            <x-icon name="user" class="w-4 h-4" />
            <span>Masuk Kembali</span>
        </a>
    </div>
</div>
@endsection

