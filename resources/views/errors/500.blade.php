@extends('errors.layout')

@section('title', '500 - Kesalahan Server | KAIDEN')

@section('content')
<div class="space-y-4">
    <!-- Status Code & Icon -->
    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 mb-2">
        <x-icon name="alert-triangle" class="w-7 h-7 text-amber-700" />
    </div>

    <div class="space-y-1.5">
        <div class="font-mono tabular-nums text-xs font-bold uppercase tracking-wider text-amber-800">
            HTTP 500 • Internal Server Error
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-[#212121] tracking-tight">
            Terjadi Kesalahan pada Server
        </h1>
        <p class="text-xs sm:text-sm text-[#212121]/70 leading-relaxed max-w-md mx-auto">
            Sistem kami sedang mengalami kendala teknis internal. Tim operasional KAIDEN telah menerima notifikasi perbaikan. Mohon coba beberapa saat lagi.
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="{{ route('home') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs">
            <x-icon name="arrow-left" class="w-4 h-4" />
            <span>Kembali ke Beranda</span>
        </a>

        <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number', env('WHATSAPP_NUMBER', '6281234567890')) }}?text={{ urlencode('Halo CS KAIDEN, saya mengalami kendala teknis (HTTP 500) saat mengakses situs.') }}" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-emerald-700 hover:bg-emerald-800 transition shadow-xs">
            <x-icon name="headphones" class="w-4 h-4" />
            <span>Customer Care WhatsApp</span>
        </a>
    </div>
</div>
@endsection

