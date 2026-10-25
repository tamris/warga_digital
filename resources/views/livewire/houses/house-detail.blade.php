<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ $this->getBackUrl() }}" 
           wire:navigate
           class="inline-flex items-center space-x-2 text-slate-700 hover:text-slate-900 text-sm font-semibold bg-white border border-slate-200 px-3.5 py-2 rounded-lg shadow-sm transition">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>{{ $this->getBackLabel() }}</span>
        </a>

        @if(auth()->user()->canManageHouse())
        <button type="button" wire:click="openCreateResidentModal" 
                class="h-9.5 px-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg shadow-xs inline-flex items-center space-x-1.5 transition">
            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Anggota</span>
        </button>
        @endif
    </div>

    <!-- CARD PROFIL RUMAH MODERN & KOMPAK -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6 shadow-sm space-y-4">
        <!-- Identitas Rumah & Catatan -->
        <div class="space-y-1.5">
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded-md text-xs font-extrabold bg-slate-100 text-slate-800 border border-slate-200">
                    RT {{ $house->rt?->nomor_rt }} 
                </span>
                @if($house->status_huni === 'dihuni_pemilik')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-300">
                        Dihuni Pemilik
                    </span>
                @elseif($house->status_huni === 'kontrak')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-800 border border-sky-300">
                        Kontrak / Kost
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-300">
                        Kosong
                    </span>
                @endif
            </div>

            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                {{ $house->nomor_rumah }}
            </h1>

            @if($house->catatan)
                <p class="text-xs text-slate-600 flex items-center space-x-1.5 font-medium">
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span><strong class="text-slate-700">Catatan:</strong> {{ $house->catatan }}</span>
                </p>
            @endif
        </div>

        <!-- Detail Tagihan & Ringkasan Penghuni Kompak Sejajar -->
        <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center gap-x-6 gap-y-2 text-xs">
            <div class="flex items-center space-x-1.5">
                <span class="text-slate-500 font-medium">Kategori Tarif:</span>
                <span class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded">
                    @if($house->kategori_tarif === 'standar') Standar Warga
                    @elseif($house->kategori_tarif === 'usaha') Tempat Usaha
                    @else Khusus / Kosong @endif
                </span>
            </div>

            <div class="flex items-center space-x-1.5">
                <span class="text-slate-500 font-medium">Total Penghuni:</span>
                <span class="font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">
                    {{ $residents->where('status_warga', 'aktif')->count() }} Jiwa Aktif
                </span>
            </div>

            <div class="flex items-center space-x-1.5">
                <span class="text-slate-500 font-medium">Riwayat Mutasi:</span>
                <span class="font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">
                    {{ $residents->where('status_warga', '!=', 'aktif')->count() }} Jiwa Pindah/Meninggal
                </span>
            </div>
        </div>
    </div>

    <!-- TABEL ANGGOTA KELUARGA (BAB 6.2 baru.md) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4.5 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900 tracking-tight">
                Daftar Anggota Keluarga Penghuni
            </h2>
            <span class="text-xs font-semibold text-slate-500">
                {{ $residents->count() }} orang tercatat
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 font-semibold uppercase text-xs tracking-wider border-b border-slate-200">
                        <th class="py-3.5 px-5">Nama Lengkap & NIK</th>
                        <th class="py-3.5 px-4">Kontak WA</th>
                        <th class="py-3.5 px-4">Peran</th>
                        <th class="py-3.5 px-3 text-center">L/P</th>
                        <th class="py-3.5 px-4">Usia & Lahir</th>
                        <th class="py-3.5 px-4">Pekerjaan & Agama</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody wire:loading.class="opacity-60 pointer-events-none" class="divide-y divide-slate-100 text-sm font-medium text-slate-700 transition-opacity duration-150">
                    @forelse($residents as $res)
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $res->status_warga !== 'aktif' ? 'bg-slate-50/50 text-slate-400' : '' }}">
                            <!-- 1. Nama Lengkap & NIK -->
                            <td class="py-3.5 px-5">
                                <div class="font-bold text-slate-900 text-sm">{{ $res->nama_lengkap }}</div>
                                <div class="text-xs font-mono text-slate-400 mt-0.5">NIK: {{ $res->nik }}</div>
                            </td>

                            <!-- 2. Kontak WhatsApp -->
                            <td class="py-3.5 px-4">
                                @if($res->no_wa)
                                    <a href="{{ $res->wa_link }}" target="_blank" 
                                       class="inline-flex items-center space-x-1.5 text-xs font-semibold text-emerald-800 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100/80 px-2.5 py-1 rounded-lg border border-emerald-200 transition shadow-2xs"
                                       title="Chat via WhatsApp">
                                        <svg class="w-3.5 h-3.5 fill-[#25D366] shrink-0" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                        <span class="font-mono">{{ $res->formatted_wa }}</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 font-bold text-sm select-none" title="Tidak ada kontak WhatsApp">—</span>
                                @endif
                            </td>

                            <!-- 3. Peran Keluarga -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($res->status_keluarga === 'kepala_keluarga')
                                    <span class="inline-block px-2.5 py-1 bg-slate-900 text-white font-bold text-xs rounded-md shadow-xs">
                                        Kepala Keluarga
                                    </span>
                                @elseif($res->status_keluarga === 'istri')
                                    <span class="inline-block px-2.5 py-1 bg-rose-50 text-rose-800 border border-rose-200 font-semibold text-xs rounded-md">
                                        Istri
                                    </span>
                                @elseif($res->status_keluarga === 'anak')
                                    <span class="inline-block px-2.5 py-1 bg-blue-50 text-blue-800 border border-blue-200 font-semibold text-xs rounded-md">
                                        Anak
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-1 bg-slate-100 text-slate-700 font-semibold text-xs rounded-md">
                                        Famili Lain
                                    </span>
                                @endif

                                @if($res->status_kependudukan === 'domisili_luar')
                                    <span class="block mt-1 text-[10px] font-bold text-sky-700 uppercase">
                                        Domisili Luar
                                    </span>
                                @endif
                            </td>

                            <!-- 4. L/P -->
                            <td class="py-3.5 px-3 text-center">
                                <span class="inline-block w-7 h-7 leading-7 rounded-full text-xs font-bold {{ $res->jenis_kelamin === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                                    {{ $res->jenis_kelamin }}
                                </span>
                            </td>

                            <!-- 5. Usia & Lahir -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800 flex items-center space-x-1.5">
                                    <span>{{ $res->umur }} Tahun</span>
                                    @if($res->umur >= 60)
                                        <span class="text-[10px] font-bold bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded">
                                            Lansia
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    {{ $res->tempat_lahir }}, {{ $res->tanggal_lahir ? $res->tanggal_lahir->format('d/m/Y') : '-' }}
                                </div>
                            </td>

                            <!-- 6. Pekerjaan & Agama -->
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-800">{{ $res->pekerjaan ?: '-' }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $res->agama ?: '-' }}</div>
                            </td>

                            <!-- 7. Status Warga -->
                            <td class="py-3.5 px-4 text-center">
                                @if($res->status_warga === 'aktif')
                                    <span class="inline-block px-2.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold rounded-full text-xs">
                                        Aktif
                                    </span>
                                @elseif($res->status_warga === 'pindah')
                                    <span class="inline-block px-2.5 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 font-semibold rounded-full text-xs">
                                        Pindah
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 font-semibold rounded-full text-xs">
                                        Meninggal
                                    </span>
                                @endif
                            </td>

                            <!-- 8. Aksi -->
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center space-x-1.5">
                                    @if(auth()->user()->canManageHouse())
                                    <button type="button" wire:click="openEditResidentModal({{ $res->id }})" 
                                            class="w-8 h-8 rounded-lg bg-white hover:bg-slate-100 text-slate-600 hover:text-slate-900 border border-slate-200 inline-flex items-center justify-center transition shadow-2xs group"
                                            title="Edit Data Warga">
                                        <svg class="w-4 h-4 text-slate-500 group-hover:text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>

                                    @if($res->status_warga === 'aktif')
                                        <button type="button" wire:click="confirmMutasi({{ $res->id }})" 
                                                class="w-8 h-8 rounded-lg bg-amber-50/70 hover:bg-amber-100 text-amber-800 hover:text-amber-900 border border-amber-200 inline-flex items-center justify-center transition shadow-2xs group"
                                                title="Tandai Warga Pindah / Keluar">
                                            <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        </button>
                                    @endif
                                    @endif

                                    @if(auth()->user()->canDeleteHouse())
                                    <button type="button" wire:click="confirmDeleteResident({{ $res->id }})" 
                                            class="w-8 h-8 rounded-lg bg-rose-50/60 hover:bg-rose-100 text-rose-600 hover:text-rose-700 border border-rose-200/80 inline-flex items-center justify-center transition shadow-2xs group"
                                            title="Hapus Data Warga">
                                        <svg class="w-4 h-4 text-rose-500 group-hover:text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">
                                <p class="text-base font-bold text-slate-800">Belum ada anggota keluarga di rumah ini</p>
                                <p class="text-xs text-slate-400 mt-1">Klik tombol "+ Tambah Anggota" untuk memasukkan data warga.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FORMULIR ANGGOTA WARGA (BAB 5B ResidentFormModal) -->
    @if($showResidentModal)
    <div x-data="{ 
            openRes: true, 
            jenisKelamin: $wire.entangle('form_jenis_kelamin'), 
            statusKependudukan: $wire.entangle('form_status_kependudukan'),
            statusKeluarga: $wire.entangle('form_status_keluarga')
         }" 
         x-show="openRes" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-lg border border-slate-200 w-full max-w-2xl overflow-hidden my-8">
            <!-- Modal Header -->
            <div class="px-6 py-4.5 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        {{ $editingResidentId ? 'Edit Data Anggota Warga' : 'Formulir Tambah Anggota Warga' }}
                    </h2>
                    <p class="text-xs text-slate-600 mt-0.5">Unit: {{ $house->nomor_rumah }} • Wilayah RT {{ $house->rt?->nomor_rt }}</p>
                </div>
                <button type="button" @click="openRes = false; $wire.closeResidentModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Form Body Sesuai 10 Field Bab 5B -->
            <form wire:submit.prevent="saveResident" 
                  class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- 1. No KK (16 digit) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nomor Kartu Keluarga (No. KK) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" inputmode="numeric" pattern="[0-9]*" wire:model="form_no_kk" maxlength="16"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               placeholder="16 digit angka No. KK"
                               class="w-full h-11 px-3.5 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                        @error('form_no_kk') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- 2. NIK (16 digit, font-mono) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            NIK (Nomor Induk Kependudukan) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" inputmode="numeric" pattern="[0-9]*" wire:model="form_nik" maxlength="16"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               placeholder="16 digit angka NIK"
                               class="w-full h-11 px-3.5 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                        @error('form_nik') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- 3. Nama Lengkap (Kapital sesuai KTP) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Lengkap (Sesuai KTP) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" wire:model="form_nama_lengkap" 
                           oninput="this.value = this.value.toUpperCase()"
                           placeholder="Contoh: AHMAD HIDAYAT"
                           class="w-full h-11 px-3.5 border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 uppercase focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                    @error('form_nama_lengkap') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 4. Jenis Kelamin (2 Tombol Segmented Besar Sesuai Bab 5B) -->
                <div>
                    <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Jenis Kelamin <span class="text-rose-500">*</span>
                    </span>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="jenisKelamin = 'L'" 
                                :class="jenisKelamin === 'L' ? 'border-emerald-600 bg-emerald-50 text-emerald-900 shadow-xs' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'"
                                class="h-11 rounded-xl border-2 font-bold text-sm transition-all duration-100 flex items-center justify-center space-x-2">
                            <span class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center"
                                  :class="jenisKelamin === 'L' ? 'border-emerald-600' : 'border-slate-400'">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600" x-show="jenisKelamin === 'L'"></span>
                            </span>
                            <span>Laki-laki (L)</span>
                        </button>

                        <button type="button" @click="jenisKelamin = 'P'" 
                                :class="jenisKelamin === 'P' ? 'border-emerald-600 bg-emerald-50 text-emerald-900 shadow-xs' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'"
                                class="h-11 rounded-xl border-2 font-bold text-sm transition-all duration-100 flex items-center justify-center space-x-2">
                            <span class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center"
                                  :class="jenisKelamin === 'P' ? 'border-emerald-600' : 'border-slate-400'">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600" x-show="jenisKelamin === 'P'"></span>
                            </span>
                            <span>Perempuan (P)</span>
                        </button>
                    </div>
                    @error('form_jenis_kelamin') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 5. Tempat & Tanggal Lahir (2 Kolom Bersisian Sesuai Bab 5B) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tempat Lahir <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" wire:model="form_tempat_lahir" 
                               oninput="this.value = this.value.toUpperCase()"
                               placeholder="Contoh: Jakarta"
                               class="w-full h-11 px-3.5 border border-slate-300 rounded-xl text-sm font-medium text-slate-900 uppercase focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                        @error('form_tempat_lahir') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tanggal Lahir <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" wire:model="form_tanggal_lahir" 
                               class="w-full h-11 px-3.5 border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                        @error('form_tanggal_lahir') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- 6. Agama & Pekerjaan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Agama <span class="text-rose-500">*</span>
                        </label>
                        <select wire:model="form_agama" 
                                class="w-full h-11 px-3 border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                            <option value="Islam">Islam</option>
                            <option value="Kristen">Kristen</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                        @error('form_agama') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Pekerjaan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" wire:model="form_pekerjaan" 
                               oninput="this.value = this.value.toUpperCase()"
                               placeholder="Contoh: Karyawan Swasta, PNS..."
                               class="w-full h-11 px-3.5 border border-slate-300 rounded-xl text-sm font-medium text-slate-900 uppercase focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                        @error('form_pekerjaan') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- 7. Peran / Status Keluarga -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Peran / Status dalam Keluarga <span class="text-rose-500">*</span>
                    </label>
                    <select x-model="statusKeluarga" wire:model="form_status_keluarga" 
                            class="w-full h-11 px-3 border border-slate-300 rounded-xl text-sm font-bold text-slate-900 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                        <option value="kepala_keluarga">Kepala Keluarga (Kontak Utama Unit Rumah)</option>
                        <option value="istri">Istri</option>
                        <option value="anak">Anak</option>
                        <option value="famili_lain">Famili Lain</option>
                    </select>
                    @error('form_status_keluarga') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 8. Status Kependudukan (Pilihan Tombol Sesuai Bab 5B) -->
                <div>
                    <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Status Kependudukan <span class="text-rose-500">*</span>
                    </span>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="statusKependudukan = 'ktp_setempat'" 
                                :class="statusKependudukan === 'ktp_setempat' ? 'border-emerald-600 bg-emerald-50 text-emerald-900 shadow-xs' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'"
                                class="h-11 rounded-xl border-2 font-bold text-xs transition-all duration-100 flex items-center justify-center space-x-2">
                            <span class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center"
                                  :class="statusKependudukan === 'ktp_setempat' ? 'border-emerald-600' : 'border-slate-400'">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600" x-show="statusKependudukan === 'ktp_setempat'"></span>
                            </span>
                            <span>KTP Setempat (Tetap)</span>
                        </button>

                        <button type="button" @click="statusKependudukan = 'domisili_luar'" 
                                :class="statusKependudukan === 'domisili_luar' ? 'border-sky-600 bg-sky-50 text-sky-900 shadow-xs' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'"
                                class="h-11 rounded-xl border-2 font-bold text-xs transition-all duration-100 flex items-center justify-center space-x-2">
                            <span class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center"
                                  :class="statusKependudukan === 'domisili_luar' ? 'border-sky-600' : 'border-slate-400'">
                                <span class="w-1.5 h-1.5 rounded-full bg-sky-600" x-show="statusKependudukan === 'domisili_luar'"></span>
                            </span>
                            <span>Domisili Luar (Pendatang/Kontrak)</span>
                        </button>
                    </div>
                    @error('form_status_kependudukan') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 9. Nomor WhatsApp (Sesuai Bab 5B baru.md) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nomor WhatsApp
                        <span x-show="statusKeluarga === 'kepala_keluarga'" class="text-rose-500 font-bold">* (Wajib Diisi untuk Kepala Keluarga)</span>
                        <span x-show="statusKeluarga !== 'kepala_keluarga'" class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <input type="tel" inputmode="numeric" maxlength="17" wire:model="form_no_wa" 
                           oninput="let v = this.value.replace(/[^0-9]/g, '').slice(0, 14); let p = v.match(/.{1,4}/g); this.value = p ? p.join('-') : '';"
                           placeholder="Contoh: 0808-9898-9898"
                           class="w-full h-11 px-3.5 border border-slate-300 rounded-lg text-sm font-mono text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                    <p class="text-xs text-slate-500 mt-1">Digunakan untuk mengirim bukti iuran dan surat pengantar via WA.</p>
                    @error('form_no_wa') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 10. Status Warga -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Status Keaktifan Warga <span class="text-rose-500">*</span>
                    </label>
                    <select wire:model="form_status_warga" 
                            class="w-full h-11 px-3 border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                        <option value="aktif">Aktif (Tinggal di Unit Rumah Ini)</option>
                        <option value="pindah">Pindah Keluar</option>
                        <option value="meninggal">Meninggal Dunia</option>
                    </select>
                    @error('form_status_warga') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Footer Tombol -->
                <div class="flex items-center justify-end space-x-2.5 pt-4 border-t border-slate-100">
                    <button type="button" @click="openRes = false; $wire.closeResidentModal()" 
                            class="h-11 px-4 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 text-sm font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-sm transition">
                        Simpan Data Warga
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL KONFIRMASI MUTASI PINDAH (BAB 6.2) -->
    @if($showMutasiModal)
    <div x-data="{ openMut: true }" x-show="openMut" class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-lg border border-slate-200 w-full max-w-md p-6">
            <div class="w-11 h-11 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center mb-4 border border-amber-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">Konfirmasi Mutasi Pindah Warga</h3>
            <p class="text-sm text-slate-600 mb-6 font-medium">
                Tandai bahwa <strong class="text-slate-900">{{ $mutasiResidentName }}</strong> telah pindah keluar?
                <br><br>
                <span class="text-slate-500 text-xs">Status warga akan diubah menjadi <strong>Pindah</strong> dan tidak dihitung sebagai penghuni aktif, namun riwayat data tetap tersimpan rapi.</span>
            </p>
            <div class="flex items-center justify-end space-x-2.5">
                <button type="button" @click="openMut = false; $wire.set('showMutasiModal', false)" 
                        class="h-11 px-4 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-sm font-semibold transition">
                    Batal
                </button>
                <button type="button" wire:click="executeMutasi" 
                        class="h-11 px-4 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold shadow-sm transition">
                    Tandai Pindah
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL KONFIRMASI HAPUS WARGA -->
    @if($showDeleteModal)
    <div x-data="{ openDel: true }" x-show="openDel" class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-lg border border-slate-200 w-full max-w-md p-6">
            <div class="w-11 h-11 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center mb-4 border border-rose-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">Hapus Data Warga</h3>
            <p class="text-sm text-slate-600 mb-6 font-medium">
                Yakin ingin menghapus data warga <strong class="text-slate-900">{{ $deleteResidentName }}</strong> secara permanen?
            </p>
            <div class="flex items-center justify-end space-x-2.5">
                <button type="button" @click="openDel = false; $wire.set('showDeleteModal', false)" 
                        class="h-11 px-4 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-sm font-semibold transition">
                    Batal
                </button>
                <button type="button" wire:click="deleteResident" 
                        class="h-11 px-4 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold shadow-sm transition">
                    Hapus
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
