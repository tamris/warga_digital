<div class="space-y-6">
    <!-- Header Halaman -->
    <div class="bg-white rounded-xl p-5 sm:p-6 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                @if(auth()->user()->isKetuaRt())
                    Kelola Kepengurusan RT {{ auth()->user()->rt?->nomor_rt }}
                @else
                    Kelola Wilayah & Kepengurusan
                @endif
            </h1>
            <p class="text-sm text-slate-600 mt-1 font-medium">
                @if(auth()->user()->isKetuaRt())
                    Pusat kendali akun pengurus (Sekretaris & Bendahara) wilayah RT {{ auth()->user()->rt?->nomor_rt }}
                @else
                    Pusat kendali data wilayah RT dan kredensial akun kepengurusan lingkungan RW
                @endif
            </p>
        </div>

        <div class="flex items-center space-x-2.5">
            @if(auth()->user()->isAdminRw())
                @if($activeTab === 'rt')
                    <button type="button" wire:click="openCreateModal" 
                            class="h-10 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 font-semibold text-xs px-3.5 rounded-lg shadow-2xs transition inline-flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Wilayah RT</span>
                    </button>

                    <button type="button" wire:click="openCreateUserModal" 
                            class="h-10 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-3.5 rounded-lg shadow-2xs transition inline-flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        <span>Tambah Akun RT</span>
                    </button>
                @else
                    <button type="button" wire:click="openCreateUserModal(null, true)" 
                            class="h-10 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-3.5 rounded-lg shadow-2xs transition inline-flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        <span>Tambah Pengurus RW</span>
                    </button>
                @endif
            @else
                <button type="button" wire:click="openCreateUserModal" 
                        class="h-10 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-3.5 rounded-lg shadow-2xs transition inline-flex items-center space-x-1.5">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span>Tambah Pengurus RT</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Navigation Tabs (Hanya ditampilkan untuk Pengurus RW) -->
    @if(auth()->user()->isAdminRw())
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-1.5 flex items-center space-x-1">
        <button type="button" wire:click="switchTab('rt')"
                class="flex-1 sm:flex-none flex items-center justify-center space-x-2 py-2 px-4 rounded-lg text-xs sm:text-sm font-bold transition {{ $activeTab === 'rt' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <svg class="w-4 h-4 {{ $activeTab === 'rt' ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span>Pengurus & Wilayah RT</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $activeTab === 'rt' ? 'bg-emerald-700/60 text-white' : 'bg-slate-200/80 text-slate-700' }}">
                {{ $rts->count() }} RT
            </span>
        </button>

        <button type="button" wire:click="switchTab('rw')"
                class="flex-1 sm:flex-none flex items-center justify-center space-x-2 py-2 px-4 rounded-lg text-xs sm:text-sm font-bold transition {{ $activeTab === 'rw' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
            <svg class="w-4 h-4 {{ $activeTab === 'rw' ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>Pengurus Tingkat RW</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $activeTab === 'rw' ? 'bg-emerald-700/60 text-white' : 'bg-slate-200/80 text-slate-700' }}">
                {{ $rwAccounts->count() }} Pejabat
            </span>
        </button>
    </div>
    @endif

    @if($activeTab === 'rt')
    <!-- TAB 1: PENGURUS & WILAYAH RT -->
    <div class="space-y-6">
        @if(auth()->user()->isAdminRw())
        <!-- 1. Grid Ringkasan Wilayah RT (Khusus Admin RW) -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                    Ringkasan Wilayah Rukun Tetangga ({{ $rts->count() }} RT)
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                @foreach($rts as $rt)
                <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4 flex flex-col justify-between hover:border-emerald-300 hover:shadow-xs transition group">
                    <div>
                        <!-- Header Kartu RT -->
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                RT {{ $rt->nomor_rt }}
                            </span>

                            <div class="flex items-center space-x-1">
                                <button type="button" wire:click="openEditModal({{ $rt->id }})" 
                                        class="text-xs text-slate-500 hover:text-slate-800 font-semibold p-1 hover:bg-slate-100 rounded transition"
                                        title="Edit Data RT {{ $rt->nomor_rt }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Ketua RT & WA -->
                        <div class="space-y-1">
                            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ketua RT:</div>
                            <div class="font-bold text-slate-900 text-sm truncate" title="{{ $rt->nama_ketua }}">
                                {{ $rt->nama_ketua ?? 'Belum diatur' }}
                            </div>

                            <div class="pt-1">
                                @if($rt->no_wa_ketua)
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $rt->no_wa_ketua)) }}" 
                                       target="_blank" 
                                       class="inline-flex items-center space-x-1.5 text-xs font-semibold bg-emerald-50 hover:bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded border border-emerald-200 transition shadow-2xs"
                                       title="Chat via WhatsApp">
                                        <svg class="w-3 h-3 fill-[#25D366] shrink-0" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                        <span class="font-mono text-xs">{{ $rt->formatted_wa_ketua }}</span>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 font-semibold">—</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Footer Kartu RT: Statistik & Link -->
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-semibold">
                            {{ $rt->houses_count }} Rumah <span class="text-slate-300">•</span> <strong class="text-emerald-700 font-bold">{{ $rt->residents_count }} Warga</strong>
                        </span>

                        <a href="{{ route('houses.index', ['rt' => $rt->id]) }}" 
                           wire:navigate
                           class="text-xs font-bold text-slate-700 hover:text-emerald-700 inline-flex items-center space-x-1 group-hover:translate-x-0.5 transition">
                            <span>Buka</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- 2. Tabel Utama: Kelola Akun Pengurus RT (Bisa Edit & Hapus Kredensial) -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <!-- Toolbar Atas Tabel Akun -->
            <div class="p-4 sm:p-5 border-b border-slate-200/80 flex flex-col md:flex-row md:items-center md:justify-between gap-3 bg-slate-50/50">
                <div>
                    <h2 class="text-base font-bold text-slate-900 flex items-center space-x-2">
                        <span>Daftar Akun Pengurus Rukun Tetangga (RT)</span>
                        <span class="px-2 py-0.5 text-xs font-bold bg-slate-200/80 text-slate-700 rounded-full">
                            {{ $accounts->total() }} Akun
                        </span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        @if(auth()->user()->isKetuaRt())
                            Kelola kredensial login akun Sekretaris dan Bendahara untuk wilayah RT {{ auth()->user()->rt?->nomor_rt }}.
                        @else
                            Pengurus RT dapat mengelola data unit rumah dan warga sesuai batasan wilayah masing-masing.
                        @endif
                    </p>
                </div>

                <!-- Filter & Pencarian Akun -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Search -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" wire:model.live.debounce.250ms="accountSearch" 
                                placeholder="Cari pengurus..." 
                                class="h-9 pl-8 pr-3 text-xs rounded-lg border border-slate-300 text-slate-900 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none w-40 sm:w-48 bg-white">
                    </div>

                    <!-- Filter RT (Hanya untuk Admin RW) -->
                    @if(auth()->user()->isAdminRw())
                    <select wire:model.live="accountRtFilter" 
                            class="h-9 px-2.5 text-xs font-semibold rounded-lg border border-slate-300 text-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none bg-white">
                        <option value="">Semua Wilayah RT</option>
                        @foreach($rts as $rt)
                            <option value="{{ $rt->id }}">RT {{ $rt->nomor_rt }}</option>
                        @endforeach
                    </select>
                    @endif

                    <!-- Filter Peran -->
                    <select wire:model.live="accountRoleFilter" 
                            class="h-9 px-2.5 text-xs font-semibold rounded-lg border border-slate-300 text-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none bg-white">
                        <option value="">Semua Jabatan</option>
                        <option value="ketua_rt">Ketua RT</option>
                        <option value="sekretaris_rt">Sekretaris RT</option>
                        <option value="bendahara_rt">Bendahara RT</option>
                    </select>
                </div>
            </div>

            <!-- Tabel Pengurus RT -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                            <th class="py-3 px-5">Nama Lengkap Pengurus</th>
                            <th class="py-3 px-4">Wilayah RT</th>
                            <th class="py-3 px-4">Peran / Jabatan</th>
                            <th class="py-3 px-4">Username Login</th>
                            <th class="py-3 px-4">Kontak WhatsApp</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($accounts as $acc)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- 1. Nama Lengkap -->
                                <td class="py-3.5 px-5">
                                    <div class="font-bold text-slate-900 text-sm">{{ $acc->nama }}</div>
                                    @if($acc->resident)
                                        <div class="inline-flex items-center space-x-1 text-[11px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 mt-1">
                                            <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>NIK: {{ $acc->resident->nik }} &bull; {{ $acc->resident->house?->nomor_rumah ?? 'RT '.$acc->rt?->nomor_rt }}</span>
                                        </div>
                                    @else
                                        <div class="text-[11px] text-slate-400 mt-0.5">ID Akun: #{{ $acc->id }} &bull; Akun mandiri</div>
                                    @endif
                                </td>

                                <!-- 2. Wilayah RT -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                        RT {{ $acc->rt?->nomor_rt ?? '—' }}
                                    </span>
                                </td>

                                <!-- 3. Peran / Jabatan -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($acc->role === 'ketua_rt')
                                        <span class="inline-block px-2.5 py-1 bg-slate-900 text-white font-bold text-xs rounded-md shadow-2xs">
                                            Ketua RT
                                        </span>
                                    @elseif($acc->role === 'sekretaris_rt')
                                        <span class="inline-block px-2.5 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 font-semibold text-xs rounded-md">
                                            Sekretaris RT
                                        </span>
                                    @elseif($acc->role === 'bendahara_rt')
                                        <span class="inline-block px-2.5 py-1 bg-amber-50 text-amber-800 border border-amber-200 font-semibold text-xs rounded-md">
                                            Bendahara RT
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-1 bg-slate-100 text-slate-700 font-semibold text-xs rounded-md">
                                            {{ $acc->role_label }}
                                        </span>
                                    @endif
                                </td>

                                <!-- 4. Username Login -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-mono text-xs font-bold bg-slate-100 text-slate-800 px-2.5 py-1 rounded-md border border-slate-200">
                                        {{ $acc->username }}
                                    </span>
                                </td>

                                <!-- 5. Kontak WhatsApp -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($acc->no_wa)
                                        <a href="{{ $acc->wa_link }}" target="_blank" 
                                           class="inline-flex items-center space-x-1.5 text-xs font-semibold text-emerald-800 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100/80 px-2.5 py-1 rounded-lg border border-emerald-200 transition shadow-2xs"
                                           title="Chat via WhatsApp">
                                            <svg class="w-3.5 h-3.5 fill-[#25D366] shrink-0" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                            <span class="font-mono">{{ $acc->formatted_wa }}</span>
                                        </a>
                                    @else
                                        <span class="text-slate-400 font-bold text-sm select-none">—</span>
                                    @endif
                                </td>

                                <!-- 6. Status -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($acc->isActive())
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold rounded-full text-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Aktif</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 bg-slate-100 text-slate-600 border border-slate-300 font-semibold rounded-full text-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            <span>Purna Tugas</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- 7. Aksi (Edit, Toggle Status, Hapus) -->
                                <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1.5">
                                        <button type="button" wire:click="openEditUserModal({{ $acc->id }})" 
                                                class="w-8 h-8 rounded-lg bg-white hover:bg-slate-100 text-slate-600 hover:text-slate-900 border border-slate-200 inline-flex items-center justify-center transition shadow-2xs group"
                                                title="Edit Akun & Ubah Kata Sandi">
                                            <svg class="w-4 h-4 text-slate-500 group-hover:text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>

                                        @if($acc->id !== auth()->id() && (! auth()->user()->isKetuaRt() || ! $acc->isKetuaRt()))
                                            <button type="button" wire:click="toggleUserStatus({{ $acc->id }})" 
                                                    class="w-8 h-8 rounded-lg {{ $acc->isActive() ? 'bg-amber-50/70 hover:bg-amber-100 text-amber-700 border-amber-200' : 'bg-emerald-50/70 hover:bg-emerald-100 text-emerald-700 border-emerald-200' }} border inline-flex items-center justify-center transition shadow-2xs group"
                                                    title="{{ $acc->isActive() ? 'Nonaktifkan Akun (Purna Tugas)' : 'Aktifkan Kembali Akun' }}">
                                                @if($acc->isActive())
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                            </button>

                                            <button type="button" wire:click="confirmDeleteUser({{ $acc->id }})" 
                                                    class="w-8 h-8 rounded-lg bg-rose-50/60 hover:bg-rose-100 text-rose-600 hover:text-rose-700 border border-rose-200/80 inline-flex items-center justify-center transition shadow-2xs group"
                                                    title="Hapus Akun Pengurus">
                                                <svg class="w-4 h-4 text-rose-500 group-hover:text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-500">
                                    <p class="text-base font-bold text-slate-800">Tidak ada akun pengurus RT yang cocok</p>
                                    <p class="text-xs text-slate-400 mt-1">Sesuaikan kata kunci pencarian atau ganti filter wilayah RT.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($accounts->total() > 0)
                <div class="p-4 bg-slate-50/50 border-t border-slate-100">
                    {{ $accounts->links() }}
                </div>
            @endif
        </div>
    </div>
    @endif

    @if($activeTab === 'rw')
    <!-- TAB 2: PENGURUS TINGKAT RW -->
    <div class="space-y-6">
        <!-- Card Tabel Pengurus RW -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200/80 flex flex-col md:flex-row md:items-center md:justify-between gap-3 bg-slate-50/50">
                <div>
                    <h2 class="text-base font-bold text-slate-900 flex items-center space-x-2">
                        <span>Daftar Pengurus Pusat Rukun Warga</span>
                        <span class="px-2 py-0.5 text-xs font-bold bg-slate-200/80 text-slate-700 rounded-full">
                            {{ $rwAccounts->count() }} Pejabat RW
                        </span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pengurus RW mengkoordinasikan kegiatan lingkungan, bantuan sosial, legalitas surat pengantar, dan pembukuan kas pusat RW.
                    </p>
                </div>

                <button type="button" wire:click="openCreateUserModal(null, true)" 
                        class="h-9 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-3.5 rounded-lg shadow-2xs transition inline-flex items-center space-x-1.5 self-start md:self-auto">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span>Tambah Pengurus RW</span>
                </button>
            </div>

            <!-- Tabel Pengurus RW -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                            <th class="py-3 px-5">Nama Pejabat RW</th>
                            <th class="py-3 px-4">Jabatan & Hak Akses</th>
                            <th class="py-3 px-4">Username Login</th>
                            <th class="py-3 px-4">Kontak WhatsApp</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($rwAccounts as $acc)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- 1. Nama Lengkap -->
                                <td class="py-3.5 px-5">
                                    <div class="font-bold text-slate-900 text-sm">{{ $acc->nama }}</div>
                                    @if($acc->resident)
                                        <div class="inline-flex items-center space-x-1 text-[11px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 mt-1">
                                            <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>NIK: {{ $acc->resident->nik }} &bull; {{ $acc->resident->house?->rt ? 'Warga RT '.$acc->resident->house->rt->nomor_rt : 'Warga Lingkungan' }}</span>
                                        </div>
                                    @else
                                        <div class="text-[11px] text-slate-400 mt-0.5">ID Akun: #{{ $acc->id }} &bull; Akun mandiri</div>
                                    @endif
                                </td>

                                <!-- 2. Jabatan & Hak Akses -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($acc->isAdminRw())
                                        <div>
                                            <span class="inline-block px-2.5 py-1 bg-slate-900 text-white font-bold text-xs rounded-md shadow-2xs">
                                                Ketua RW (Admin)
                                            </span>
                                            <div class="text-[11px] text-slate-500 mt-0.5 font-medium">Akses penuh seluruh sistem</div>
                                        </div>
                                    @elseif($acc->role === 'sekretaris_rw')
                                        <div>
                                            <span class="inline-block px-2.5 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold text-xs rounded-md">
                                                Sekretaris RW
                                            </span>
                                            <div class="text-[11px] text-slate-500 mt-0.5 font-medium">Bansos, pengantar & arsip</div>
                                        </div>
                                    @elseif($acc->role === 'bendahara_rw')
                                        <div>
                                            <span class="inline-block px-2.5 py-1 bg-amber-50 text-amber-800 border border-amber-200 font-bold text-xs rounded-md">
                                                Bendahara RW
                                            </span>
                                            <div class="text-[11px] text-slate-500 mt-0.5 font-medium">Kas RW & rekap iuran</div>
                                        </div>
                                    @else
                                        <span class="inline-block px-2.5 py-1 bg-slate-100 text-slate-700 font-semibold text-xs rounded-md">
                                            {{ $acc->role_label }}
                                        </span>
                                    @endif
                                </td>

                                <!-- 3. Username Login -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-mono text-xs font-bold bg-slate-100 text-slate-800 px-2.5 py-1 rounded-md border border-slate-200">
                                        {{ $acc->username }}
                                    </span>
                                </td>

                                <!-- 4. Kontak WhatsApp -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($acc->no_wa)
                                        <a href="{{ $acc->wa_link }}" target="_blank" 
                                           class="inline-flex items-center space-x-1.5 text-xs font-semibold text-emerald-800 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100/80 px-2.5 py-1 rounded-lg border border-emerald-200 transition shadow-2xs"
                                           title="Chat via WhatsApp">
                                            <svg class="w-3.5 h-3.5 fill-[#25D366] shrink-0" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                            <span class="font-mono">{{ $acc->formatted_wa }}</span>
                                        </a>
                                    @else
                                        <span class="text-slate-400 font-bold text-sm select-none">—</span>
                                    @endif
                                </td>

                                <!-- 5. Status -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($acc->isActive())
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold rounded-full text-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Aktif</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 bg-slate-100 text-slate-600 border border-slate-300 font-semibold rounded-full text-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            <span>Purna Tugas</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- 6. Aksi (Edit, Toggle Status, Hapus) -->
                                <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1.5">
                                        <button type="button" wire:click="openEditUserModal({{ $acc->id }})" 
                                                class="w-8 h-8 rounded-lg bg-white hover:bg-slate-100 text-slate-600 hover:text-slate-900 border border-slate-200 inline-flex items-center justify-center transition shadow-2xs group"
                                                title="Edit Akun & Ubah Kata Sandi">
                                            <svg class="w-4 h-4 text-slate-500 group-hover:text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>

                                        @if(! $acc->isAdminRw() && $acc->id !== auth()->id())
                                            <button type="button" wire:click="toggleUserStatus({{ $acc->id }})" 
                                                    class="w-8 h-8 rounded-lg {{ $acc->isActive() ? 'bg-amber-50/70 hover:bg-amber-100 text-amber-700 border-amber-200' : 'bg-emerald-50/70 hover:bg-emerald-100 text-emerald-700 border-emerald-200' }} border inline-flex items-center justify-center transition shadow-2xs group"
                                                    title="{{ $acc->isActive() ? 'Nonaktifkan Akun (Purna Tugas)' : 'Aktifkan Kembali Akun' }}">
                                                @if($acc->isActive())
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                            </button>

                                            <button type="button" wire:click="confirmDeleteUser({{ $acc->id }})" 
                                                    class="w-8 h-8 rounded-lg bg-rose-50/60 hover:bg-rose-100 text-rose-600 hover:text-rose-700 border border-rose-200/80 inline-flex items-center justify-center transition shadow-2xs group"
                                                    title="Hapus Akun Pengurus RW">
                                                <svg class="w-4 h-4 text-rose-500 group-hover:text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        @else
                                            <span class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-200/60 inline-flex items-center justify-center text-slate-300 select-none" title="Akun Utama RW tidak dapat diubah statusnya">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-slate-500">
                                    <p class="text-base font-bold text-slate-800">Belum ada akun pengurus RW tambahan</p>
                                    <p class="text-xs text-slate-400 mt-1">Gunakan tombol "Tambah Pengurus RW" untuk menambahkan Sekretaris atau Bendahara RW.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL 1: TAMBAH / EDIT RT -->
    @if($showModal)
    <div x-data="{ openRt: true }" x-show="openRt" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-md p-6 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">
                    {{ $editingRtId ? 'Edit Data Wilayah RT' : 'Tambah Wilayah RT Baru' }}
                </h3>
                <button type="button" @click="openRt = false; $wire.set('showModal', false)" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="saveRt" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nomor RT (Contoh: 004) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" wire:model="nomor_rt" maxlength="5" placeholder="Contoh: 004"
                           class="w-full h-11 px-3.5 border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                    @error('nomor_rt') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Ketua RT</label>
                    <input type="text" wire:model="nama_ketua" placeholder="Nama lengkap Ketua RT"
                           class="w-full h-11 px-3.5 border border-slate-300 rounded-xl text-sm text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor WhatsApp Ketua RT</label>
                    <input type="tel" inputmode="numeric" maxlength="17" wire:model="no_wa_ketua" 
                           oninput="let v = this.value.replace(/[^0-9]/g, '').slice(0, 14); let p = v.match(/.{1,4}/g); this.value = p ? p.join('-') : '';"
                           placeholder="Contoh: 0808-9898-9898"
                           class="w-full h-11 px-3.5 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                </div>

                <div class="flex items-center justify-end space-x-2.5 pt-4 border-t border-slate-100">
                    <button type="button" @click="openRt = false; $wire.set('showModal', false)" 
                            class="h-11 px-4 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 text-sm font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-sm transition">
                        Simpan RT
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL 2: TAMBAH / EDIT AKUN PENGURUS (RT / RW) -->
    @if($showUserModal)
    <div x-data="{ openUser: true }" x-show="openUser" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-lg p-6 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900">
                        @if($editingUserId)
                            {{ $isRwOfficerModal ? 'Edit Akun Pengurus RW' : 'Edit Akun Pengurus RT' }}
                        @else
                            {{ $isRwOfficerModal ? 'Buat Akun Pengurus RW Baru' : 'Buat Akun Pengurus RT Baru' }}
                        @endif
                    </h3>
                    @if($editingUserId)
                        <p class="text-xs text-slate-500 mt-0.5">Memperbarui kredensial untuk <strong class="text-slate-800">{{ $user_nama }}</strong></p>
                    @endif
                </div>
                <button type="button" @click="openUser = false; $wire.set('showUserModal', false)" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="saveUser" class="space-y-4">
                <!-- Wilayah Penugasan -->
                @if($isRwOfficerModal)
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-800">Tingkat RW (Pusat Lingkungan)</div>
                            <div class="text-[11px] text-slate-500">Pejabat kepengurusan RW bertugas untuk seluruh wilayah lingkungan.</div>
                        </div>
                    </div>
                @elseif(auth()->user()->isKetuaRt())
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-800">Wilayah Rukun Tetangga {{ auth()->user()->rt?->nomor_rt }}</div>
                            <div class="text-[11px] text-slate-500">Akun pengurus ditugaskan khusus di wilayah RT {{ auth()->user()->rt?->nomor_rt }}.</div>
                        </div>
                    </div>
                @else
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Wilayah RT Penugasan <span class="text-rose-500">*</span>
                        </label>
                        <select wire:model.live="selectedRtIdForUser" 
                                class="w-full h-11 px-3 border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                            @foreach($rts as $rt)
                                <option value="{{ $rt->id }}">Rukun Tetangga {{ $rt->nomor_rt }}</option>
                            @endforeach
                        </select>
                        @error('selectedRtIdForUser') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif

                <!-- Nama Lengkap Pengurus (Terintegrasi Pencarian Data Master Warga) -->
                <div class="relative">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Nama Lengkap Pengurus <span class="text-rose-500">*</span>
                        </label>
                        @if($selectedResident)
                            <span class="inline-flex items-center space-x-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>Tersinkron Data Warga</span>
                            </span>
                        @endif
                    </div>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <input type="text" 
                               wire:model.live.debounce.300ms="user_nama" 
                               placeholder="{{ $isRwOfficerModal ? 'Ketik nama atau NIK warga (lintas RT)...' : 'Ketik nama atau NIK warga RT...' }}"
                               class="w-full h-11 pl-10 pr-9 border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none {{ $selectedResident ? 'bg-emerald-50/20 border-emerald-300' : 'bg-white' }}">

                        @if($user_nama)
                            <button type="button" 
                                    wire:click="resetSelectedResident"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                                    title="Bersihkan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        @endif
                    </div>

                    <!-- Indikator Warga Terhubung -->
                    @if($selectedResident)
                        <div class="mt-2 p-2.5 bg-emerald-50 border border-emerald-200/90 rounded-xl flex items-center justify-between gap-2.5">
                            <div class="min-w-0 flex items-center space-x-2 text-xs text-emerald-950">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div class="truncate text-[11px]">
                                    Terhubung: <span class="font-bold font-mono">{{ $selectedResident->nik }}</span> &bull; <span>{{ $selectedResident->house?->rt ? 'RT '.$selectedResident->house->rt->nomor_rt.' • ' : '' }}{{ $selectedResident->house?->nomor_rumah ?? 'Tanpa Rumah' }}</span>
                                </div>
                            </div>
                            <button type="button" 
                                    wire:click="resetSelectedResident"
                                    class="h-6 px-2 rounded-md bg-white hover:bg-rose-50 hover:text-rose-700 hover:border-rose-300 border border-slate-200 text-slate-600 text-[10px] font-bold transition shrink-0">
                                Lepas
                            </button>
                        </div>
                    @else
                        <!-- Dropdown Popover Rekomendasi Warga -->
                        @if($residentSuggestions->isNotEmpty())
                            <div class="absolute left-0 right-0 z-30 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl max-h-52 overflow-y-auto divide-y divide-slate-100">
                                <div class="px-3 py-1.5 bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                    {{ $isRwOfficerModal ? 'Ditemukan di Data Warga RW ini (klik untuk menghubungkan):' : 'Ditemukan di Data Warga RT ini (klik untuk menghubungkan):' }}
                                </div>
                                @foreach($residentSuggestions as $suggest)
                                    <button type="button" 
                                            wire:click="selectResident({{ $suggest->id }})"
                                            class="w-full text-left px-3 py-2.5 hover:bg-emerald-50/80 transition flex items-center justify-between text-xs group">
                                        <div class="min-w-0 pr-2">
                                            <div class="font-bold text-slate-900 group-hover:text-emerald-800 truncate">{{ $suggest->nama_lengkap }}</div>
                                            <div class="text-[11px] text-slate-500 mt-0.5 truncate">
                                                NIK: <span class="font-mono text-slate-600">{{ $suggest->nik }}</span> &bull; {{ $suggest->house?->rt ? 'RT '.$suggest->house->rt->nomor_rt.' • ' : '' }}{{ $suggest->house?->nomor_rumah ?? 'Tanpa Rumah' }}
                                            </div>
                                        </div>
                                        <span class="px-2.5 py-1 rounded text-[10px] font-bold bg-slate-100 group-hover:bg-emerald-600 group-hover:text-white text-slate-700 transition shrink-0">
                                            Hubungkan
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        <p class="text-[11px] text-slate-400 mt-1">
                            {{ $isRwOfficerModal ? 'Ketik nama warga dari RT mana saja di RW ini untuk dihubungkan otomatis, atau ketik nama mandiri.' : 'Ketik nama warga untuk mencari dari master data RT ini (otomatis terhubung), atau ketik nama mandiri.' }}
                        </p>
                    @endif
                    @error('user_nama') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Peran / Jabatan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Peran / Hak Akses <span class="text-rose-500">*</span>
                    </label>
                    @if($isRwOfficerModal)
                        @if(in_array($user_role, ['admin_rw', 'ketua_rw'], true))
                            <div class="w-full h-11 px-3 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 flex items-center">
                                Ketua RW (Admin Lingkungan)
                            </div>
                        @else
                            <select wire:model="user_role" 
                                    class="w-full h-11 px-3 border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                                <option value="sekretaris_rw">Sekretaris RW (Kelola Program Bansos, Pengantar & Arsip)</option>
                                <option value="bendahara_rw">Bendahara RW (Kelola Kas RW, Mutasi & Rekap Iuran)</option>
                            </select>
                        @endif
                    @else
                        @if(auth()->user()->isKetuaRt())
                            @if($editingUserId && $editingUserId === auth()->id())
                                <div class="w-full h-11 px-3 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 flex items-center">
                                    Ketua RT (Penanggung Jawab Wilayah)
                                </div>
                            @else
                                <select wire:model="user_role" 
                                        class="w-full h-11 px-3 border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                                    <option value="sekretaris_rt">Sekretaris RT (Bisa Tambah & Edit Warga/Rumah)</option>
                                    <option value="bendahara_rt">Bendahara RT (Hanya Lihat Data & Kelola Iuran)</option>
                                </select>
                            @endif
                        @else
                            <select wire:model="user_role" 
                                    class="w-full h-11 px-3 border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                                <option value="ketua_rt">Ketua RT (Akses Penuh Kelola & Hapus Wilayah RT)</option>
                                <option value="sekretaris_rt">Sekretaris RT (Bisa Tambah & Edit Warga/Rumah)</option>
                                <option value="bendahara_rt">Bendahara RT (Hanya Lihat Data & Kelola Iuran)</option>
                            </select>
                        @endif
                    @endif
                    @error('user_role') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Username & Password -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Username <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" wire:model="user_username" placeholder="{{ $isRwOfficerModal ? 'Contoh: sekretaris_rw' : 'Contoh: ketua_rt01' }}"
                               class="w-full h-11 px-3 border border-slate-300 rounded-xl text-xs font-mono font-semibold text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                        @error('user_username') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kata Sandi {!! $editingUserId ? '<span class="text-slate-400 font-normal lowercase">(opsional)</span>' : '<span class="text-rose-500">*</span>' !!}
                        </label>
                        <input type="text" wire:model="user_password" 
                               placeholder="{{ $editingUserId ? 'Kosongkan jika tetap' : 'Min. 4 huruf' }}"
                               class="w-full h-11 px-3 border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                        @error('user_password') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Nomor WhatsApp -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Nomor WhatsApp <span class="text-rose-500">*</span>
                        </label>
                        @if($selectedResident && $selectedResident->no_wa)
                            <span class="text-[11px] text-emerald-700 font-medium">Tersinkron dari kontak warga</span>
                        @endif
                    </div>
                    <input type="tel" inputmode="numeric" maxlength="17" wire:model="user_no_wa" 
                           oninput="let v = this.value.replace(/[^0-9]/g, '').slice(0, 14); let p = v.match(/.{1,4}/g); this.value = p ? p.join('-') : '';"
                           placeholder="Contoh: 0808-9898-9898"
                           class="w-full h-11 px-3.5 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none {{ $selectedResident && $selectedResident->no_wa ? 'bg-slate-50/60' : 'bg-white' }}">
                    <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk komunikasi pengurus dan reset akun. Format: 0808-9898-9898.</p>
                    @error('user_no_wa') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Status Jabatan (Hanya tampil saat Edit Akun orang lain) -->
                @if($editingUserId && $editingUserId !== auth()->id())
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Status Jabatan
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" 
                                wire:click="$set('user_is_active', true)"
                                class="text-left p-3 rounded-xl border transition flex items-start space-x-3 {{ $user_is_active ? 'border-emerald-500 bg-emerald-50/40 ring-1 ring-emerald-500 shadow-2xs' : 'border-slate-200 bg-white hover:bg-slate-50' }}">
                            <div class="mt-0.5 w-4 h-4 rounded-full border flex items-center justify-center shrink-0 {{ $user_is_active ? 'border-emerald-600 bg-white' : 'border-slate-300 bg-white' }}">
                                @if($user_is_active)
                                    <div class="w-2 h-2 rounded-full bg-emerald-600"></div>
                                @endif
                            </div>
                            <div>
                                <div class="text-xs font-bold {{ $user_is_active ? 'text-emerald-950' : 'text-slate-800' }}">
                                    Aktif (Menjabat)
                                </div>
                                <div class="text-[11px] {{ $user_is_active ? 'text-emerald-700 font-medium' : 'text-slate-500' }} mt-0.5">
                                    Dapat login & bertugas
                                </div>
                            </div>
                        </button>

                        <button type="button" 
                                wire:click="$set('user_is_active', false)"
                                class="text-left p-3 rounded-xl border transition flex items-start space-x-3 {{ ! $user_is_active ? 'border-slate-500 bg-slate-100/80 ring-1 ring-slate-500 shadow-2xs' : 'border-slate-200 bg-white hover:bg-slate-50' }}">
                            <div class="mt-0.5 w-4 h-4 rounded-full border flex items-center justify-center shrink-0 {{ ! $user_is_active ? 'border-slate-700 bg-white' : 'border-slate-300 bg-white' }}">
                                @if(! $user_is_active)
                                    <div class="w-2 h-2 rounded-full bg-slate-700"></div>
                                @endif
                            </div>
                            <div>
                                <div class="text-xs font-bold {{ ! $user_is_active ? 'text-slate-900' : 'text-slate-800' }}">
                                    Purna Tugas
                                </div>
                                <div class="text-[11px] {{ ! $user_is_active ? 'text-slate-700 font-medium' : 'text-slate-500' }} mt-0.5">
                                    Akses login dinonaktifkan
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
                @endif

                <!-- Footer Tombol -->
                <div class="flex items-center justify-end space-x-2.5 pt-4 border-t border-slate-100">
                    <button type="button" @click="openUser = false; $wire.set('showUserModal', false)" 
                            class="h-11 px-4 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 text-sm font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-sm transition">
                        {{ $editingUserId ? 'Simpan Perubahan' : 'Buat Akun' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL 3: KONFIRMASI HAPUS AKUN PENGURUS -->
    @if($showDeleteUserModal)
    <div x-data="{ openDelete: true }" x-show="openDelete" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-md p-6 animate-in fade-in zoom-in-95 duration-150 text-center">
            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 mx-auto flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>

            <h3 class="text-base font-bold text-slate-900 mb-1">Hapus Akun Pengurus?</h3>
            <p class="text-xs text-slate-600 mb-4 leading-relaxed">
                Apakah Anda yakin ingin menghapus akun pengurus <strong class="text-slate-900">{{ $deletingUserName }}</strong>?
            </p>

            @if($deletingUserHasHistory)
                <div class="text-left bg-amber-50 border border-amber-200 rounded-xl p-3 mb-5 space-y-1.5">
                    <div class="flex items-center space-x-1.5 text-xs font-bold text-amber-800">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Pengurus Memiliki Riwayat Arsip</span>
                    </div>
                    <p class="text-[11px] text-amber-900 leading-normal">
                        Pengurus ini tercatat pernah membuat surat, membukukan kas, atau mencatat iuran. Data arsip historis tersebut <strong>tetap aman dan tetap bertuliskan nama {{ $deletingUserName }}</strong>.
                    </p>
                    <p class="text-[11px] text-amber-800 font-medium">
                        Disarankan untuk memilih <strong>Jadikan Purna Tugas</strong> agar riwayat kepengurusan tetap tersimpan rapi tanpa memberi akses login.
                    </p>
                </div>
            @else
                <p class="text-xs text-slate-500 mb-5 leading-relaxed">
                    Akun ini tidak akan dapat login lagi ke sistem.
                </p>
            @endif

            <div class="flex flex-col sm:flex-row items-center justify-center gap-2">
                <button type="button" @click="openDelete = false; $wire.set('showDeleteUserModal', false)" 
                        class="w-full sm:w-auto h-10 px-4 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-semibold transition">
                    Batal
                </button>
                @if($deletingUserHasHistory)
                    <button type="button" wire:click="deactivateFromDeleteModal" 
                            class="w-full sm:w-auto h-10 px-4 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold shadow-sm transition">
                        Jadikan Purna Tugas Saja
                    </button>
                @endif
                <button type="button" wire:click="deleteUser" 
                        class="w-full sm:w-auto h-10 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition">
                    Ya, Hapus Akun
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
