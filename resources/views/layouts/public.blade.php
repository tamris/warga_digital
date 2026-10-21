<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sensus Warga Mandiri' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-50 text-slate-800 min-h-full flex flex-col font-sans antialiased selection:bg-emerald-500 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-40">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-base shadow-xs">
                    RW
                </div>
                <div>
                    <span class="block text-sm font-bold text-slate-900 leading-tight">Portal Pelayanan Warga</span>
                    <span class="block text-xs text-slate-500">Rukun Tetangga & Rukun Warga</span>
                </div>
            </div>

            <!-- <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-700 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition">
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition">
                        Login Pengurus
                    </a>
                @endauth
            </div> -->
        </div>
    </header>

    <!-- Main Content Slot -->
    <main class="flex-1 w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200/70 bg-white py-6 mt-auto">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500">
            Sistem Informasi & Pendataan Kependudukan Tingkat RT / RW &bull; Data terlindungi untuk administrasi lingkungan
        </div>
    </footer>

    @livewireScripts
</body>
</html>
