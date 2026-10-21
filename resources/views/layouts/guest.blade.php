<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Masuk - Portal Warga RW' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-50 text-slate-800 min-h-full flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 font-sans antialiased">
    <div class="w-full {{ $maxWidth ?? 'max-w-lg' }} my-auto">
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
