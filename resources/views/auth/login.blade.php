@extends('layouts.app')

@section('title', 'Masuk Akun - KAIDEN')

@section('content')
<div class="max-w-md mx-auto px-3 sm:px-4 pt-6 sm:pt-12">
    <div class="rounded-2xl bg-white dark:bg-[#18181B] border border-[#212121]/10 dark:border-white/10 shadow-xs p-5 sm:p-8 space-y-5 sm:space-y-6">
        <!-- Header -->
        <div class="text-center space-y-2">
            <a href="{{ route('home') }}" class="inline-block">
                <img src="{{ asset('images/logo2.png') }}" alt="KAIDEN Logo" class="h-12 w-auto mx-auto mb-2 object-contain hover:opacity-90 transition dark:hidden">
                <img src="{{ asset('images/logo.png') }}" alt="KAIDEN Logo" class="h-12 w-auto mx-auto mb-2 object-contain hover:opacity-90 transition hidden dark:block">
            </a>
            <h1 class="text-xl font-bold tracking-tight text-[#212121] dark:text-zinc-100">Masuk ke Akun</h1>
            <p class="text-xs text-[#212121]/60 dark:text-zinc-400">Gunakan akun Anda untuk melakukan pemesanan dan melihat riwayat transaksi.</p>
        </div>

        <!-- Form -->
        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-semibold text-[#212121] dark:text-zinc-200">Alamat Email</label>
                <input type="email" name="email" id="email" required autofocus value="{{ old('email') }}" placeholder="nama@email.com" class="w-full px-3.5 py-2.5 rounded-xl border border-[#212121]/15 dark:border-white/15 bg-white dark:bg-zinc-800 text-sm text-[#212121] dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 dark:focus:ring-rose-500/30 focus:border-[#6D0808] dark:focus:border-rose-500 transition placeholder:text-[#212121]/30 dark:placeholder:text-zinc-500">
                @error('email')
                    <p class="text-[11px] text-red-600 dark:text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-xs font-semibold text-[#212121] dark:text-zinc-200">Kata Sandi</label>
                </div>
                <input type="password" name="password" id="password" required placeholder="••••••••" class="w-full px-3.5 py-2.5 rounded-xl border border-[#212121]/15 dark:border-white/15 bg-white dark:bg-zinc-800 text-sm text-[#212121] dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 dark:focus:ring-rose-500/30 focus:border-[#6D0808] dark:focus:border-rose-500 transition placeholder:text-[#212121]/30 dark:placeholder:text-zinc-500">
                @error('password')
                    <p class="text-[11px] text-red-600 dark:text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-[#212121]/20 dark:border-white/20 text-[#6D0808] focus:ring-[#6D0808]">
                    <span class="text-xs text-[#212121]/70 dark:text-zinc-400">Ingat sesi saya</span>
                </label>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 rounded-xl font-semibold text-xs text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs focus:ring-2 focus:ring-[#6D0808]/30">
                Masuk Sekarang
            </button>
        </form>

        <!-- Akun Demo Cepat untuk Pengujian Skripsi / Verifikasi RBAC -->
        <div class="p-3.5 rounded-xl bg-[#F9F8F6] dark:bg-zinc-800/60 border border-[#212121]/10 dark:border-white/10 space-y-2">
            <div class="text-[11px] font-bold uppercase tracking-wider text-[#212121]/70 dark:text-zinc-400">Akun Pengujian RBAC:</div>
            <div class="space-y-1 text-xs">
                <button type="button" onclick="fillCreds('superadmin@kaiden.id', 'Password123!')" class="w-full text-left px-2 py-1 rounded bg-white dark:bg-zinc-800 border border-[#212121]/10 dark:border-white/10 hover:border-[#6D0808] dark:hover:border-rose-500 flex items-center justify-between transition">
                    <span class="font-mono text-[11px] text-[#212121] dark:text-zinc-200">superadmin@kaiden.id</span>
                    <span class="text-[10px] font-semibold text-[#6D0808] dark:text-rose-400">Superadmin</span>
                </button>
                <button type="button" onclick="fillCreds('admin@kaiden.id', 'Password123!')" class="w-full text-left px-2 py-1 rounded bg-white dark:bg-zinc-800 border border-[#212121]/10 dark:border-white/10 hover:border-[#6D0808] dark:hover:border-rose-500 flex items-center justify-between transition">
                    <span class="font-mono text-[11px] text-[#212121] dark:text-zinc-200">admin@kaiden.id</span>
                    <span class="text-[10px] font-semibold text-[#212121] dark:text-zinc-300">Admin</span>
                </button>
                <button type="button" onclick="fillCreds('pelanggan@kaiden.id', 'Password123!')" class="w-full text-left px-2 py-1 rounded bg-white dark:bg-zinc-800 border border-[#212121]/10 dark:border-white/10 hover:border-[#6D0808] dark:hover:border-rose-500 flex items-center justify-between transition">
                    <span class="font-mono text-[11px] text-[#212121] dark:text-zinc-200">pelanggan@kaiden.id</span>
                    <span class="text-[10px] font-semibold text-emerald-700 dark:text-emerald-400">Pelanggan</span>
                </button>
            </div>
            <div class="text-[10px] text-[#212121]/50 dark:text-zinc-500 text-center">Kata sandi default: <span class="font-mono font-bold text-[#212121] dark:text-zinc-300">Password123!</span></div>
        </div>

        <!-- Footer Form -->
        <div class="text-center pt-2 border-t border-[#212121]/10 dark:border-white/10 text-xs text-[#212121]/70 dark:text-zinc-400">
            Belum memiliki akun?
            <a href="{{ route('register') }}" class="font-semibold text-[#6D0808] dark:text-rose-400 hover:underline ml-1">Daftar Akun Baru</a>
        </div>
    </div>
</div>

<script>
function fillCreds(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}
</script>
@endsection

