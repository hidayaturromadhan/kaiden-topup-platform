@extends('layouts.app')

@section('title', 'Daftar Akun Baru - KAIDEN')

@section('content')
<div class="max-w-md mx-auto px-3 sm:px-4 pt-6 sm:pt-12">
    <div class="rounded-2xl bg-white dark:bg-[#18181B] border border-[#212121]/10 dark:border-white/10 shadow-xs p-5 sm:p-8 space-y-5 sm:space-y-6">
        <!-- Header -->
        <div class="text-center space-y-2">
            <a href="{{ route('home') }}" class="inline-block">
                <img src="{{ asset('images/logo2.png') }}" alt="KAIDEN Logo" class="h-12 w-auto mx-auto mb-2 object-contain hover:opacity-90 transition dark:hidden">
                <img src="{{ asset('images/logo.png') }}" alt="KAIDEN Logo" class="h-12 w-auto mx-auto mb-2 object-contain hover:opacity-90 transition hidden dark:block">
            </a>
            <h1 class="text-xl font-bold tracking-tight text-[#212121] dark:text-zinc-100">Daftar Akun Pelanggan</h1>
            <p class="text-xs text-[#212121]/60 dark:text-zinc-400">Buat akun untuk melakukan transaksi top up game dan melacak status pesanan Anda.</p>
        </div>

        <!-- Form -->
        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-semibold text-[#212121] dark:text-zinc-200">Nama Lengkap</label>
                <input type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="Nama Anda" class="w-full px-3.5 py-2.5 rounded-xl border border-[#212121]/15 dark:border-white/15 bg-white dark:bg-zinc-800 text-sm text-[#212121] dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 dark:focus:ring-rose-500/30 focus:border-[#6D0808] dark:focus:border-rose-500 transition placeholder:text-[#212121]/30 dark:placeholder:text-zinc-500">
                @error('name')
                    <p class="text-[11px] text-red-600 dark:text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-semibold text-[#212121] dark:text-zinc-200">Alamat Email</label>
                <input type="email" name="email" id="email" required value="{{ old('email') }}" placeholder="nama@email.com" class="w-full px-3.5 py-2.5 rounded-xl border border-[#212121]/15 dark:border-white/15 bg-white dark:bg-zinc-800 text-sm text-[#212121] dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 dark:focus:ring-rose-500/30 focus:border-[#6D0808] dark:focus:border-rose-500 transition placeholder:text-[#212121]/30 dark:placeholder:text-zinc-500">
                @error('email')
                    <p class="text-[11px] text-red-600 dark:text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-semibold text-[#212121] dark:text-zinc-200">Kata Sandi</label>
                <div class="relative">
                    <input type="password" 
                           name="password" 
                           id="password" 
                           required 
                           placeholder="Minimal 8 karakter" 
                           class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-[#212121]/15 dark:border-white/15 bg-white dark:bg-zinc-800 text-sm text-[#212121] dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 dark:focus:ring-rose-500/30 focus:border-[#6D0808] dark:focus:border-rose-500 transition placeholder:text-[#212121]/30 dark:placeholder:text-zinc-500">
                    <button type="button" 
                            onclick="togglePasswordVisibility('password', 'eyeIconPass', 'eyeOffIconPass', this)" 
                            aria-label="Lihat kata sandi" 
                            title="Lihat kata sandi"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#212121]/40 dark:text-zinc-400 hover:text-[#6D0808] dark:hover:text-rose-400 focus:outline-none transition-colors cursor-pointer">
                        <span id="eyeIconPass">
                            <x-icon name="eye" class="w-4 h-4" />
                        </span>
                        <span id="eyeOffIconPass" class="hidden">
                            <x-icon name="eye-off" class="w-4 h-4" />
                        </span>
                    </button>
                </div>
                @error('password')
                    <p class="text-[11px] text-red-600 dark:text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-xs font-semibold text-[#212121] dark:text-zinc-200">Konfirmasi Kata Sandi</label>
                <div class="relative">
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation" 
                           required 
                           placeholder="Ulangi kata sandi" 
                           class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-[#212121]/15 dark:border-white/15 bg-white dark:bg-zinc-800 text-sm text-[#212121] dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 dark:focus:ring-rose-500/30 focus:border-[#6D0808] dark:focus:border-rose-500 transition placeholder:text-[#212121]/30 dark:placeholder:text-zinc-500">
                    <button type="button" 
                            onclick="togglePasswordVisibility('password_confirmation', 'eyeIconConf', 'eyeOffIconConf', this)" 
                            aria-label="Lihat konfirmasi kata sandi" 
                            title="Lihat konfirmasi kata sandi"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#212121]/40 dark:text-zinc-400 hover:text-[#6D0808] dark:hover:text-rose-400 focus:outline-none transition-colors cursor-pointer">
                        <span id="eyeIconConf">
                            <x-icon name="eye" class="w-4 h-4" />
                        </span>
                        <span id="eyeOffIconConf" class="hidden">
                            <x-icon name="eye-off" class="w-4 h-4" />
                        </span>
                    </button>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl font-semibold text-xs text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs focus:ring-2 focus:ring-[#6D0808]/30">
                    Daftar Sebagai Pelanggan
                </button>
            </div>
        </form>

        <!-- Footer -->
        <div class="text-center pt-2 border-t border-[#212121]/10 dark:border-white/10 text-xs text-[#212121]/70 dark:text-zinc-400">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-semibold text-[#6D0808] dark:text-rose-400 hover:underline ml-1">Masuk Sekarang</a>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, eyeIconId, eyeOffIconId, btn) {
    const input = document.getElementById(inputId);
    const eyeIcon = document.getElementById(eyeIconId);
    const eyeOffIcon = document.getElementById(eyeOffIconId);

    if (!input) return;

    if (input.type === 'password') {
        input.type = 'text';
        if (eyeIcon) eyeIcon.classList.add('hidden');
        if (eyeOffIcon) eyeOffIcon.classList.remove('hidden');
        if (btn) {
            btn.setAttribute('aria-label', 'Sembunyikan kata sandi');
            btn.setAttribute('title', 'Sembunyikan kata sandi');
        }
    } else {
        input.type = 'password';
        if (eyeIcon) eyeIcon.classList.remove('hidden');
        if (eyeOffIcon) eyeOffIcon.classList.add('hidden');
        if (btn) {
            btn.setAttribute('aria-label', 'Lihat kata sandi');
            btn.setAttribute('title', 'Lihat kata sandi');
        }
    }
}
</script>
@endsection

