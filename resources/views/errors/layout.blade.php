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
</head>
<body class="min-h-screen bg-[#F9F8F6] dark:bg-[#0E0E10] text-[#212121] dark:text-[#F4F4F5] font-sans antialiased flex flex-col justify-between selection:bg-[#6D0808]/15 selection:text-[#6D0808] transition-colors">

    <!-- Simple Top Bar -->
    <header class="w-full bg-[#FFFFFF] dark:bg-[#18181B] border-b border-[#212121]/10 dark:border-white/10 transition-colors">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center space-x-3">
                <img src="{{ asset('images/logo2.png') }}" alt="KAIDEN" width="130" height="32" class="h-8 w-auto object-contain dark:hidden">
                <img src="{{ asset('images/logo.png') }}" alt="KAIDEN" width="130" height="32" class="h-8 w-auto object-contain hidden dark:block">
            </a>

            <div class="flex items-center space-x-3 text-xs">
                <a href="{{ route('home') }}" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg border border-[#212121]/15 text-[#212121]/80 hover:text-[#212121] hover:bg-stone-50 transition">
                    <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Center Card -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 my-auto">
        <div class="w-full max-w-lg p-6 sm:p-8 rounded-2xl bg-white border border-[#212121]/10 shadow-xs text-center space-y-6">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full bg-white border-t border-[#212121]/10 py-4 text-center text-xs text-[#212121]/50">
        <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>&copy; {{ date('Y') }} KAIDEN. Seluruh hak cipta dilindungi.</span>
            <span>Sistem Top Up Game Online Aman &amp; Terverifikasi</span>
        </div>
    </footer>

</body>
</html>

