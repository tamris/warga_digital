<div class="space-y-5">
    <!-- 1. HERO HEADER KOMPAK (HEMAT TEMPAT & TANPA REDUNDANSI WILAYAH KERJA) -->
    <div class="bg-white rounded-xl p-4 sm:p-5 border border-slate-200 shadow-2xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3.5">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    {{ $user->role_label }}
                </span>
                <span class="text-xs text-slate-300">•</span>
                <span class="text-xs text-slate-500 font-semibold">
                    {{ $user->rt ? 'Lingkungan RT ' . $user->rt->nomor_rt : 'Admin RW (Seluruh Wilayah RT)' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                Selamat Bertugas, {{ $user->nama }}
            </h1>
        </div>

        <div>
            @if($user->isAdminRw())
                <!-- Pemilih Global Tingkat RW Khusus Admin RW -->
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-2 flex items-center space-x-2.5 shrink-0">
                    <div class="w-8 h-8 rounded-md bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    </div>
                    <div>
                        <label for="filter-rt" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            Filter Wilayah RT:
                        </label>
                        <select id="filter-rt" wire:model.live="selectedRtId" 
                                class="bg-transparent text-xs font-bold text-slate-900 focus:outline-none cursor-pointer pr-2">
                            <option value="">Seluruh RT (Semua)</option>
                            @foreach($rts as $rt)
                                <option value="{{ $rt->id }}">RT {{ $rt->nomor_rt }} ({{ $rt->nama_ketua ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @else
                <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-lg bg-emerald-50/70 border border-emerald-200 text-xs font-bold text-emerald-800">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Sistem Aktif • RT {{ $user->rt?->nomor_rt }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- 2. KARTU STATISTIK UTAMA (KOMPAK & PROPOSIONAL) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Rumah -->
        <div class="bg-white rounded-xl p-4 sm:p-5 border border-slate-200 shadow-2xs hover:border-slate-300 transition">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Unit Rumah</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 mb-2.5">
                {{ $totalHouses }} <span class="text-xs font-semibold text-slate-400">Unit</span>
            </div>
            <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-600">
                <span class="text-emerald-700">Dihuni: <strong>{{ $totalDihuniPemilik }}</strong></span>
                <span class="text-sky-700">Kontrak: <strong>{{ $totalKontrak }}</strong></span>
                <span class="text-slate-400">Kosong: <strong>{{ $totalKosong }}</strong></span>
            </div>
        </div>

        <!-- 2. Warga Terdaftar Aktif -->
        <div class="bg-white rounded-xl p-4 sm:p-5 border border-slate-200 shadow-2xs hover:border-slate-300 transition">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Warga Aktif</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-700 mb-2.5">
                {{ $totalResidents }} <span class="text-xs font-semibold text-slate-400">Jiwa</span>
            </div>
            <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-600">
                <span>Laki: <strong class="text-slate-900">{{ $totalLaki }}</strong></span>
                <span class="text-slate-300">•</span>
                <span>Perempuan: <strong class="text-slate-900">{{ $totalPerempuan }}</strong></span>
            </div>
        </div>

        <!-- 3. Lansia -->
        <div class="bg-white rounded-xl p-4 sm:p-5 border border-slate-200 shadow-2xs hover:border-slate-300 transition">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Lansia (≥ 60 Thn)</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-amber-700 mb-2.5">
                {{ $totalLansia }} <span class="text-xs font-semibold text-slate-400">Jiwa</span>
            </div>
            <div class="pt-2.5 border-t border-slate-100 text-xs text-slate-500 font-medium">
                Prioritas program Posyandu & Bantuan
            </div>
        </div>

        <!-- 4. Domisili Luar -->
        <div class="bg-white rounded-xl p-4 sm:p-5 border border-slate-200 shadow-2xs hover:border-slate-300 transition">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Domisili Luar</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-sky-700 mb-2.5">
                {{ $totalDomisiliLuar }} <span class="text-xs font-semibold text-slate-400">Jiwa</span>
            </div>
            <div class="pt-2.5 border-t border-slate-100 text-xs text-slate-500 font-medium">
                Warga KTP luar wilayah RT
            </div>
        </div>
    </div>

    <!-- 3. INSIGHT OPERASIONAL KEPENDUDUKAN & HUNIAN (MENGGANTIKAN MENU CEPAT REDUNDAN) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- 1. Komposisi Hunian Rumah -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Status Hunian Rumah</span>
                    @php
                        $occupancyRate = $totalHouses > 0 ? round((($totalDihuniPemilik + $totalKontrak) / $totalHouses) * 100) : 0;
                    @endphp
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                        {{ $occupancyRate }}% Terisi
                    </span>
                </div>

                @php
                    $pctDihuni = $totalHouses > 0 ? round(($totalDihuniPemilik / $totalHouses) * 100) : 0;
                    $pctKontrak = $totalHouses > 0 ? round(($totalKontrak / $totalHouses) * 100) : 0;
                    $pctKosong = $totalHouses > 0 ? round(($totalKosong / $totalHouses) * 100) : 0;
                @endphp
                <div class="w-full h-3 rounded-full bg-slate-100 flex overflow-hidden mb-3.5">
                    <div style="width: {{ $pctDihuni }}%" class="bg-emerald-500 h-full" title="Dihuni Pemilik"></div>
                    <div style="width: {{ $pctKontrak }}%" class="bg-sky-500 h-full" title="Kontrak"></div>
                    <div style="width: {{ $pctKosong }}%" class="bg-slate-300 h-full" title="Kosong"></div>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center space-x-1.5 text-slate-600 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span>Dihuni Pemilik</span>
                        </span>
                        <strong class="text-slate-900">{{ $totalDihuniPemilik }} Unit ({{ $pctDihuni }}%)</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center space-x-1.5 text-slate-600 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                            <span>Sewa / Kontrak</span>
                        </span>
                        <strong class="text-slate-900">{{ $totalKontrak }} Unit ({{ $pctKontrak }}%)</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center space-x-1.5 text-slate-600 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-300"></span>
                            <span>Unit Kosong</span>
                        </span>
                        <strong class="text-slate-900">{{ $totalKosong }} Unit ({{ $pctKosong }}%)</strong>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-400">Total Basis Hunian</span>
                <span class="font-bold text-slate-900">{{ $totalHouses }} Unit Rumah</span>
            </div>
        </div>

        <!-- 2. Komposisi Gender & Usia -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Demografi Warga Aktif</span>
                    <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">
                        {{ $totalResidents }} Jiwa
                    </span>
                </div>

                @php
                    $pctLaki = $totalResidents > 0 ? round(($totalLaki / $totalResidents) * 100) : 0;
                    $pctPerempuan = $totalResidents > 0 ? round(($totalPerempuan / $totalResidents) * 100) : 0;
                @endphp
                <div class="w-full h-3 rounded-full bg-slate-100 flex overflow-hidden mb-3.5">
                    <div style="width: {{ $pctLaki }}%" class="bg-blue-500 h-full" title="Laki-laki"></div>
                    <div style="width: {{ $pctPerempuan }}%" class="bg-pink-400 h-full" title="Perempuan"></div>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center space-x-1.5 text-slate-600 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <span>Laki-laki</span>
                        </span>
                        <strong class="text-slate-900">{{ $totalLaki }} Jiwa ({{ $pctLaki }}%)</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center space-x-1.5 text-slate-600 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-pink-400"></span>
                            <span>Perempuan</span>
                        </span>
                        <strong class="text-slate-900">{{ $totalPerempuan }} Jiwa ({{ $pctPerempuan }}%)</strong>
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-slate-100">
                        <span class="flex items-center space-x-1.5 text-amber-700 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span>Warga Lansia (≥ 60 Thn)</span>
                        </span>
                        <strong class="text-amber-800">{{ $totalLansia }} Jiwa</strong>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-400">Warga Pindah Keluar</span>
                <span class="font-bold text-slate-700">{{ $totalPindah }} Jiwa Tercatat</span>
            </div>
        </div>

        <!-- 3. Status Hak Akses & Kependudukan -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-2xs flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-3">Informasi Sistem Lingkungan</span>
                <div class="space-y-2.5">
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 flex items-start space-x-2.5">
                        <div class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div class="text-xs">
                            <div class="font-bold text-slate-900">Isolasi Keamanan Data</div>
                            <div class="text-slate-500 mt-0.5">
                                @if($user->isAdminRw())
                                    Sebagai <strong>Admin RW</strong>, Anda dapat memantau dan menyaring seluruh RT di lingkungan RW.
                                @else
                                    Anda bertugas mengelola unit dan warga di wilayah <strong>RT {{ $user->rt?->nomor_rt }}</strong>.
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 flex items-start space-x-2.5">
                        <div class="w-6 h-6 rounded-md bg-sky-100 text-sky-800 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                        </div>
                        <div class="text-xs">
                            <div class="font-bold text-slate-900">Warga Domisili Luar</div>
                            <div class="text-slate-500 mt-0.5">Tercatat <strong>{{ $totalDomisiliLuar }} jiwa</strong> tinggal sebagai penyewa atau pemegang KTP luar.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-right">
                <a href="{{ route('residents.index') }}" wire:navigate class="text-xs font-bold text-emerald-700 hover:text-emerald-800 inline-flex items-center space-x-1">
                    <span>Lihat Master Warga</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- 4. TABEL DATA OPERASIONAL -->
    @if($user->isAdminRw() && $rtSummaries->isNotEmpty())
    <!-- Khusus Admin RW: Ringkasan Seluruh RT -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Ringkasan Agregat Seluruh RT</h2>
                <p class="text-xs text-slate-500 mt-0.5">Pantauan data unit rumah dan penghuni per rukun tetangga</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                Total {{ $rts->count() }} RT Aktif
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-5">Wilayah RT</th>
                        <th class="py-3 px-5">Ketua RT</th>
                        <th class="py-3 px-5">Kontak WhatsApp</th>
                        <th class="py-3 px-5 text-center">Unit Rumah</th>
                        <th class="py-3 px-5 text-center">Warga Aktif</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @foreach($rtSummaries as $rtData)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-5 font-bold text-slate-900">
                            <span class="inline-flex items-center space-x-2">
                                <span class="w-7 h-7 rounded-md bg-emerald-50 text-emerald-700 font-extrabold text-xs flex items-center justify-center border border-emerald-200">
                                    {{ $rtData['nomor_rt'] }}
                                </span>
                                <span>Rukun Tetangga {{ $rtData['nomor_rt'] }}</span>
                            </span>
                        </td>
                        <td class="py-3.5 px-5 font-semibold text-slate-800">
                            {{ $rtData['nama_ketua'] ?? 'Belum ditentukan' }}
                        </td>
                        <td class="py-3.5 px-5">
                            @if($rtData['no_wa_ketua'])
                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $rtData['no_wa_ketua'])) }}" 
                                   target="_blank" 
                                   class="inline-flex items-center space-x-1.5 text-xs font-semibold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded-lg transition shadow-2xs"
                                   title="Chat via WhatsApp">
                                    <svg class="w-3.5 h-3.5 fill-[#25D366] shrink-0" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                    <span class="font-mono font-bold">{{ $rtData['formatted_wa_ketua'] ?? $rtData['no_wa_ketua'] }}</span>
                                </a>
                            @else
                                <span class="text-slate-400 font-bold text-xs select-none">—</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-5 text-center font-bold text-slate-900">
                            <span class="inline-block px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-800 font-bold text-xs">
                                {{ $rtData['total_rumah'] }} Unit
                            </span>
                        </td>
                        <td class="py-3.5 px-5 text-center font-bold text-emerald-700">
                            <span class="inline-block px-2.5 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-bold text-xs">
                                {{ $rtData['total_warga'] }} Jiwa
                            </span>
                        </td>
                        <td class="py-3 px-5 text-right">
                            <a href="{{ route('houses.index', ['rt' => $rtData['id']]) }}" 
                               wire:navigate
                               class="h-8 px-3 rounded-lg bg-white hover:bg-slate-100 text-slate-700 hover:text-slate-900 border border-slate-200 text-xs font-semibold inline-flex items-center space-x-1.5 transition shadow-2xs">
                                <span>Buka RT</span>
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
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
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Pantauan Warga Terkini</h2>
                <p class="text-xs text-slate-500 mt-0.5">Warga yang baru ditambahkan atau diperbarui di wilayah RT {{ $user->rt?->nomor_rt }}</p>
            </div>
            <a href="{{ route('residents.index') }}" wire:navigate class="text-xs font-bold text-emerald-700 hover:text-emerald-800">
                Lihat Semua Warga →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-5">Nama Lengkap & NIK</th>
                        <th class="py-3 px-4">Kontak WhatsApp</th>
                        <th class="py-3 px-4">Unit Rumah</th>
                        <th class="py-3 px-4">Peran Keluarga</th>
                        <th class="py-3 px-3 text-center">L/P</th>
                        <th class="py-3 px-4">Usia</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentResidents as $res)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-5">
                            <div class="font-bold text-slate-900 text-sm">{{ $res->nama_lengkap }}</div>
                            <div class="text-xs font-mono text-slate-400 mt-0.5">NIK: {{ $res->nik }}</div>
                        </td>

                        <td class="py-3.5 px-4 whitespace-nowrap">
                            @if($res->no_wa)
                                <a href="{{ $res->wa_link }}" target="_blank" 
                                   class="inline-flex items-center space-x-1.5 text-xs font-semibold text-emerald-800 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100/80 px-2.5 py-1 rounded-lg border border-emerald-200 transition shadow-2xs"
                                   title="Chat via WhatsApp">
                                    <svg class="w-3.5 h-3.5 fill-[#25D366] shrink-0" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                    <span class="font-mono">{{ $res->formatted_wa }}</span>
                                </a>
                            @else
                                <span class="text-slate-400 font-bold text-sm select-none">—</span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4">
                            @if($res->house)
                                <a href="{{ route('houses.show', $res->house_id) }}" 
                                   wire:navigate
                                   class="font-bold text-slate-900 hover:text-emerald-700 transition block text-sm">
                                    {{ $res->house->nomor_rumah }}
                                </a>
                            @else
                                <span class="text-xs text-slate-400 italic">-</span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4 whitespace-nowrap">
                            @if($res->status_keluarga === 'kepala_keluarga')
                                <span class="inline-block px-2.5 py-0.5 bg-slate-900 text-white font-bold text-xs rounded-md">
                                    Kepala Keluarga
                                </span>
                            @elseif($res->status_keluarga === 'istri')
                                <span class="inline-block px-2.5 py-0.5 bg-rose-50 text-rose-800 border border-rose-200 font-semibold text-xs rounded-md">
                                    Istri
                                </span>
                            @elseif($res->status_keluarga === 'anak')
                                <span class="inline-block px-2.5 py-0.5 bg-blue-50 text-blue-800 border border-blue-200 font-semibold text-xs rounded-md">
                                    Anak
                                </span>
                            @else
                                <span class="inline-block px-2.5 py-0.5 bg-slate-100 text-slate-700 font-semibold text-xs rounded-md">
                                    Famili Lain
                                </span>
                            @endif
                        </td>

                        <td class="py-3.5 px-3 text-center">
                            <span class="inline-block w-6 h-6 leading-6 rounded-full text-xs font-bold {{ $res->jenis_kelamin === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                                {{ $res->jenis_kelamin }}
                            </span>
                        </td>

                        <td class="py-3.5 px-4 font-semibold text-slate-800">
                            {{ $res->umur }} Thn
                        </td>

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

                        <td class="py-3.5 px-5 text-right whitespace-nowrap">
                            @if($res->house_id)
                                <a href="{{ route('houses.show', $res->house_id) }}" 
                                   wire:navigate
                                   class="w-7 h-7 rounded-lg bg-white hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 border border-slate-200 hover:border-emerald-300 inline-flex items-center justify-center transition shadow-2xs"
                                   title="Buka Detail Rumah">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-500">
                            <p class="text-sm font-bold text-slate-800">Belum ada warga terdaftar di RT ini</p>
                            <p class="text-xs text-slate-400 mt-1">Data warga yang baru didaftarkan akan muncul di sini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
