<div class="space-y-6">
    <!-- Header Area -->
    <div class="bg-white rounded-xl p-5 sm:p-6 border border-slate-200 shadow-sm flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    Daftar Rumah & Warga
                </h1>
                @if($pendingSensusCount > 0)
                    <button type="button" wire:click="switchTab('sensus')" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-300 shadow-2xs animate-pulse hover:bg-amber-100 transition-colors">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        {{ $pendingSensusCount }} Sensus Menunggu Verifikasi
                    </button>
                @endif
            </div>
            <p class="text-sm text-slate-600 mt-1 font-medium">
                Kelola unit hunian fisik, impor data massal, dan verifikasi sensus mandiri warga.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Tautan Publik Sensus Mandiri (Salin Link) -->
            @php
                $sensusUrl = url('/sensus' . ($user->rt ? '/' . str_pad((string)((int)$user->rt->nomor_rt), 2, '0', STR_PAD_LEFT) : ''));
            @endphp
            <div x-data="{ copied: false }">
                <button type="button" 
                    @click="navigator.clipboard.writeText('{{ $sensusUrl }}'); copied = true; setTimeout(() => copied = false, 2500)"
                    class="h-10 px-3.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition inline-flex items-center space-x-1.5 shadow-2xs">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    <span x-show="!copied">Tautan Sensus Warga</span>
                    <span x-show="copied" class="text-emerald-700 font-bold">Tautan Tersalin!</span>
                </button>
            </div>

            @if($user->canManageHouse())
                <!-- Tombol Import Excel -->
                <button type="button" wire:click="openImportModal" 
                        class="h-10 px-3.5 rounded-lg border border-emerald-300 bg-emerald-50 hover:bg-emerald-100/80 text-emerald-800 font-bold text-xs transition inline-flex items-center space-x-1.5 shadow-2xs">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Import Excel</span>
                </button>

                <!-- Tombol Tambah Rumah Manual -->
                <button type="button" wire:click="openCreateModal" 
                        class="h-10 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 rounded-lg shadow-2xs transition inline-flex items-center space-x-1.5">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Rumah</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Tab Navigasi Utama: Data Rumah vs Antrean Sensus -->
    <div class="flex items-center border-b border-slate-200 gap-4">
        <button type="button" 
            wire:click="switchTab('houses')" 
            class="pb-3 text-sm font-bold border-b-2 transition-all flex items-center gap-2 {{ $activeTab === 'houses' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <svg class="w-4 h-4 {{ $activeTab === 'houses' ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Daftar Unit Rumah & Penghuni</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $activeTab === 'houses' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                {{ $houses->total() }}
            </span>
        </button>

        <button type="button" 
            wire:click="switchTab('sensus')" 
            class="pb-3 text-sm font-bold border-b-2 transition-all flex items-center gap-2 {{ $activeTab === 'sensus' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <svg class="w-4 h-4 {{ $activeTab === 'sensus' ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <span>Antrean Sensus Warga Mandiri</span>
            @if($pendingSensusCount > 0)
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                    {{ $pendingSensusCount }} Baru
                </span>
            @endif
        </button>
    </div>

    @if($activeTab === 'houses')
        <!-- Toolbar / Filter Bar -->
        <div class="bg-white rounded-xl p-4 sm:p-5 border border-slate-200 shadow-sm space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center gap-3">
                <!-- Search bar -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" wire:model.live.debounce.150ms="search" 
                           placeholder="Cari nomor rumah atau nama warga..."
                           class="w-full h-11 pl-10 pr-10 rounded-xl border border-slate-300 text-sm text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition placeholder:text-slate-400">
                    @if($search !== '')
                        <button type="button" wire:click="$set('search', '')" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 transition" title="Hapus pencarian">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    @endif
                </div>

                <!-- Filter RT (Admin RW) -->
                @if($user->isAdminRw())
                <div class="sm:w-56 shrink-0">
                    <select wire:model.live="selectedRtId" 
                            class="w-full h-11 px-3 border border-slate-300 rounded-xl text-sm font-semibold text-slate-700 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                        <option value="">Semua Wilayah RT</option>
                        @foreach($rts as $rt)
                            <option value="{{ $rt->id }}">RT {{ $rt->nomor_rt }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Tab Pill Filter Status -->
                <div x-data="{ activeStatus: $wire.entangle('statusHuni').live }" 
                     class="flex items-center space-x-1 bg-slate-100 p-1 rounded-xl shrink-0 overflow-x-auto text-xs font-semibold">
                    <button type="button" @click="activeStatus = ''" 
                            :class="activeStatus === '' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                            class="px-3 py-2 rounded-lg transition">
                        Semua
                    </button>
                    <button type="button" @click="activeStatus = 'dihuni_pemilik'" 
                            :class="activeStatus === 'dihuni_pemilik' ? 'bg-white text-emerald-800 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                            class="px-3 py-2 rounded-lg transition">
                        Dihuni
                    </button>
                    <button type="button" @click="activeStatus = 'kontrak'" 
                            :class="activeStatus === 'kontrak' ? 'bg-white text-sky-800 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                            class="px-3 py-2 rounded-lg transition">
                        Kontrak
                    </button>
                    <button type="button" @click="activeStatus = 'kosong'" 
                            :class="activeStatus === 'kosong' ? 'bg-white text-slate-800 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                            class="px-3 py-2 rounded-lg transition">
                        Kosong
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabel Data Rumah -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 font-semibold uppercase text-xs tracking-wider border-b border-slate-200">
                            <th class="py-3.5 px-5">Rumah</th>
                            <th class="py-3.5 px-3 text-center">RT</th>
                            <th class="py-3.5 px-5">Kepala Keluarga</th>
                            <th class="py-3.5 px-5">Kontak WhatsApp</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-3 text-center">Jiwa</th>
                            <th class="py-3.5 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody wire:loading.class="opacity-60 pointer-events-none" class="divide-y divide-slate-100 text-sm font-medium text-slate-700 transition-opacity duration-150">
                        @forelse($houses as $house)
                            @php
                                $head = $house->headOfFamily;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors border-b border-slate-100">
                                <!-- Rumah -->
                                <td class="py-4 px-5">
                                    <a href="{{ route('houses.show', $house->id) }}" wire:navigate class="font-bold text-slate-900 hover:text-emerald-700 transition">
                                        {{ $house->nomor_rumah }}
                                    </a>
                                    @if($house->kategori_tarif === 'usaha')
                                        <span class="inline-block ml-1.5 px-2 py-0.5 text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 rounded">
                                            Usaha
                                        </span>
                                    @endif
                                    @if($house->catatan)
                                        <div class="text-xs text-slate-500 font-normal mt-0.5 truncate max-w-xs" title="{{ $house->catatan }}">
                                            {{ $house->catatan }}
                                        </div>
                                    @endif
                                </td>

                                <!-- RT -->
                                <td class="py-4 px-3 text-center font-bold text-slate-800">
                                    <span class="px-2 py-1 rounded-md bg-slate-100 text-xs">
                                        RT {{ $house->rt?->nomor_rt }}
                                    </span>
                                </td>

                                <!-- Kepala Keluarga -->
                                <td class="py-4 px-5">
                                    @if($head)
                                        <div class="font-bold text-slate-900">{{ $head->nama_lengkap }}</div>
                                        <div class="text-xs text-slate-500 font-mono">NIK: {{ $head->nik }}</div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Belum ada penghuni</span>
                                    @endif
                                </td>

                                <!-- Kontak WhatsApp -->
                                <td class="py-4 px-5">
                                    @if($head && $head->no_wa)
                                        <a href="{{ $head->wa_link }}" target="_blank" 
                                           class="inline-flex items-center space-x-1.5 text-emerald-800 hover:text-emerald-900 font-semibold text-xs bg-emerald-50 hover:bg-emerald-100/80 px-2.5 py-1 rounded-lg border border-emerald-200 transition shadow-2xs">
                                            <svg class="w-3.5 h-3.5 fill-[#25D366] shrink-0" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                            <span class="font-mono">{{ $head->formatted_wa }}</span>
                                        </a>
                                    @else
                                        <span class="text-slate-400 font-bold text-sm select-none" title="Tidak ada kontak WhatsApp">—</span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="py-4 px-4 text-center">
                                    @if($house->status_huni === 'dihuni_pemilik')
                                        <span class="inline-block px-2.5 py-1 bg-emerald-50 text-emerald-800 border border-emerald-300 font-semibold rounded-full text-xs">
                                            Dihuni Pemilik
                                        </span>
                                    @elseif($house->status_huni === 'kontrak')
                                        <span class="inline-block px-2.5 py-1 bg-sky-50 text-sky-800 border border-sky-300 font-semibold rounded-full text-xs">
                                            Kontrak / Kost
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-1 bg-slate-100 text-slate-700 border border-slate-300 font-semibold rounded-full text-xs">
                                            Kosong
                                        </span>
                                    @endif
                                </td>

                                <!-- Jiwa -->
                                <td class="py-4 px-3 text-center font-bold text-slate-900">
                                    <span class="text-sm">{{ $house->total_jiwa }}</span>
                                </td>

                                <!-- Aksi -->
                                <td class="py-3 px-5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1.5">
                                        <a href="{{ route('houses.show', $house->id) }}" 
                                           wire:navigate
                                           class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 border border-slate-200/80 hover:border-emerald-300 inline-flex items-center justify-center transition shadow-2xs group"
                                           title="Lihat Detail Rumah & Penghuni">
                                            <svg class="w-4 h-4 text-slate-500 group-hover:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        @if($user->canManageHouse())
                                        <button type="button" wire:click="openEditModal({{ $house->id }})" 
                                                class="w-8 h-8 rounded-lg bg-white hover:bg-slate-100 text-slate-600 hover:text-slate-900 border border-slate-200 inline-flex items-center justify-center transition shadow-2xs group"
                                                title="Ubah Data Rumah">
                                            <svg class="w-4 h-4 text-slate-500 group-hover:text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        @endif
                                        @if($user->canDeleteHouse())
                                        <button type="button" wire:click="confirmDelete({{ $house->id }})" 
                                                class="w-8 h-8 rounded-lg bg-rose-50/60 hover:bg-rose-100 text-rose-600 hover:text-rose-700 border border-rose-200/80 inline-flex items-center justify-center transition shadow-2xs group"
                                                title="Hapus Rumah">
                                            <svg class="w-4 h-4 text-rose-500 group-hover:text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <p class="text-base font-bold text-slate-800">Tidak ada unit rumah yang sesuai</p>
                                    <p class="text-xs text-slate-500 mt-1">Ubah kata kunci pencarian, impor Excel, atau klik "+ Tambah Rumah".</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($houses->total() > 0)
                <div class="p-4 bg-slate-50/50 border-t border-slate-100">
                    {{ $houses->links() }}
                </div>
            @endif
        </div>
    @else
        <!-- TAB ANTREAN SENSUS WARGA MANDIRI -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Pengajuan Sensus Mandiri dari Warga</h2>
                    <p class="text-xs text-slate-600 mt-0.5">Tinjau dan setujui pendaftaran data keluarga mandiri sebelum dimasukkan resmi ke database sistem.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-slate-500">Cari Pengajuan:</span>
                    <input type="text" wire:model.live.debounce.200ms="search" placeholder="Cari tiket, rumah, atau nama..." class="text-xs rounded-lg border border-slate-300 py-1.5 px-3 w-56 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition bg-white">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 font-semibold uppercase text-xs tracking-wider border-b border-slate-200">
                            <th class="py-3 px-5">Kode Tiket</th>
                            <th class="py-3 px-4">Rumah & Wilayah</th>
                            <th class="py-3 px-5">Pelapor & Kontak</th>
                            <th class="py-3 px-3 text-center">Jumlah Jiwa</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4">Tanggal Masuk</th>
                            <th class="py-3 px-5 text-right">Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                        @if($sensusSubmissions)
                            @forelse($sensusSubmissions as $submission)
                                <tr class="hover:bg-slate-50 transition-colors {{ $submission->status === 'menunggu_verifikasi' ? 'bg-amber-50/30 font-semibold' : '' }}">
                                    <!-- Tiket -->
                                    <td class="py-3.5 px-5">
                                        <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded text-[11px] border border-slate-200">
                                            {{ $submission->kode_tiket }}
                                        </span>
                                    </td>

                                    <!-- Rumah & RT -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900">{{ $submission->nomor_rumah }}</div>
                                        <div class="text-[11px] text-slate-500">
                                            RT {{ str_pad((string)((int)$submission->rt?->nomor_rt), 2, '0', STR_PAD_LEFT) }} &bull; {{ ucwords(str_replace('_', ' ', $submission->status_huni)) }}
                                        </div>
                                    </td>

                                    <!-- Pelapor & Kontak -->
                                    <td class="py-3.5 px-5">
                                        <div class="font-bold text-slate-900">{{ $submission->nama_pengirim }}</div>
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $submission->no_wa_pengirim) }}" target="_blank" class="text-[11px] font-mono text-emerald-700 hover:underline inline-flex items-center gap-1.5 mt-0.5">
                                            <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            <span>{{ $submission->no_wa_pengirim }}</span>
                                        </a>
                                    </td>

                                    <!-- Jumlah Jiwa -->
                                    <td class="py-3.5 px-3 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full bg-slate-100 font-bold text-slate-800 text-[11px]">
                                            {{ $submission->total_anggota }} Jiwa
                                        </span>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-3.5 px-4 text-center">
                                        @if($submission->status === 'menunggu_verifikasi')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                                Menunggu
                                            </span>
                                        @elseif($submission->status === 'disetujui')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Disetujui
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Ditolak
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Tanggal Masuk -->
                                    <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                                        {{ $submission->created_at->format('d/m/Y H:i') }}
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button type="button" 
                                                wire:click="openSensusReview({{ $submission->id }})" 
                                                class="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 rounded-lg text-xs font-bold transition shadow-2xs flex items-center gap-1">
                                                <span>Detail</span>
                                            </button>

                                            @if($submission->status === 'menunggu_verifikasi')
                                                <button type="button" 
                                                    wire:click="approveSensus({{ $submission->id }})" 
                                                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition shadow-2xs">
                                                    Setujui
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-500">
                                        <p class="text-sm font-bold text-slate-700">Belum ada pengajuan sensus warga mandiri</p>
                                        <p class="text-xs text-slate-400 mt-1">Bagikan tautan formulir sensus di atas ke warga RT Anda melalui WhatsApp.</p>
                                    </td>
                                </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>

            @if($sensusSubmissions && $sensusSubmissions->total() > 0)
                <div class="p-4 bg-slate-50/50 border-t border-slate-100">
                    {{ $sensusSubmissions->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- ========================================== -->
    <!-- MODAL 1: FORMULIR UNIT RUMAH MANUAL -->
    <!-- ========================================== -->
    @if($showModal)
    <div x-data="{ openModal: true }" x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-lg border border-slate-200 w-full max-w-xl overflow-hidden my-8">
            <div class="px-6 py-4.5 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        {{ $editingHouseId ? 'Edit Data Unit Rumah' : 'Tambah Unit Rumah Baru' }}
                    </h2>
                    <p class="text-xs text-slate-600 mt-0.5">Lengkapi identitas fisik dan status hunian rumah warga</p>
                </div>
                <button type="button" @click="openModal = false; $wire.closeModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="saveHouse" 
                  x-data="{ 
                      statusHuni: $wire.entangle('form_status_huni'), 
                      kategoriTarif: $wire.entangle('form_kategori_tarif') 
                  }"
                  class="p-6 space-y-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Wilayah Rukun Tetangga (RT) <span class="text-rose-500">*</span>
                    </label>
                    @if($user->isAdminRw())
                        <select wire:model="form_rt_id" 
                                class="w-full h-11 px-3 border border-slate-300 rounded-lg text-base font-semibold text-slate-900 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                            <option value="">-- Pilih Wilayah RT --</option>
                            @foreach($rts as $rt)
                                <option value="{{ $rt->id }}">RT {{ $rt->nomor_rt }} (Ketua: {{ $rt->nama_ketua ?? '-' }})</option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" readonly disabled 
                               value="RT {{ $user->rt?->nomor_rt }} (Terkunci otomatis akun pengurus RT)"
                               class="w-full h-11 px-3 border border-slate-200 bg-slate-100 rounded-lg text-base font-semibold text-slate-600 cursor-not-allowed">
                        <input type="hidden" wire:model="form_rt_id">
                    @endif
                    @error('form_rt_id') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alamat Rumah <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" wire:model="form_nomor_rumah" 
                           placeholder="Contoh: Blok B3 No. 14"
                           class="w-full h-11 px-3.5 border border-slate-300 rounded-lg text-base font-medium text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                    @error('form_nomor_rumah') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Status Hunian Saat Ini <span class="text-rose-500">*</span>
                    </span>
                    <div class="grid grid-cols-3 gap-2.5">
                        <button type="button" @click="statusHuni = 'dihuni_pemilik'" 
                                :class="statusHuni === 'dihuni_pemilik' ? 'border-emerald-600 bg-emerald-50 text-emerald-950 ring-2 ring-emerald-500/20 shadow-xs' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700'"
                                class="p-3 rounded-xl border-2 transition-all duration-100 text-center flex flex-col items-center justify-center space-y-1">
                            <span class="text-xs font-bold flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full" :class="statusHuni === 'dihuni_pemilik' ? 'bg-emerald-600' : 'bg-slate-300'"></span>
                                <span>Dihuni Pemilik</span>
                            </span>
                            <span class="text-[10px]" :class="statusHuni === 'dihuni_pemilik' ? 'text-emerald-700 font-medium' : 'text-slate-500'">Rumah warga tetap</span>
                        </button>

                        <button type="button" @click="statusHuni = 'kontrak'" 
                                :class="statusHuni === 'kontrak' ? 'border-sky-600 bg-sky-50 text-sky-950 ring-2 ring-sky-500/20 shadow-xs' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700'"
                                class="p-3 rounded-xl border-2 transition-all duration-100 text-center flex flex-col items-center justify-center space-y-1">
                            <span class="text-xs font-bold flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full" :class="statusHuni === 'kontrak' ? 'bg-sky-600' : 'bg-slate-300'"></span>
                                <span>Kontrak / Kost</span>
                            </span>
                            <span class="text-[10px]" :class="statusHuni === 'kontrak' ? 'text-sky-700 font-medium' : 'text-slate-500'">Penyewa musiman</span>
                        </button>

                        <button type="button" @click="statusHuni = 'kosong'" 
                                :class="statusHuni === 'kosong' ? 'border-slate-600 bg-slate-100 text-slate-950 ring-2 ring-slate-400/20 shadow-xs' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700'"
                                class="p-3 rounded-xl border-2 transition-all duration-100 text-center flex flex-col items-center justify-center space-y-1">
                            <span class="text-xs font-bold flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full" :class="statusHuni === 'kosong' ? 'bg-slate-700' : 'bg-slate-300'"></span>
                                <span>Kosong</span>
                            </span>
                            <span class="text-[10px]" :class="statusHuni === 'kosong' ? 'text-slate-700 font-medium' : 'text-slate-500'">Belum dihuni</span>
                        </button>
                    </div>
                    @error('form_status_huni') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Kategori Tarif Iuran <span class="text-rose-500">*</span>
                    </span>
                    <div class="grid grid-cols-3 gap-2 border border-slate-200 p-1.5 rounded-lg bg-slate-50">
                        <button type="button" @click="kategoriTarif = 'standar'" 
                                :class="kategoriTarif === 'standar' ? 'bg-white text-slate-900 shadow-sm font-bold border border-slate-200' : 'text-slate-600 hover:text-slate-900 border border-transparent'"
                                class="px-3 py-2.5 rounded-md text-center transition-all duration-100 text-xs font-semibold">
                            <span>Standar</span>
                        </button>

                        <button type="button" @click="kategoriTarif = 'usaha'" 
                                :class="kategoriTarif === 'usaha' ? 'bg-white text-slate-900 shadow-sm font-bold border border-slate-200' : 'text-slate-600 hover:text-slate-900 border border-transparent'"
                                class="px-3 py-2.5 rounded-md text-center transition-all duration-100 text-xs font-semibold">
                            <span>Tempat Usaha</span>
                        </button>

                        <button type="button" @click="kategoriTarif = 'kosong'" 
                                :class="kategoriTarif === 'kosong' ? 'bg-white text-slate-900 shadow-sm font-bold border border-slate-200' : 'text-slate-600 hover:text-slate-900 border border-transparent'"
                                class="px-3 py-2.5 rounded-md text-center transition-all duration-100 text-xs font-semibold">
                            <span>Khusus / Kosong</span>
                        </button>
                    </div>
                    @error('form_kategori_tarif') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Catatan Rumah (Opsional)
                    </label>
                    <textarea wire:model="form_catatan" rows="2" 
                              placeholder="Keterangan tambahan..."
                              class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none placeholder:text-slate-400"></textarea>
                    @error('form_catatan') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end space-x-2.5 pt-4 border-t border-slate-100">
                    <button type="button" @click="openModal = false; $wire.closeModal()" 
                            class="h-10 px-4 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="h-10 px-5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-2xs transition">
                        Simpan Data Rumah
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- ========================================== -->
    <!-- MODAL 2: BATCH IMPORT EXCEL WARGA -->
    <!-- ========================================== -->
    @if($showImportModal)
    <div x-data="{ openImport: true }" x-show="openImport" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full {{ $importStep === 'preview' ? 'max-w-4xl' : 'max-w-xl' }} overflow-hidden my-8 transition-all">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            Import Data Warga & Rumah dari Excel
                        </h2>
                        <p class="text-xs text-slate-500">
                            {{ $importStep === 'upload' ? 'Langkah 1: Unduh template dan unggah berkas .xlsx' : 'Langkah 2: Tinjau data sebelum disimpan ke database' }}
                        </p>
                    </div>
                </div>
                <button type="button" wire:click="closeImportModal" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                @if($importStep === 'upload')
                    <div class="space-y-6">
                        <!-- Step A: Unduh Template Resmi -->
                        <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h3 class="text-xs font-bold text-emerald-950 uppercase tracking-wide">1. Format Template Resmi</h3>
                                <p class="text-xs text-emerald-800 mt-0.5">Gunakan format Excel standar agar kolom dan data keluarga terbaca presisi.</p>
                            </div>
                            <a href="{{ route('residents.import.template', ['rt_id' => $importRtId]) }}" target="_blank" 
                               class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition shadow-2xs shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Unduh Template .XLSX
                            </a>
                        </div>

                        <!-- Step B: Pilih Wilayah RT (Jika Admin RW) -->
                        @if($user->isAdminRw())
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Wilayah RT Tujuan Import <span class="text-rose-500">*</span>
                                </label>
                                <select wire:model="importRtId" class="w-full text-xs font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl py-2.5 px-3 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                                    @foreach($rts as $rt)
                                        <option value="{{ $rt->id }}">RT {{ $rt->nomor_rt }} (Ketua: {{ $rt->nama_ketua ?? '-' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div class="text-xs text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-200">
                                Data akan otomatis diimpor ke lingkungan: <strong class="text-slate-900">RT {{ $user->rt?->nomor_rt }}</strong>
                            </div>
                        @endif

                        <!-- Step C: Upload Berkas -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Unggah Berkas Excel (.xlsx / .xls) <span class="text-rose-500">*</span>
                            </label>
                            <input type="file" wire:model="importFile" accept=".xlsx,.xls" 
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 border border-slate-300 rounded-xl cursor-pointer">
                            @error('importFile') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                            <button type="button" wire:click="closeImportModal" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-semibold">
                                Batal
                            </button>
                            <button type="button" 
                                wire:click="processImportPreview" 
                                wire:loading.attr="disabled"
                                wire:target="importFile, processImportPreview"
                                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                                <span wire:loading.remove wire:target="importFile, processImportPreview">Periksa & Tinjau Berkas &rarr;</span>
                                <span wire:loading wire:target="importFile">Mengunggah...</span>
                                <span wire:loading wire:target="processImportPreview">Menganalisis...</span>
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Step: Preview Hasil Validasi -->
                    <div class="space-y-5">
                        <!-- Ringkasan Kartu -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200">
                                <span class="text-[11px] font-bold text-emerald-800 uppercase block">Rumah Dideteksi</span>
                                <span class="text-2xl font-extrabold text-emerald-950">{{ $importNewHousesCount }}</span>
                                <span class="text-[10px] text-emerald-700 block">Unit tempat tinggal</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-sky-50 border border-sky-200">
                                <span class="text-[11px] font-bold text-sky-800 uppercase block">Total Warga Valid</span>
                                <span class="text-2xl font-extrabold text-sky-950">{{ count($importValidRows) }}</span>
                                <span class="text-[10px] text-sky-700 block">Jiwa siap disimpan</span>
                            </div>

                            <div class="p-3.5 rounded-xl {{ count($importErrors) > 0 ? 'bg-rose-50 border border-rose-200' : 'bg-slate-50 border border-slate-200' }}">
                                <span class="text-[11px] font-bold {{ count($importErrors) > 0 ? 'text-rose-800' : 'text-slate-600' }} uppercase block">Peringatan / Error</span>
                                <span class="text-2xl font-extrabold {{ count($importErrors) > 0 ? 'text-rose-950' : 'text-slate-700' }}">{{ count($importErrors) }}</span>
                                <span class="text-[10px] {{ count($importErrors) > 0 ? 'text-rose-700' : 'text-slate-500' }} block">Baris bermasalah</span>
                            </div>
                        </div>

                        <!-- Notifikasi Error Jika Ada -->
                        @if(count($importErrors) > 0)
                            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs">
                                <span class="font-bold text-rose-900 block mb-1">Daftar Peringatan Validasi (Baris bermasalah akan dilewati):</span>
                                <ul class="list-disc pl-4 space-y-0.5 text-rose-800 max-h-28 overflow-y-auto">
                                    @foreach($importErrors as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Tabel Pratinjau 5 Baris Pertama -->
                        <div>
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1.5">
                                Pratinjau 5 Baris Pertama dari Berkas:
                            </span>
                            <div class="border border-slate-200 rounded-xl overflow-x-auto">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                                        <tr>
                                            <th class="py-2 px-3">Rumah</th>
                                            <th class="py-2 px-3">NIK</th>
                                            <th class="py-2 px-3">Nama Lengkap</th>
                                            <th class="py-2 px-2 text-center">L/P</th>
                                            <th class="py-2 px-3">Hubungan</th>
                                            <th class="py-2 px-3">Tanggal Lahir</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-600">
                                        @foreach($importPreviewData as $preview)
                                            <tr>
                                                <td class="py-2 px-3 font-bold text-slate-900">{{ $preview['nomor_rumah'] }}</td>
                                                <td class="py-2 px-3 font-mono text-slate-700">{{ $preview['nik'] }}</td>
                                                <td class="py-2 px-3 font-medium text-slate-900">{{ $preview['nama_lengkap'] }}</td>
                                                <td class="py-2 px-2 text-center">{{ $preview['jenis_kelamin'] }}</td>
                                                <td class="py-2 px-3">{{ ucwords(str_replace('_', ' ', $preview['status_keluarga'])) }}</td>
                                                <td class="py-2 px-3 text-slate-500">{{ $preview['tanggal_lahir'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tombol Eksekusi -->
                        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                            <button type="button" wire:click="$set('importStep', 'upload')" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-semibold">
                                &larr; Pilih Berkas Lain
                            </button>
                            <button type="button" 
                                wire:click="executeImport" 
                                wire:loading.attr="disabled"
                                class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                                <span wire:loading.remove wire:target="executeImport">Eksekusi Import {{ count($importValidRows) }} Data Warga</span>
                                <span wire:loading wire:target="executeImport">Menyimpan...</span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- ========================================== -->
    <!-- MODAL 3: DETAIL REVIEW SENSUS MANDIRI WARGA -->
    <!-- ========================================== -->
    @if($showSensusReviewModal && $reviewingSensus)
    <div x-data="{ openReview: true }" x-show="openReview" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-3xl overflow-hidden my-8">
            <div class="px-6 py-4.5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded">
                            {{ $reviewingSensus->kode_tiket }}
                        </span>
                        <h2 class="text-base font-bold text-slate-900">
                            Tinjau Pengajuan Sensus Warga
                        </h2>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Dikirim oleh {{ $reviewingSensus->nama_pengirim }} pada {{ $reviewingSensus->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <button type="button" wire:click="closeSensusReview" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-5">
                <!-- Info Tempat Tinggal & Kontak -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <div>
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Nomor Rumah</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $reviewingSensus->nomor_rumah }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Wilayah RT</span>
                        <span class="font-bold text-slate-900 text-sm">RT {{ str_pad((string)((int)$reviewingSensus->rt?->nomor_rt), 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Status Huni</span>
                        <span class="font-bold text-slate-900 text-sm">{{ ucwords(str_replace('_', ' ', $reviewingSensus->status_huni)) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Kontak WhatsApp</span>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $reviewingSensus->no_wa_pengirim) }}" target="_blank" class="font-bold text-emerald-700 text-sm hover:underline">
                            {{ $reviewingSensus->no_wa_pengirim }}
                        </a>
                    </div>
                </div>

                <!-- Info Nomor KK -->
                <div class="p-3 bg-slate-100 rounded-xl flex items-center justify-between text-xs">
                    <div>
                        <span class="text-slate-500">Nomor Kartu Keluarga (KK):</span>
                        <span class="font-mono font-bold text-slate-900 ml-1">{{ $reviewingSensus->no_kk }}</span>
                    </div>
                    <div class="font-bold text-slate-700">
                        Total: {{ $reviewingSensus->total_anggota }} Jiwa
                    </div>
                </div>

                <!-- Daftar Anggota Keluarga -->
                <div>
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">Daftar Anggota Keluarga:</h3>
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="py-2.5 px-3">NIK</th>
                                    <th class="py-2.5 px-3">Nama Lengkap</th>
                                    <th class="py-2.5 px-2 text-center">L/P</th>
                                    <th class="py-2.5 px-3">Hubungan</th>
                                    <th class="py-2.5 px-3">Tanggal Lahir</th>
                                    <th class="py-2.5 px-3">Status KTP</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach($reviewingSensus->data_keluarga ?? [] as $m)
                                    <tr>
                                        <td class="py-2.5 px-3 font-mono font-bold text-slate-900">{{ $m['nik'] }}</td>
                                        <td class="py-2.5 px-3 font-semibold">{{ $m['nama_lengkap'] }}</td>
                                        <td class="py-2.5 px-2 text-center">{{ $m['jenis_kelamin'] }}</td>
                                        <td class="py-2.5 px-3">{{ ucwords(str_replace('_', ' ', $m['status_keluarga'])) }}</td>
                                        <td class="py-2.5 px-3 text-slate-500">{{ $m['tanggal_lahir'] }}</td>
                                        <td class="py-2.5 px-3">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ ($m['status_domisili'] ?? '') === 'ktp_setempat' ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                                {{ ($m['status_domisili'] ?? '') === 'ktp_setempat' ? 'KTP Setempat' : 'Domisili Luar' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Input Catatan Tolak Jika Mode Reject Aktif -->
                @if($isRejecting)
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 space-y-2">
                        <label class="block text-xs font-bold text-rose-900 uppercase tracking-wide">
                            Alasan Penolakan / Catatan Perbaikan untuk Warga:
                        </label>
                        <textarea wire:model.defer="rejectReasonInput" rows="2" placeholder="Contoh: Mohon periksa NIK Kepala Keluarga atau lengkapi data tempat tanggal lahir..." class="w-full text-xs rounded-xl border-rose-300 py-2 px-3 focus:ring-rose-500 focus:border-rose-500"></textarea>
                    </div>
                @endif

                <!-- Footer Aksi -->
                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <button type="button" wire:click="closeSensusReview" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-semibold">
                        Tutup
                    </button>

                    <div class="flex items-center gap-2">
                        @if($reviewingSensus->status === 'menunggu_verifikasi')
                            @if(!$isRejecting)
                                <button type="button" wire:click="$set('isRejecting', true)" class="px-4 py-2 text-rose-600 hover:bg-rose-50 border border-rose-200 rounded-xl text-xs font-bold transition">
                                    Tolak Pengajuan
                                </button>
                                <button type="button" wire:click="approveSensus({{ $reviewingSensus->id }})" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                                    Setujui & Masukkan ke Sistem
                                </button>
                            @else
                                <button type="button" wire:click="$set('isRejecting', false)" class="px-3 py-2 text-slate-600 text-xs font-semibold">
                                    Batal Tolak
                                </button>
                                <button type="button" wire:click="rejectSensus({{ $reviewingSensus->id }})" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                                    Konfirmasi Tolak
                                </button>
                            @endif
                        @else
                            <span class="text-xs text-slate-500 italic">
                                Pengajuan ini telah diverifikasi dengan status: <strong class="text-slate-800">{{ $reviewingSensus->status }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ========================================== -->
    <!-- MODAL 4: KONFIRMASI HAPUS RUMAH -->
    <!-- ========================================== -->
    @if($showDeleteModal)
    <div x-data="{ openDel: true }" x-show="openDel" class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-lg border border-slate-200 w-full max-w-md p-6">
            <div class="w-11 h-11 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">Konfirmasi Hapus Unit Rumah</h3>
            <p class="text-sm text-slate-600 mb-6 font-medium">
                Yakin ingin menghapus unit rumah <strong class="text-slate-900">{{ $deletingHouseNumber }}</strong>? Seluruh data warga penghuni rumah ini juga akan terhapus.
            </p>
            <div class="flex items-center justify-end space-x-2.5">
                <button type="button" @click="openDel = false; $wire.set('showDeleteModal', false)" 
                        class="h-10 px-4 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-semibold transition">
                    Batal
                </button>
                <button type="button" wire:click="deleteHouse" 
                        class="h-10 px-4 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-xs transition">
                    Hapus Permanen
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
