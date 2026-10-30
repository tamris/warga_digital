<div>
    <!-- Split Dual-Pane Card Ultra Modern (Inspirasi Desain HRIS Enterprise Sesuai Referensi) -->
    <div class="bg-white rounded-[28px] sm:rounded-[36px] shadow-2xl overflow-hidden border border-white/20 grid grid-cols-1 lg:grid-cols-12 min-h-[580px]">
        
        <!-- Sisi Kiri: Hero Banner Gradient & Organic Shapes -->
        <div class="lg:col-span-5 bg-gradient-to-br from-[#0c5a45] via-[#083344] to-[#0a192f] text-white p-8 sm:p-10 flex flex-col justify-between relative overflow-hidden">
            <!-- Organic Fluid Shapes (Sesuai Referensi Desain) -->
            <div class="absolute -bottom-14 -left-14 w-48 h-48 rounded-full bg-white/10 blur-xs pointer-events-none"></div>
            <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full bg-emerald-500/15 blur-sm pointer-events-none"></div>
            <div class="absolute top-1/3 -right-8 w-28 h-28 rounded-full bg-teal-400/10 blur-md pointer-events-none"></div>

            <!-- Header Left -->
            <div class="relative z-10 text-center sm:text-left">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white/15 backdrop-blur-md border border-white/25 text-white shadow-md mb-4">
                    <svg class="w-7 h-7 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="text-[11px] font-extrabold tracking-widest text-emerald-300 uppercase">
                    Rukun Warga 01
                </div>
                <div class="text-xs font-semibold text-white/70">
                    Kelurahan Slawi Kulon
                </div>
            </div>

            <!-- Content Left -->
            <div class="relative z-10 py-6 sm:py-8 text-center sm:text-left">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-tight">
                    Selamat<br class="hidden sm:inline"> Datang!
                </h1>
                <p class="text-xs sm:text-sm text-emerald-100/80 mt-3 leading-relaxed max-w-sm">
                    Untuk tetap terhubung dengan sistem pengelolaan warga dan pelayanan administrasi lingkungan, silakan masuk menggunakan akun pengurus Anda.
                </p>

                <div class="mt-6 flex flex-wrap gap-2 justify-center sm:justify-start">
                    <div class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/20 text-emerald-200 text-xs font-semibold backdrop-blur-xs">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>TTE Digital Resmi</span>
                    </div>
                    <div class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/20 text-emerald-200 text-xs font-semibold backdrop-blur-xs">
                        <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Sensus Mandiri</span>
                    </div>
                </div>
            </div>

            <!-- Footer Left -->
            <div class="relative z-10 text-center sm:text-left pt-4 border-t border-white/10">
                <span class="text-[10px] tracking-widest font-extrabold text-emerald-300/80 uppercase">
                    POWERED BY DIGITAL RW PLATFORM
                </span>
            </div>
        </div>

        <!-- Sisi Kanan: Form Masuk Modern -->
        <div class="lg:col-span-7 bg-white p-8 sm:p-12 flex flex-col justify-between relative">
            <!-- Decorative Accent Corner Shape (Sesuai Referensi) -->
            <div class="w-24 h-24 bg-gradient-to-br from-emerald-500/10 to-teal-500/5 rounded-bl-full absolute top-0 right-0 pointer-events-none"></div>

            <div class="my-auto">
                <!-- Form Header -->
                <div class="text-center sm:text-left mb-7">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Masuk
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1.5 font-medium">
                        Silakan masukkan nama pengguna dan kata sandi Anda
                    </p>
                </div>

                @if (session()->has('error'))
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-3.5 rounded-xl mb-5 font-semibold text-xs sm:text-sm flex items-center space-x-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Form Login -->
                <form wire:submit.prevent="login" class="space-y-4">
                    <!-- Field Username -->
                    <div>
                        <label for="username" class="block text-xs font-bold text-slate-700 tracking-wide mb-1.5">
                            Nama Pengguna (Username) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input type="text" id="username" wire:model="username" 
                                   placeholder="Masukkan username Anda..."
                                   class="w-full h-11 pl-10 pr-4 rounded-full bg-slate-50/80 hover:bg-slate-50 border border-slate-200 text-slate-900 text-xs sm:text-sm focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 outline-none transition @error('username') border-rose-500 @enderror">
                        </div>
                        @error('username')
                            <p class="mt-1 text-xs font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Field Password dengan Toggle Show/Hide -->
                    <div x-data="{ show: false }">
                        <label for="password" class="block text-xs font-bold text-slate-700 tracking-wide mb-1.5">
                            Kata Sandi (Password) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input :type="show ? 'text' : 'password'" id="password" wire:model="password" 
                                   placeholder="Masukkan kata sandi..."
                                   class="w-full h-11 pl-10 pr-11 rounded-full bg-slate-50/80 hover:bg-slate-50 border border-slate-200 text-slate-900 text-xs sm:text-sm focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 outline-none transition @error('password') border-rose-500 @enderror">
                            
                            <!-- Toggle Button Show/Hide Password -->
                            <button type="button" @click="show = !show" 
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition"
                                    title="Tampilkan / Sembunyikan Kata Sandi">
                                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me & Security Note -->
                    <div class="flex items-center justify-between pt-1 text-xs">
                        <label for="remember" class="flex items-center cursor-pointer select-none">
                            <input type="checkbox" id="remember" wire:model="remember" class="w-4 h-4 rounded-md border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <span class="ml-2 font-medium text-slate-600">Ingat saya</span>
                        </label>
                        <div class="flex items-center space-x-1 text-slate-400">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span class="text-[11px] font-semibold">Terkoneksi Aman</span>
                        </div>
                    </div>

                    <!-- Submit Button Pill (Sesuai Referensi) -->
                    <div class="pt-4 flex justify-center">
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                wire:target="login"
                                class="w-full sm:w-auto min-w-[200px] h-11 px-8 rounded-full bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-500 hover:to-teal-500 active:scale-[0.99] disabled:opacity-75 disabled:cursor-wait text-white font-bold text-xs sm:text-sm tracking-wider uppercase shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center space-x-2">
                            <!-- Idle State -->
                            <span wire:loading.remove wire:target="login" class="inline-flex items-center space-x-2">
                                <span>Masuk</span>
                              
                            </span>

                            <!-- Loading State -->
                            <span wire:loading.flex wire:target="login" class="items-center justify-center space-x-2">
                                <svg class="animate-spin h-4 w-4 text-white shrink-0" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                    <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>Memverifikasi...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer Copyright -->
            <div class="pt-8 text-center text-[11px] text-slate-400 font-medium">
                &copy; {{ date('Y') }} Rukun Warga 01 • Kelurahan Slawi Kulon. Hak cipta dilindungi.
            </div>
        </div>

    </div>
</div>
