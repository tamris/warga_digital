<div class="space-y-5">
    <!-- 1. HERO HEADER KOMPAK (HEMAT TEMPAT & TANPA REDUNDANSI WILAYAH KERJA) -->
    <div class="bg-white dark:bg-[#111b2f] rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-[#1d2c4d] shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3.5">
        <div>
            <div class="flex items-center space-x-2 mb-1.5">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                    {{ $user->role_label }}
                </span>
                <span class="text-xs text-slate-300 dark:text-slate-600">•</span>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold">
                    {{ $user->rt ? 'Lingkungan RT ' . $user->rt->nomor_rt : 'Admin RW (Seluruh Wilayah RT)' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                {{ $greeting }}, {{ $user->nama }}
            </h1>
        </div>

        <div>
            @if($user->isAdminRw())
                <!-- Pemilih Global Tingkat RW Khusus Admin RW -->
                <div class="bg-slate-50 dark:bg-[#0c1427] border border-slate-200 dark:border-[#1d2c4d] rounded-xl p-2 flex items-center space-x-2.5 shrink-0 shadow-2xs">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    </div>
                    <div>
                        <label for="filter-rt" class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                            Filter Wilayah RT:
                        </label>
                        <select id="filter-rt" wire:model.live="selectedRtId" 
                                class="bg-transparent text-xs font-bold text-slate-900 dark:text-slate-100 focus:outline-none cursor-pointer pr-2">
                            <option value="" class="bg-white dark:bg-[#111b2f] text-slate-900 dark:text-slate-100">Seluruh RT (Semua)</option>
                            @foreach($rts as $rt)
                                <option value="{{ $rt->id }}" class="bg-white dark:bg-[#111b2f] text-slate-900 dark:text-slate-100">RT {{ $rt->nomor_rt }} ({{ $rt->nama_ketua ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @else
                <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-xs font-bold text-emerald-800 dark:text-emerald-300 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Sistem Aktif • RT {{ $user->rt?->nomor_rt }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- 2. KARTU STATISTIK UTAMA (KOMPAK & BERISI INFORMASI PADAT) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Rumah -->
        <div class="bg-white dark:bg-[#111b2f] rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-[#1d2c4d] shadow-xs hover:border-slate-300 dark:hover:border-[#273d6b] transition flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Unit Rumah</span>
                    <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-[#172440] text-slate-700 dark:text-slate-300 border border-transparent dark:border-[#273d6b]/50 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mb-2">
                    {{ $totalHouses }} <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">Unit</span>
                </div>
                @php
                    $pctDihuni = $totalHouses > 0 ? round(($totalDihuniPemilik / $totalHouses) * 100) : 0;
                    $pctKontrak = $totalHouses > 0 ? round(($totalKontrak / $totalHouses) * 100) : 0;
                    $pctKosong = $totalHouses > 0 ? round(($totalKosong / $totalHouses) * 100) : 0;
                @endphp
                <div class="w-full h-1.5 rounded-full bg-slate-100 dark:bg-[#0c1427] flex overflow-hidden mb-2.5 border border-transparent dark:border-[#1d2c4d]" title="Dihuni: {{ $pctDihuni }}% | Kontrak: {{ $pctKontrak }}% | Kosong: {{ $pctKosong }}%">
                    <div style="width: {{ $pctDihuni }}%" class="bg-emerald-500 h-full"></div>
                    <div style="width: {{ $pctKontrak }}%" class="bg-sky-500 h-full"></div>
                    <div style="width: {{ $pctKosong }}%" class="bg-slate-300 dark:bg-slate-600 h-full"></div>
                </div>
            </div>
            <div class="pt-2.5 border-t border-slate-100 dark:border-[#1d2c4d] flex items-center justify-between text-xs font-semibold text-slate-600 dark:text-slate-300">
                <span class="text-emerald-700 dark:text-emerald-400">Dihuni: <strong class="text-slate-900 dark:text-white">{{ $totalDihuniPemilik }}</strong></span>
                <span class="text-sky-700 dark:text-sky-400">Kontrak: <strong class="text-slate-900 dark:text-white">{{ $totalKontrak }}</strong></span>
                <span class="text-slate-400 dark:text-slate-500">Kosong: <strong class="text-slate-700 dark:text-slate-300">{{ $totalKosong }}</strong></span>
            </div>
        </div>

        <!-- 2. Warga Terdaftar Aktif -->
        <div class="bg-white dark:bg-[#111b2f] rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-[#1d2c4d] shadow-xs hover:border-slate-300 dark:hover:border-[#273d6b] transition flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Warga Aktif</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-transparent dark:border-emerald-800/40 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 mb-2">
                    {{ $totalResidents }} <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">Jiwa</span>
                </div>
                @php
                    $pctLaki = $totalResidents > 0 ? round(($totalLaki / $totalResidents) * 100) : 0;
                    $pctPerempuan = $totalResidents > 0 ? round(($totalPerempuan / $totalResidents) * 100) : 0;
                @endphp
                <div class="w-full h-1.5 rounded-full bg-slate-100 dark:bg-[#0c1427] flex overflow-hidden mb-2.5 border border-transparent dark:border-[#1d2c4d]" title="Laki-laki: {{ $pctLaki }}% | Perempuan: {{ $pctPerempuan }}%">
                    <div style="width: {{ $pctLaki }}%" class="bg-blue-500 h-full"></div>
                    <div style="width: {{ $pctPerempuan }}%" class="bg-pink-400 h-full"></div>
                </div>
            </div>
            <div class="pt-2.5 border-t border-slate-100 dark:border-[#1d2c4d] flex items-center justify-between text-xs font-semibold text-slate-600 dark:text-slate-300">
                <span>Laki: <strong class="text-slate-900 dark:text-white">{{ $totalLaki }}</strong></span>
                <span class="text-slate-300 dark:text-slate-600">•</span>
                <span>Perempuan: <strong class="text-slate-900 dark:text-white">{{ $totalPerempuan }}</strong></span>
            </div>
        </div>

        <!-- 3. Lansia -->
        <div class="bg-white dark:bg-[#111b2f] rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-[#1d2c4d] shadow-xs hover:border-slate-300 dark:hover:border-[#273d6b] transition flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Lansia (≥ 60 Thn)</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-transparent dark:border-amber-800/40 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 mb-1">
                    {{ $totalLansia }} <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">Jiwa</span>
                </div>
            </div>
            <div class="pt-2.5 border-t border-slate-100 dark:border-[#1d2c4d] text-xs text-slate-500 dark:text-slate-400 font-medium">
                Prioritas program Posyandu & Bantuan
            </div>
        </div>

        <!-- 4. Domisili Luar -->
        <div class="bg-white dark:bg-[#111b2f] rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-[#1d2c4d] shadow-xs hover:border-slate-300 dark:hover:border-[#273d6b] transition flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Domisili Luar</span>
                    <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 border border-transparent dark:border-sky-800/40 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-sky-600 dark:text-sky-400 mb-1">
                    {{ $totalDomisiliLuar }} <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">Jiwa</span>
                </div>
            </div>
            <div class="pt-2.5 border-t border-slate-100 dark:border-[#1d2c4d] text-xs text-slate-500 dark:text-slate-400 font-medium flex items-center justify-between">
                <span>KTP luar wilayah</span>
                <span class="text-slate-400 dark:text-slate-500">Pindah: <strong class="text-slate-700 dark:text-slate-300">{{ $totalPindah }}</strong></span>
            </div>
        </div>
    </div>

    <!-- 4. TABEL DATA OPERASIONAL -->
    @if($user->isAdminRw() && $rtSummaries->isNotEmpty())
    <!-- Khusus Admin RW: Ringkasan Seluruh RT -->
    <div class="bg-white dark:bg-[#111b2f] rounded-2xl border border-slate-200 dark:border-[#1d2c4d] shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-[#1d2c4d] flex items-center justify-between bg-slate-50/50 dark:bg-[#0c1427]/70">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Ringkasan Agregat Seluruh RT</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pantauan data unit rumah dan penghuni per rukun tetangga</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-[#172440] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-[#273d6b]/50">
                Total {{ $rts->count() }} RT Aktif
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/90 dark:bg-[#0c1427] text-slate-600 dark:text-slate-400 text-[11px] font-black uppercase tracking-wider border-b border-slate-200 dark:border-[#1d2c4d]">
                        <th class="py-3.5 px-5">Wilayah RT</th>
                        <th class="py-3.5 px-5">Ketua RT</th>
                        <th class="py-3.5 px-5">Kontak WhatsApp</th>
                        <th class="py-3.5 px-5 text-center">Unit Rumah</th>
                        <th class="py-3.5 px-5 text-center">Warga Aktif</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#1d2c4d]/60 font-medium text-slate-700 dark:text-slate-300">
                    @foreach($rtSummaries as $rtData)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-[#162441]/70 transition-colors">
                        <td class="py-3.5 px-5 font-bold text-slate-900 dark:text-slate-100">
                            <span class="inline-flex items-center space-x-2">
                                <span class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-extrabold text-xs flex items-center justify-center border border-emerald-200 dark:border-emerald-800/60">
                                    {{ $rtData['nomor_rt'] }}
                                </span>
                                <span>Rukun Tetangga {{ $rtData['nomor_rt'] }}</span>
                            </span>
                        </td>
                        <td class="py-3.5 px-5 font-semibold text-slate-800 dark:text-slate-200">
                            {{ $rtData['nama_ketua'] ?? 'Belum ditentukan' }}
                        </td>
                        <td class="py-3.5 px-5">
                            @if($rtData['no_wa_ketua'])
                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $rtData['no_wa_ketua'])) }}" 
                                   target="_blank" 
                                   class="inline-flex items-center space-x-1.5 text-xs font-semibold text-emerald-800 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 border border-emerald-200 dark:border-emerald-800/60 px-2.5 py-1 rounded-lg transition shadow-2xs cursor-pointer"
                                   title="Chat via WhatsApp">
                                    <svg class="w-3.5 h-3.5 fill-[#25D366] shrink-0" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                    <span class="font-mono font-bold">{{ $rtData['formatted_wa_ketua'] ?? $rtData['no_wa_ketua'] }}</span>
                                </a>
                            @else
                                <span class="text-slate-400 dark:text-slate-500 font-bold text-xs select-none">—</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-5 text-center font-bold text-slate-900 dark:text-slate-100">
                            <span class="inline-block px-2.5 py-0.5 rounded-lg bg-slate-100 dark:bg-[#172440] text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-[#273d6b]/50 font-bold text-xs">
                                {{ $rtData['total_rumah'] }} Unit
                            </span>
                        </td>
                        <td class="py-3.5 px-5 text-center font-bold text-emerald-700 dark:text-emerald-300">
                            <span class="inline-block px-2.5 py-0.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 font-bold text-xs">
                                {{ $rtData['total_warga'] }} Jiwa
                            </span>
                        </td>
                        <td class="py-3 px-5 text-right">
                            <a href="{{ route('houses.index', ['rt' => $rtData['id']]) }}" 
                               wire:navigate
                               class="h-8 px-3 rounded-xl bg-white dark:bg-[#172440] hover:bg-slate-100 dark:hover:bg-[#21355a] text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white border border-slate-200 dark:border-[#273d6b]/60 text-xs font-semibold inline-flex items-center space-x-1.5 transition shadow-2xs cursor-pointer">
                                <span>Buka RT</span>
                                <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <!-- Khusus Pengurus RT: Daftar Warga Terbaru di Wilayah RT -->
    <div class="bg-white dark:bg-[#111b2f] rounded-2xl border border-slate-200 dark:border-[#1d2c4d] shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-[#1d2c4d] flex items-center justify-between bg-slate-50/50 dark:bg-[#0c1427]/70">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Pantauan Warga Terkini</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Warga yang baru ditambahkan atau diperbarui di wilayah RT {{ $user->rt?->nomor_rt }}</p>
            </div>
            <a href="{{ route('residents.index') }}" wire:navigate class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-500">
                Lihat Semua Warga →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/90 dark:bg-[#0c1427] text-slate-600 dark:text-slate-400 text-[11px] font-black uppercase tracking-wider border-b border-slate-200 dark:border-[#1d2c4d]">
                        <th class="py-3.5 px-5">Nama Lengkap & NIK</th>
                        <th class="py-3.5 px-4">Kontak WhatsApp</th>
                        <th class="py-3.5 px-4">Unit Rumah</th>
                        <th class="py-3.5 px-4">Peran Keluarga</th>
                        <th class="py-3.5 px-3 text-center">L/P</th>
                        <th class="py-3.5 px-4">Usia</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#1d2c4d]/60 font-medium text-slate-700 dark:text-slate-300">
                    @forelse($recentResidents as $res)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-[#162441]/70 transition-colors">
                        <td class="py-3.5 px-5">
                            <div class="font-bold text-slate-900 dark:text-slate-100 text-sm">{{ $res->nama_lengkap }}</div>
                            <div class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-0.5">NIK: {{ $res->nik }}</div>
                        </td>

                        <td class="py-3.5 px-4 whitespace-nowrap">
                            @if($res->no_wa)
                                <a href="{{ $res->wa_link }}" target="_blank" 
                                   class="inline-flex items-center space-x-1.5 text-xs font-semibold text-emerald-800 dark:text-emerald-300 hover:text-emerald-900 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-emerald-800/60 transition shadow-2xs cursor-pointer"
                                   title="Chat via WhatsApp">
                                    <svg class="w-3.5 h-3.5 fill-[#25D366] shrink-0" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                    <span class="font-mono">{{ $res->formatted_wa }}</span>
                                </a>
                            @else
                                <span class="text-slate-400 dark:text-slate-500 font-bold text-sm select-none">—</span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4">
                            @if($res->house)
                                <a href="{{ route('houses.show', $res->house_id) }}" 
                                   wire:navigate
                                   class="font-bold text-slate-900 dark:text-slate-100 hover:text-emerald-600 dark:hover:text-emerald-400 transition block text-sm">
                                    {{ $res->house->nomor_rumah }}
                                </a>
                            @else
                                <span class="text-xs text-slate-400 dark:text-slate-500 italic">-</span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4 whitespace-nowrap">
                            @if($res->status_keluarga === 'kepala_keluarga')
                                <span class="inline-block px-2.5 py-0.5 bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 font-bold text-xs rounded-md">
                                    Kepala Keluarga
                                </span>
                            @elseif($res->status_keluarga === 'istri')
                                <span class="inline-block px-2.5 py-0.5 bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60 font-semibold text-xs rounded-md">
                                    Istri
                                </span>
                            @elseif($res->status_keluarga === 'anak')
                                <span class="inline-block px-2.5 py-0.5 bg-blue-50 dark:bg-blue-950/40 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60 font-semibold text-xs rounded-md">
                                    Anak
                                </span>
                            @else
                                <span class="inline-block px-2.5 py-0.5 bg-slate-100 dark:bg-[#172440] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-[#273d6b]/50 font-semibold text-xs rounded-md">
                                    Famili Lain
                                </span>
                            @endif
                        </td>

                        <td class="py-3.5 px-3 text-center">
                            <span class="inline-block w-6 h-6 leading-6 rounded-full text-xs font-bold {{ $res->jenis_kelamin === 'L' ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-transparent dark:border-blue-800/40' : 'bg-pink-50 dark:bg-pink-950/40 text-pink-700 dark:text-pink-300 border border-transparent dark:border-pink-800/40' }}">
                                {{ $res->jenis_kelamin }}
                            </span>
                        </td>

                        <td class="py-3.5 px-4 font-semibold text-slate-800 dark:text-slate-200">
                            {{ $res->umur }} Thn
                        </td>

                        <td class="py-3.5 px-4 text-center">
                            @if($res->status_warga === 'aktif')
                                <span class="inline-block px-2.5 py-0.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 font-semibold rounded-full text-xs">
                                    Aktif
                                </span>
                            @elseif($res->status_warga === 'pindah')
                                <span class="inline-block px-2.5 py-0.5 bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 font-semibold rounded-full text-xs">
                                    Pindah
                                </span>
                            @else
                                <span class="inline-block px-2.5 py-0.5 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60 font-semibold rounded-full text-xs">
                                    Meninggal
                                </span>
                            @endif
                        </td>

                        <td class="py-3.5 px-5 text-right whitespace-nowrap">
                            @if($res->house_id)
                                <a href="{{ route('houses.show', $res->house_id) }}" 
                                   wire:navigate
                                   class="w-7 h-7 rounded-lg bg-white dark:bg-[#172440] hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-600 dark:text-slate-300 hover:text-emerald-700 dark:hover:text-emerald-300 border border-slate-200 dark:border-[#273d6b]/60 hover:border-emerald-300 dark:hover:border-emerald-600/50 inline-flex items-center justify-center transition shadow-2xs cursor-pointer"
                                   title="Buka Detail Rumah">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-500 dark:text-slate-400">
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-200">Belum ada warga terdaftar di RT ini</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Data warga yang baru didaftarkan akan muncul di sini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
