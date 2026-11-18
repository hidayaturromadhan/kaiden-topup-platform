<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kendala Akses - KAIDEN')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo2.png') }}">

    <!-- Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Anti-Flicker & Persistent Theme Initializer -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('kaiden_theme');
                const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-[#F9F8F6] dark:bg-[#0E0E10] text-[#212121] dark:text-[#F4F4F5] font-sans antialiased flex flex-col justify-between selection:bg-[#6D0808]/15 selection:text-[#6D0808] transition-colors relative overflow-x-hidden">

    <!-- Zero-Framedrop Ambient Background (Hardware Native Radial Gradient, Tanpa Blur Berat) -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_40%,rgba(109,8,8,0.06),transparent_65%)] dark:bg-[radial-gradient(circle_at_50%_40%,rgba(109,8,8,0.18),transparent_65%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#212121_1px,transparent_1px)] dark:bg-[radial-gradient(rgba(255,255,255,0.06)_1px,transparent_1px)] [background-size:24px_24px] opacity-30"></div>
    </div>

    <!-- Top Bar -->
    <header class="w-full bg-[#FFFFFF]/95 dark:bg-[#18181B]/95 border-b border-[#212121]/10 dark:border-white/10 transition-colors z-10 sticky top-0">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-14 sm:h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center space-x-3">
                <img src="{{ asset('images/logo2.png') }}" alt="KAIDEN" width="130" height="32" class="h-7 sm:h-8 w-auto object-contain dark:hidden">
                <img src="{{ asset('images/logo.png') }}" alt="KAIDEN" width="130" height="32" class="h-7 sm:h-8 w-auto object-contain hidden dark:block">
            </a>

            <div class="flex items-center space-x-3 text-xs">
                <a href="{{ route('home') }}" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl border border-[#212121]/15 dark:border-white/15 text-[#212121]/80 dark:text-zinc-300 hover:text-[#212121] dark:hover:text-white hover:bg-stone-50 dark:hover:bg-zinc-800 transition">
                    <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Center Card: Fit-to-Viewport & Responsive -->
    <main class="flex-1 flex items-center justify-center p-3.5 sm:p-5 my-auto z-10 w-full">
        <div class="w-full max-w-lg p-5 sm:p-7 rounded-2xl sm:rounded-3xl bg-white dark:bg-[#18181B] border border-[#212121]/10 dark:border-white/10 shadow-xl shadow-stone-200/40 dark:shadow-black/50 text-center">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full bg-white/90 dark:bg-[#18181B]/90 border-t border-[#212121]/10 dark:border-white/10 py-3 text-center text-xs text-[#212121]/50 dark:text-zinc-500 z-10">
        <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>&copy; {{ date('Y') }} KAIDEN. Seluruh hak cipta dilindungi.</span>
            <span>Sistem Top Up Game Online Aman &amp; Terverifikasi</span>
        </div>
    </footer>

</body>
</html>
