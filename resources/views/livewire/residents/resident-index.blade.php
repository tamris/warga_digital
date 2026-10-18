<div class="space-y-6">
    <!-- Header Halaman & Tombol Ekspor Excel Sesuai Bab 6.3 baru.md -->
    <div class="bg-white rounded-xl p-5 sm:p-6 border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Master Data Warga RW
            </h1>
            <p class="text-sm text-slate-600 mt-1 font-medium">
                Pusat data seluruh warga untuk pencarian cepat, pelaporan, dan ekspor arsip
            </p>
        </div>

        <div>
            <a href="{{ $exportUrl }}" 
               class="h-11 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm px-4 py-2 rounded-lg shadow-sm inline-flex items-center space-x-2 transition">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Unduh Data Excel (.xlsx)</span>
            </a>
        </div>
    </div>

    <!-- PANEL FILTER & PENCARIAN CEPAT (BAB 6.3 baru.md) -->
    <div class="bg-white rounded-xl p-4 sm:p-5 border border-slate-200 shadow-sm space-y-4">
        <!-- Baris 1: Pencarian & Dropdown -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            <!-- Search Input -->
            <div class="{{ $user->isAdminRw() ? 'md:col-span-6' : 'md:col-span-8' }}">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" wire:model.live.debounce.150ms="search" 
                           wire:keydown.enter.prevent="$refresh"
                           placeholder="Cari nama lengkap, NIK, No. KK, atau pekerjaan..."
                           class="w-full h-11 pl-10 pr-10 rounded-xl border border-slate-300 text-sm text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none placeholder:text-slate-400">
                    @if($search !== '')
                        <button type="button" wire:click="$set('search', '')" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 transition" title="Hapus pencarian">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Filter RT (Khusus Admin RW) -->
            @if($user->isAdminRw())
            <div class="md:col-span-3">
                <select wire:model.live="selectedRtId" 
                        class="w-full h-11 px-3 border border-slate-300 rounded-xl text-sm font-semibold text-slate-700 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                    <option value="">Semua Wilayah RT</option>
                    @foreach($rts as $rt)
                        <option value="{{ $rt->id }}">RT {{ $rt->nomor_rt }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Status Warga -->
            <div class="{{ $user->isAdminRw() ? 'md:col-span-3' : 'md:col-span-4' }}">
                <select wire:model.live="statusWarga" 
                        class="w-full h-11 px-3 border border-slate-300 rounded-xl text-sm font-semibold text-slate-700 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none">
                    <option value="aktif">Warga Aktif</option>
                    <option value="pindah">Riwayat Pindah Keluar</option>
                    <option value="meninggal">Warga Meninggal</option>
                    <option value="semua">Semua Status Warga</option>
                </select>
            </div>
        </div>

        <!-- Baris 2: Chips Filter Cepat (0ms Instant Client Switching) -->
        <div x-data="{
                filterLansia: $wire.entangle('filterLansia').live,
                filterBalita: $wire.entangle('filterBalita').live,
                filterDomisiliLuar: $wire.entangle('filterDomisiliLuar').live,
                statusWarga: $wire.entangle('statusWarga').live,
                search: $wire.entangle('search')
             }" 
             class="pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2">
            <span class="text-xs font-semibold text-slate-500 mr-1">Filter Cepat:</span>

            <!-- Filter Semua -->
            <button type="button" 
                    @click="filterLansia = false; filterBalita = false; filterDomisiliLuar = false; statusWarga = 'aktif'; search = ''; $wire.resetFilters()" 
                    :class="(!filterLansia && !filterBalita && !filterDomisiliLuar && statusWarga === 'aktif' && !search) ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'"
                    class="h-9 px-3 rounded-lg text-xs font-semibold transition border">
                <span>Semua</span>
            </button>

            <!-- Filter Lansia (>= 60 thn) -->
            <button type="button" 
                    @click="filterLansia = !filterLansia" 
                    :class="filterLansia ? 'bg-amber-600 text-white border-amber-700 shadow-sm' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'"
                    class="h-9 px-3 rounded-lg text-xs font-semibold transition flex items-center space-x-2 border">
                <span class="w-2 h-2 rounded-full" :class="filterLansia ? 'bg-white' : 'bg-amber-500'"></span>
                <span>Lansia (≥ 60 Thn)</span>
            </button>

            <!-- Filter Balita (< 5 thn) -->
            <button type="button" 
                    @click="filterBalita = !filterBalita" 
                    :class="filterBalita ? 'bg-pink-600 text-white border-pink-700 shadow-sm' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'"
                    class="h-9 px-3 rounded-lg text-xs font-semibold transition flex items-center space-x-2 border">
                <span class="w-2 h-2 rounded-full" :class="filterBalita ? 'bg-white' : 'bg-pink-500'"></span>
                <span>Balita (< 5 Thn)</span>
            </button>

            <!-- Filter Warga Domisili Luar -->
            <button type="button" 
                    @click="filterDomisiliLuar = !filterDomisiliLuar" 
                    :class="filterDomisiliLuar ? 'bg-sky-600 text-white border-sky-700 shadow-sm' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'"
                    class="h-9 px-3 rounded-lg text-xs font-semibold transition flex items-center space-x-2 border">
                <span class="w-2 h-2 rounded-full" :class="filterDomisiliLuar ? 'bg-white' : 'bg-sky-500'"></span>
                <span>Warga KTP Luar (Domisili)</span>
            </button>

            <!-- Filter Status Aktif -->
            <button type="button" 
                    @click="statusWarga = (statusWarga === 'aktif' ? 'semua' : 'aktif')" 
                    :class="statusWarga === 'aktif' ? 'bg-emerald-600 text-white border-emerald-700 shadow-sm' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'"
                    class="h-9 px-3 rounded-lg text-xs font-semibold transition flex items-center space-x-2 border">
                <span class="w-2 h-2 rounded-full" :class="statusWarga === 'aktif' ? 'bg-white' : 'bg-emerald-500'"></span>
                <span>Status Aktif</span>
            </button>

            <button type="button" 
                    x-show="search || filterLansia || filterBalita || filterDomisiliLuar || statusWarga !== 'aktif' || {{ ($user->isAdminRw() && $selectedRtId) ? 'true' : 'false' }}"
                    @click="filterLansia = false; filterBalita = false; filterDomisiliLuar = false; statusWarga = 'aktif'; search = ''; $wire.resetFilters()" 
                    class="h-9 px-3 rounded-lg text-xs font-semibold text-slate-500 hover:text-slate-800 transition ml-auto flex items-center space-x-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Reset Filter</span>
            </button>
        </div>
    </div>

    <!-- TABEL MASTER WARGA MODERN & CLEAN -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 font-semibold uppercase text-xs tracking-wider border-b border-slate-200">
                        <th class="py-3.5 px-5">Nama Lengkap & NIK</th>
                        <th class="py-3.5 px-4">Kontak WA</th>
                        <th class="py-3.5 px-4">Rumah & RT</th>
                        <th class="py-3.5 px-4">Peran</th>
                        <th class="py-3.5 px-3 text-center">L/P</th>
                        <th class="py-3.5 px-4">Usia & Profesi</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
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

                            <!-- 3. Alamat Rumah & RT -->
                            <td class="py-3.5 px-4">
                                @if($res->house)
                                    <a href="{{ route('houses.show', $res->house_id) }}" 
                                       wire:navigate
                                       class="font-bold text-slate-900 hover:text-emerald-700 transition block text-sm">
                                        {{ $res->house->nomor_rumah }}
                                    </a>
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 mt-0.5">
                                        RT {{ $res->house->rt?->nomor_rt }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum ada unit</span>
                                @endif
                            </td>

                            <!-- 4. Peran Keluarga -->
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

                            <!-- 5. L/P -->
                            <td class="py-3.5 px-3 text-center">
                                <span class="inline-block w-7 h-7 leading-7 rounded-full text-xs font-bold {{ $res->jenis_kelamin === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                                    {{ $res->jenis_kelamin }}
                                </span>
                            </td>

                            <!-- 6. Usia & Profesi -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800 flex items-center space-x-1.5">
                                    <span>{{ $res->umur }} Tahun</span>
                                    @if($res->umur >= 60)
                                        <span class="text-[10px] font-bold bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded">
                                            Lansia
                                        </span>
                                    @elseif($res->umur < 5)
                                        <span class="text-[10px] font-bold bg-pink-100 text-pink-800 px-1.5 py-0.5 rounded">
                                            Balita
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    {{ $res->pekerjaan ?: '-' }}
                                </div>
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
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                @if($res->house_id)
                                    <a href="{{ route('houses.show', $res->house_id) }}" 
                                       wire:navigate
                                       class="w-8 h-8 rounded-lg bg-white hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 border border-slate-200 hover:border-emerald-300 inline-flex items-center justify-center transition shadow-2xs group"
                                       title="Buka Detail Rumah & Anggota Keluarga">
                                        <svg class="w-4 h-4 text-slate-500 group-hover:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">
                                <p class="text-base font-bold text-slate-800">Tidak ada data warga yang cocok</p>
                                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau matikan filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($residents->total() > 0)
            <div class="p-4 bg-slate-50/50 border-t border-slate-100">
                {{ $residents->links() }}
            </div>
        @endif
    </div>
</div>
