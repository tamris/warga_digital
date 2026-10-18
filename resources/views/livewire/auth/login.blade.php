<div>
    <!-- Kartu Login Modern Enterprise SaaS (Sesuai Bab 1 baru.md) -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 sm:p-10">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-600 text-white font-bold mb-3 shadow-sm">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Portal Pengelolaan Warga</h1>
            <p class="text-sm text-slate-600 mt-1 font-medium">Sistem Informasi Wilayah Rukun Warga</p>
        </div>

        @if (session()->has('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-lg mb-6 font-semibold text-sm flex items-center space-x-2">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Formulir Masuk -->
        <form wire:submit.prevent="login" class="space-y-5">
            <div>
                <label for="username" class="block text-sm font-semibold text-slate-800 tracking-wide uppercase mb-1.5">
                    Nama Pengguna (Username)
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <input type="text" id="username" wire:model="username" 
                           placeholder="Masukkan username Anda..."
                           class="w-full h-12 pl-11 pr-4 rounded-lg border border-slate-300 text-slate-900 text-base focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition @error('username') border-rose-500 @enderror">
                </div>
                @error('username')
                    <p class="mt-1.5 text-xs font-bold text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-800 tracking-wide uppercase mb-1.5">
                    Kata Sandi (Password)
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <input type="password" id="password" wire:model="password" 
                           placeholder="Masukkan kata sandi..."
                           class="w-full h-12 pl-11 pr-4 rounded-lg border border-slate-300 text-slate-900 text-base focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition @error('password') border-rose-500 @enderror">
                </div>
                @error('password')
                    <p class="mt-1.5 text-xs font-bold text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label for="remember" class="flex items-center cursor-pointer">
                    <input type="checkbox" id="remember" wire:model="remember" class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                    <span class="ml-2 text-sm font-medium text-slate-700">Ingat sesi saya</span>
                </label>
                <span class="text-xs text-slate-500 font-medium">Sistem Enkripsi Aman</span>
            </div>

            <button type="submit" 
                    class="w-full h-12 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-base rounded-lg shadow-sm transition duration-150 flex items-center justify-center space-x-2">
                <span wire:loading.remove>Masuk ke Portal</span>
                <span wire:loading class="flex items-center space-x-2">
                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <span>Memeriksa Akun...</span>
                </span>
            </button>
        </form>

        <!-- Bagian Masuk Cepat / Demo Akun -->
        <div class="mt-8 pt-6 border-t border-slate-200">
            <div class="flex items-center justify-center space-x-2 mb-4">
                <span class="h-px w-8 bg-slate-200"></span>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Akses Uji Coba Cepat (1 Klik)</span>
                <span class="h-px w-8 bg-slate-200"></span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                <button type="button" wire:click="quickLogin('admin_rw')" 
                        class="p-2.5 text-left bg-slate-50 hover:bg-slate-100 border border-slate-200 hover:border-slate-300 rounded-lg transition duration-150 group">
                    <div class="flex items-center space-x-2 mb-0.5">
                        <span class="w-5 h-5 rounded bg-slate-900 text-white flex items-center justify-center text-[10px] font-bold">RW</span>
                        <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-700">Admin RW</span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-medium truncate">AMAT MAJA</div>
                </button>

                <button type="button" wire:click="quickLogin('ketua_rt01')" 
                        class="p-2.5 text-left bg-slate-50 hover:bg-slate-100 border border-slate-200 hover:border-slate-300 rounded-lg transition duration-150 group">
                    <div class="flex items-center space-x-2 mb-0.5">
                        <span class="w-5 h-5 rounded bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold">01</span>
                        <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-700">Ketua RT 001</span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-medium truncate">AGUNG JATMIKO</div>
                </button>

                <button type="button" wire:click="quickLogin('ketua_rt02')" 
                        class="p-2.5 text-left bg-slate-50 hover:bg-slate-100 border border-slate-200 hover:border-slate-300 rounded-lg transition duration-150 group">
                    <div class="flex items-center space-x-2 mb-0.5">
                        <span class="w-5 h-5 rounded bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold">02</span>
                        <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-700">Ketua RT 002</span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-medium truncate">DENY</div>
                </button>

                <button type="button" wire:click="quickLogin('ketua_rt03')" 
                        class="p-2.5 text-left bg-slate-50 hover:bg-slate-100 border border-slate-200 hover:border-slate-300 rounded-lg transition duration-150 group">
                    <div class="flex items-center space-x-2 mb-0.5">
                        <span class="w-5 h-5 rounded bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold">03</span>
                        <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-700">Ketua RT 003</span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-medium truncate">MUKHID</div>
                </button>

                <button type="button" wire:click="quickLogin('ketua_rt04')" 
                        class="p-2.5 text-left bg-slate-50 hover:bg-slate-100 border border-slate-200 hover:border-slate-300 rounded-lg transition duration-150 group">
                    <div class="flex items-center space-x-2 mb-0.5">
                        <span class="w-5 h-5 rounded bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold">04</span>
                        <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-700">Ketua RT 004</span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-medium truncate">SUBANDI</div>
                </button>

                <button type="button" wire:click="quickLogin('sekretaris_rt01')" 
                        class="p-2.5 text-left bg-slate-50 hover:bg-slate-100 border border-slate-200 hover:border-slate-300 rounded-lg transition duration-150 group">
                    <div class="flex items-center space-x-2 mb-0.5">
                        <span class="w-5 h-5 rounded bg-emerald-100 text-emerald-800 flex items-center justify-center text-[10px] font-bold">01</span>
                        <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-700">Sekretaris RT 001</span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-medium truncate">Indra (No Delete)</div>
                </button>
            </div>
            <p class="text-[11px] text-center text-slate-500 mt-4">Kata sandi seluruh akun: <code class="bg-slate-100 text-slate-800 px-1.5 py-0.5 rounded font-mono font-semibold">password</code></p>
        </div>
    </div>
</div>
