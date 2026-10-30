<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="dark light">
    <title>{{ $title ?? 'Masuk - Portal Warga RW' }}</title>
    
    <!-- Instant Zero-FOUC Dark Mode Initializer -->
    <script>
        (function() {
            try {
                var theme = localStorage.getItem('theme');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (theme === 'dark' || (!theme && prefersDark)) {
                    document.documentElement.classList.add('dark');
                    document.documentElement.style.colorScheme = 'dark';
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.style.colorScheme = 'light';
                }
            } catch (e) {}
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#0b1325] text-slate-800 dark:text-slate-100 min-h-screen flex flex-col justify-center items-center py-6 sm:py-10 px-4 sm:px-6 lg:px-8 font-['Plus_Jakarta_Sans',sans-serif] antialiased relative overflow-x-hidden selection:bg-emerald-500 selection:text-white">
    <!-- Ambient Radial Mesh Glows (Sesuai Referensi Visual) -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10">
        <div class="absolute -top-32 -left-32 w-[480px] h-[480px] bg-emerald-600/20 rounded-full blur-[140px]"></div>
        <div class="absolute -bottom-32 -right-32 w-[480px] h-[480px] bg-teal-500/20 rounded-full blur-[140px]"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-sky-900/15 rounded-full blur-[160px]"></div>
    </div>

    <div class="w-full max-w-4xl my-auto">
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
