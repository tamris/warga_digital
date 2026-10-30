<!DOCTYPE html>
<html lang="id" class="h-full {{ request()->cookie('theme') === 'dark' ? 'dark' : '' }}" style="{{ request()->cookie('theme') === 'dark' ? 'color-scheme: dark;' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <title>{{ $title ?? 'Sensus Warga Mandiri' }}</title>
    
    <!-- Instant Zero-FOUC Dark Mode & Livewire Wire:Navigate Guard -->
    <script>
        (function() {
            try {
                var cookieMatch = document.cookie.match(/(?:^|; )theme=([^;]*)/);
                var cookieTheme = cookieMatch ? decodeURIComponent(cookieMatch[1]) : null;
                var localTheme = localStorage.getItem('theme');
                var theme = localTheme || cookieTheme;
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var isDark = theme === 'dark' || (!theme && prefersDark);
                if (isDark) {
                    document.documentElement.classList.add('dark');
                    document.documentElement.style.colorScheme = 'dark';
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.style.colorScheme = 'light';
                }

                // Guard: Prevent Livewire 3 DOM morphing from stripping .dark during wire:navigate
                if (window.MutationObserver) {
                    var observer = new MutationObserver(function() {
                        var activeTheme = localStorage.getItem('theme') || (document.cookie.match(/(?:^|; )theme=([^;]*)/) ? decodeURIComponent(document.cookie.match(/(?:^|; )theme=([^;]*)/)[1]) : null);
                        var activeDark = activeTheme === 'dark' || (!activeTheme && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
                        if (activeDark && !document.documentElement.classList.contains('dark')) {
                            document.documentElement.classList.add('dark');
                            document.documentElement.style.colorScheme = 'dark';
                        }
                    });
                    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
                    window._themeGuardObserver = observer;
                }
            } catch (e) {}
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-50 dark:bg-[#0b0f19] text-slate-800 dark:text-slate-100 min-h-full flex flex-col font-sans antialiased selection:bg-emerald-500 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="bg-white dark:bg-[#0d1527] border-b border-slate-200/80 dark:border-slate-800/80 sticky top-0 z-40">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-base shadow-xs">
                    RW
                </div>
                <div>
                    <span class="block text-sm font-bold text-slate-900 dark:text-white leading-tight">Portal Pelayanan Warga</span>
                    <span class="block text-xs text-slate-500 dark:text-slate-400">Rukun Tetangga & Rukun Warga</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <!-- Dark Mode Toggle Button -->
                <button type="button" 
                        onclick="window.toggleTheme(event)" 
                        class="relative inline-flex items-center justify-center w-9 h-9 rounded-xl border border-slate-200 dark:border-[#223558] bg-white dark:bg-[#121c33] text-slate-600 dark:text-amber-400 hover:bg-slate-100 dark:hover:bg-[#192745] hover:border-slate-300 dark:hover:border-[#334e80] transition-all duration-200 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/40 group cursor-pointer"
                        title="Ganti Mode Tampilan (Terang / Gelap)"
                        aria-label="Ganti Mode Tampilan">
                    <!-- Sun Icon (shown when dark) -->
                    <svg class="w-4.5 h-4.5 hidden dark:block text-amber-400 transition-transform duration-300 group-hover:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <!-- Moon Icon (shown when light) -->
                    <svg class="w-4.5 h-4.5 block dark:hidden text-slate-600 transition-transform duration-300 group-hover:-rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content Slot -->
    <main class="flex-1 w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200/70 dark:border-slate-800 bg-white dark:bg-[#0f172a] py-6 mt-auto">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500 dark:text-slate-400">
            Sistem Informasi & Pendataan Kependudukan Tingkat RT / RW &bull; Data terlindungi untuk administrasi lingkungan
        </div>
    </footer>

    @livewireScripts
</body>
</html>
