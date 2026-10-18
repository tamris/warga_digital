<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Portal Pengelolaan Warga' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body x-data="{ mobileSidebarOpen: false }" class="bg-slate-50 text-slate-800 min-h-full font-sans antialiased selection:bg-emerald-500 selection:text-white">

    <!-- GLOBAL INSTANT LIVEWIRE LOADING BAR (0ms ZERO DELAY) -->
    <div wire:loading class="fixed top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-600 z-[9999] shadow-xs"></div>

    <!-- MOBILE SIDEBAR OVERLAY & DRAWER -->
    <div x-show="mobileSidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-slate-900/60 lg:hidden" 
         @click="mobileSidebarOpen = false" 
         style="display: none;"></div>

    <div x-show="mobileSidebarOpen" 
         x-transition:enter="transition ease-in-out duration-200 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-200 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200 flex flex-col justify-between p-5 lg:hidden"
         style="display: none;">
        
        <!-- Mobile Sidebar Header -->
        <div>
            <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-base shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center space-x-1.5">
                            <span class="text-base font-extrabold text-slate-900">WARGA DIGITAL</span>
                            </div>
                        <span class="block text-xs font-medium text-slate-500">Sistem Multi-RT</span>
                    </div>
                </div>
                <button type="button" @click="mobileSidebarOpen = false" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Mobile Nav Links -->
            <nav class="mt-6 space-y-1.5">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('houses.index') }}" wire:navigate class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('houses.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('houses.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Data Unit Rumah</span>
                </a>
                <a href="{{ route('residents.index') }}" wire:navigate class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('residents.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('residents.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Master Data Warga</span>
                </a>
                @if(auth()->user()?->role === 'admin_rw')
                <a href="{{ route('rts.index') }}" wire:navigate class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('rts.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('rts.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Master Data RT</span>
                </a>
                @endif
                <a href="{{ route('residents.export') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Unduh Excel (.xlsx)</span>
                </a>
            </nav>
        </div>

        <!-- Mobile User & Switcher -->
        <div class="pt-4 border-t border-slate-200 space-y-3">
            <div class="flex items-center space-x-3 bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                <div class="w-8 h-8 rounded-md bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">
                    {{ substr(auth()->user()->nama, 0, 1) }}
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-900 leading-tight">{{ auth()->user()->nama }}</div>
                    <div class="text-[11px] font-semibold text-emerald-700">{{ auth()->user()->role_label }}</div>
                </div>
            </div>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full h-10 px-3 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-700 text-xs font-semibold border border-slate-200 transition flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Keluar dari Sistem</span>
                </button>
            </form>
        </div>
    </div>

    <!-- DESKTOP PERMANENT SIDEBAR (LEFT) -->
    <aside class="hidden lg:flex lg:flex-col lg:w-64 xl:w-72 lg:fixed lg:inset-y-0 lg:z-40 bg-white border-r border-slate-200 justify-between">
        <!-- Top Section -->
        <div>
            <!-- Brand / Logo Header -->
            <div class="h-16 px-6 border-b border-slate-200 flex items-center">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center space-x-3 group">
                    <div class="w-9 h-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-base shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center space-x-1.5">
                            <span class="text-base font-extrabold tracking-tight text-slate-900 group-hover:text-emerald-700 transition-colors">WARGA DIGITAL</span>
                            <!-- <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">RW 05</span> -->
                        </div>
                        <span class="block text-[11px] font-medium text-slate-500">Sistem Administrasi Multi-RT</span>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="p-4 space-y-6">
                <!-- Group: Menu Utama -->
                <div>
                    <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider px-3 mb-2">Menu Utama</span>
                    <nav class="space-y-1">
                        <a href="{{ route('dashboard') }}" 
                           wire:navigate
                           class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('houses.index') }}" 
                           wire:navigate
                           class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('houses.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('houses.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span>Data Unit Rumah</span>
                        </a>

                        <a href="{{ route('residents.index') }}" 
                           wire:navigate
                           class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('residents.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('residents.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span>Master Data Warga</span>
                        </a>
                    </nav>
                </div>

                <!-- Group: Administrasi RW (Khusus Admin RW) -->
                @if(auth()->user()?->role === 'admin_rw')
                <div>
                    <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider px-3 mb-2">Administrasi Wilayah</span>
                    <nav class="space-y-1">
                        <a href="{{ route('rts.index') }}" 
                           wire:navigate
                           class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('rts.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('rts.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Data Master RT</span>
                        </a>
                    </nav>
                </div>
                @endif

                <!-- Group: Laporan -->
                <div>
                    <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider px-3 mb-2">Laporan & Arsip</span>
                    <nav class="space-y-1">
                        <a href="{{ route('residents.export') }}" 
                           class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh Excel (.xlsx)</span>
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Bottom User & Role Switcher Section -->
        <div class="p-4 border-t border-slate-200 bg-slate-50/60 space-y-3">
            @auth
                <!-- Profil Pengguna Aktif -->
                <div class="flex items-center space-x-2.5 bg-white p-2.5 rounded-lg border border-slate-200 shadow-xs">
                    <div class="w-8 h-8 rounded-md bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                        {{ substr(auth()->user()->nama, 0, 1) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->nama }}</div>
                        <div class="text-[11px] font-semibold text-emerald-700 truncate">{{ auth()->user()->role_label }}</div>
                    </div>
                </div>

                <!-- Dropdown Switch Akun Demo Cepat (Bab 2) -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Ganti Akun Demo:</label>
                    <form action="{{ route('auth.switch') }}" method="POST">
                        @csrf
                        <div class="relative">
                            <select name="user_id" onchange="this.form.submit()" title="Ganti Akun Demo" 
                                    class="w-full appearance-none bg-white hover:bg-slate-50 text-xs font-semibold text-slate-800 border border-slate-300 rounded-lg pl-3 pr-7 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer shadow-xs transition">
                                @php
                                    $demoUsers = \App\Models\User::with('rt')->orderBy('id')->get();
                                @endphp
                                @foreach($demoUsers as $du)
                                    <option value="{{ $du->id }}" {{ auth()->id() == $du->id ? 'selected' : '' }}>
                                        {{ $du->role_label }} ({{ explode(' ', $du->nama)[0] }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tombol Logout -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full h-9 px-3 rounded-lg bg-white hover:bg-rose-50 text-slate-700 hover:text-rose-700 text-xs font-semibold border border-slate-200 hover:border-rose-200 transition flex items-center justify-center space-x-1.5"
                            title="Keluar">
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Keluar Sistem</span>
                    </button>
                </form>
            @endauth
        </div>
    </aside>

    <!-- MAIN CONTENT WRAPPER (RIGHT SIDE) -->
    <div class="lg:pl-64 xl:pl-72 flex flex-col flex-1 min-h-screen">
        <!-- Top App Bar for Mobile / Status Header -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
                <!-- Left: Hamburger (Mobile) & Status Breadcrumb (Desktop) -->
                <div class="flex items-center space-x-3">
                    <button type="button" @click="mobileSidebarOpen = true" class="lg:hidden text-slate-600 hover:text-slate-900 p-1.5 rounded-lg border border-slate-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <div class="hidden sm:flex items-center space-x-2 text-xs font-medium text-slate-500">
                        <span>Portal Pengelolaan Warga</span>
                        <span class="text-slate-300">/</span>
                        <span class="font-bold text-slate-800">{{ $title ?? 'Dashboard' }}</span>
                    </div>

                    <!-- <div class="sm:hidden font-bold text-sm text-slate-900">
                        WARGA RW 05
                    </div> -->
                </div>

                <!-- Right: Active Role Pill / Quick Info -->
                <div class="flex items-center space-x-2">
                    @auth
                        <div class="flex items-center space-x-2 bg-slate-100 py-1 px-2.5 rounded-md border border-slate-200 text-xs font-semibold text-slate-700">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ auth()->user()->role_label }}</span>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs shadow-sm">
                            Masuk
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <!-- FLASH MESSAGES -->
        <div class="w-full px-4 sm:px-6 lg:px-8 mt-4">
            @if (session()->has('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 rounded-xl shadow-sm mb-4 flex items-center justify-between" role="alert">
                    <div class="flex items-center space-x-3">
                        <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 p-1 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3 rounded-xl shadow-sm mb-4 flex items-center justify-between" role="alert">
                    <div class="flex items-center space-x-3">
                        <div class="w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <span class="text-sm font-semibold">{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 p-1 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif
        </div>

        <!-- MAIN PAGE SLOT CONTENT (RESPONSIVE FULL WIDTH) -->
        <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-6">
            {{ $slot }}
        </main>

        <!-- FOOTER -->
        <footer class="bg-white border-t border-slate-200 py-5 mt-12 text-slate-500 text-xs">
            <div class="w-full px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center space-x-2">
                    <div class="w-5 h-5 rounded-md bg-emerald-600 text-white flex items-center justify-center font-black text-[10px]">RW</div>
                    <span class="font-bold text-slate-800">Portal Warga Digital</span>
                    <span class="text-slate-300">|</span>
                    <span>Sistem Administrasi Multi-RT Lingkungan</span>
                </div>
                <p class="text-slate-400">Dirancang responsif dan fleksibel untuk seluruh ukuran layar.</p>
            </div>
        </footer>
    </div>

    @livewireScripts
</body>
</html>
