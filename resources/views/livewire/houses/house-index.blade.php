<div class="space-y-6">
    <!-- Header Area Sesuai Bab 6.1 baru.md -->
    <div class="bg-white rounded-xl p-5 sm:p-6 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Daftar Rumah & Hunian
            </h1>
            <p class="text-sm text-slate-600 mt-1 font-medium">
                Kelola unit hunian, status kepemilikan, dan daftar kepala keluarga di lingkungan
            </p>
        </div>

        @if($user->canManageHouse())
        <div>
            <button type="button" wire:click="openCreateModal" 
                    class="h-11 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm px-4 py-2.5 rounded-lg shadow-sm transition inline-flex items-center space-x-2">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Rumah Baru</span>
            </button>
        </div>
        @endif
    </div>

    <!-- Toolbar / Filter Bar Sesuai Bab 6.1 baru.md -->
    <div class="bg-white rounded-xl p-4 sm:p-5 border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center gap-3">
            <!-- Search bar ber-icon kaca pembesar -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" wire:model.live.debounce.150ms="search" 
                       wire:keydown.enter.prevent="$refresh"
                       placeholder="Cari nomor rumah atau nama warga..."
                       class="w-full h-11 pl-10 pr-10 rounded-xl border border-slate-300 text-sm text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition placeholder:text-slate-400">
                @if($search !== '')
                    <button type="button" wire:click="$set('search', '')" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 transition" title="Hapus pencarian">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                @endif
            </div>

            <!-- Filter RT (Hanya Tampil Jika Login Admin RW) -->
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

            <!-- Tab Pill Filter Status Bersih (0ms Instant Client Switching) -->
            <div x-data="{ activeTab: $wire.entangle('statusHuni').live }" 
                 class="flex items-center space-x-1 bg-slate-100 p-1 rounded-xl shrink-0 overflow-x-auto text-xs font-semibold">
                <button type="button" @click="activeTab = ''" 
                        :class="activeTab === '' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-2 rounded-lg transition">
                    Semua
                </button>
                <button type="button" @click="activeTab = 'dihuni_pemilik'" 
                        :class="activeTab === 'dihuni_pemilik' ? 'bg-white text-emerald-800 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-2 rounded-lg transition">
                    Dihuni
                </button>
                <button type="button" @click="activeTab = 'kontrak'" 
                        :class="activeTab === 'kontrak' ? 'bg-white text-sky-800 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-2 rounded-lg transition">
                    Kontrak
                </button>
                <button type="button" @click="activeTab = 'kosong'" 
                        :class="activeTab === 'kosong' ? 'bg-white text-slate-800 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-2 rounded-lg transition">
                    Kosong
                </button>
            </div>
        </div>
    </div>

    <!-- Table Design Bersih & Modern Sesuai Bab 6.1 baru.md -->
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

                            <!-- Aksi Icon Rapi & Hemat Ruang -->
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
                                <p class="text-xs text-slate-500 mt-1">Ubah kata kunci pencarian atau klik "+ Tambah Rumah Baru".</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($houses->hasPages())
            <div class="p-4 bg-slate-50/50 border-t border-slate-100">
                {{ $houses->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL FORMULIR UNIT RUMAH (BAB 5A HouseFormModal) -->
    @if($showModal)
    <div x-data="{ openModal: true }" x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-lg border border-slate-200 w-full max-w-xl overflow-hidden my-8">
            <!-- Modal Header -->
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

            <!-- Modal Body -->
            <form wire:submit.prevent="saveHouse" 
                  x-data="{ 
                      statusHuni: $wire.entangle('form_status_huni'), 
                      kategoriTarif: $wire.entangle('form_kategori_tarif') 
                  }"
                  class="p-6 space-y-5">
                <!-- 1. Pilihan RT -->
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

                <!-- 2. Nomor / Identitas Rumah -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nomor / Blok Rumah <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" wire:model="form_nomor_rumah" 
                           placeholder="Contoh: Blok B3 No. 14"
                           class="w-full h-11 px-3.5 border border-slate-300 rounded-lg text-base font-medium text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                    @error('form_nomor_rumah') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 3. Status Hunian (0ms Instant Client Switching) -->
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

                <!-- 4. Kategori Tarif Iuran (0ms Instant Client Switching) -->
                <div>
                    <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Kategori Tarif Iuran <span class="text-rose-500">*</span>
                    </span>
                    <div class="grid grid-cols-3 gap-2 border border-slate-200 p-1.5 rounded-lg bg-slate-50">
                        <button type="button" @click="kategoriTarif = 'standar'" 
                                :class="kategoriTarif === 'standar' ? 'bg-white text-slate-900 shadow-sm font-bold border border-slate-200' : 'text-slate-600 hover:text-slate-900 border border-transparent'"
                                class="px-3 py-2.5 rounded-md text-center transition-all duration-100 text-xs font-semibold">
                            <span>Standar (Warga Biasa)</span>
                        </button>

                        <button type="button" @click="kategoriTarif = 'usaha'" 
                                :class="kategoriTarif === 'usaha' ? 'bg-white text-slate-900 shadow-sm font-bold border border-slate-200' : 'text-slate-600 hover:text-slate-900 border border-transparent'"
                                class="px-3 py-2.5 rounded-md text-center transition-all duration-100 text-xs font-semibold">
                            <span>Tempat Usaha / Ruko</span>
                        </button>

                        <button type="button" @click="kategoriTarif = 'kosong'" 
                                :class="kategoriTarif === 'kosong' ? 'bg-white text-slate-900 shadow-sm font-bold border border-slate-200' : 'text-slate-600 hover:text-slate-900 border border-transparent'"
                                class="px-3 py-2.5 rounded-md text-center transition-all duration-100 text-xs font-semibold">
                            <span>Khusus / Kosong</span>
                        </button>
                    </div>
                    @error('form_kategori_tarif') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 5. Catatan (Textarea 2 baris Sesuai Bab 5A) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Catatan Rumah (Opsional)
                    </label>
                    <textarea wire:model="form_catatan" rows="2" 
                              placeholder="Contoh: Pagar hitam, ada anjing peliharaan"
                              class="w-full px-3.5 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none placeholder:text-slate-400"></textarea>
                    @error('form_catatan') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Footer Tombol -->
                <div class="flex items-center justify-end space-x-2.5 pt-4 border-t border-slate-100">
                    <button type="button" @click="openModal = false; $wire.closeModal()" 
                            class="h-11 px-4 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-sm font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="h-11 px-5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-sm transition">
                        Simpan Data Rumah
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL KONFIRMASI HAPUS -->
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
                        class="h-11 px-4 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-sm font-semibold transition">
                    Batal
                </button>
                <button type="button" wire:click="deleteHouse" 
                        class="h-11 px-4 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold shadow-sm transition">
                    Hapus Permanen
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
