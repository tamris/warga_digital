<div class="space-y-6">
    <!-- Header Halaman & Tombol Ekspor DTKS -->
    <div class="bg-white dark:bg-[#111b2f] rounded-2xl p-5 sm:p-6 border border-slate-200 dark:border-[#1d2c4d] shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 px-2.5 py-1 rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 text-xs font-bold border border-emerald-200 dark:border-emerald-800/60 mb-2">
                <span>Kesejahteraan & DTKS</span>
                <span>•</span>
                <span>{{ $user->isRwLevel() ? 'Tingkat RW 01' : 'Wilayah RT ' . str_pad((string)((int)$user->rt?->nomor_rt), 2, '0', STR_PAD_LEFT) }}</span>
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                Pemetaan Kesejahteraan & DTKS Warga
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1 font-medium">
                Pencatatan profil sosial-ekonomi rumah tangga, kalkulasi skor prioritas bantuan, dan inventarisasi warga belum terdaftar DTKS
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ $exportUrl }}" 
               class="h-11 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs inline-flex items-center space-x-2 transition shadow-xs cursor-pointer">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Unduh Usulan DTKS (.xlsx)</span>
            </a>
        </div>
    </div>


    <!-- 4 SUMMARY CARDS ATAS (SESUAI SPESIFIKASI BAB 5.1) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total KK Rentan & Miskin -->
        <div class="bg-white dark:bg-[#111b2f] p-5 rounded-2xl border border-slate-200 dark:border-[#1d2c4d] shadow-xs">
            <div class="flex items-center justify-between text-rose-600 dark:text-rose-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">KK Rentan & Miskin</span>
                <div class="w-9 h-9 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-transparent dark:border-rose-800/40 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="flex items-baseline space-x-2">
                <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $totalRentanMiskin }}</div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">KK</span>
            </div>
            <div class="mt-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                <span>{{ $totalRumah > 0 ? round(($totalRentanMiskin / $totalRumah) * 100) : 0 }}% dari {{ $totalRumah }} total rumah</span>
            </div>
        </div>

        <!-- 2. Sudah Terdaftar DTKS -->
        <div class="bg-white dark:bg-[#111b2f] p-5 rounded-2xl border border-slate-200 dark:border-[#1d2c4d] shadow-xs">
            <div class="flex items-center justify-between text-emerald-600 dark:text-emerald-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tercover DTKS Pusat</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-transparent dark:border-emerald-800/40 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="flex items-baseline space-x-2">
                <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $totalTerdaftarDtks }}</div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">KK</span>
            </div>
            <div class="mt-2 text-xs font-medium text-emerald-700 dark:text-emerald-400 font-semibold">
                <span>Penerima PKH, BPNT, atau PBI</span>
            </div>
        </div>

        <!-- 3. Belum Masuk DTKS (Wajib Diusulkan) -->
        <div class="bg-white dark:bg-[#111b2f] p-5 rounded-2xl border border-slate-200 dark:border-[#1d2c4d] shadow-xs">
            <div class="flex items-center justify-between text-amber-600 dark:text-amber-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Wajib Diusulkan (Non-DTKS)</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-transparent dark:border-amber-800/40 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="flex items-baseline space-x-2">
                <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $totalBelumDtks }}</div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">KK Miskin</span>
            </div>
            <div class="mt-2 text-xs font-semibold text-amber-600 dark:text-amber-400">
                <span>Belum terdaftar bantuan pemerintah</span>
            </div>
        </div>

        <!-- 4. KK Lansia / Disabilitas -->
        <div class="bg-white dark:bg-[#111b2f] p-5 rounded-2xl border border-slate-200 dark:border-[#1d2c4d] shadow-xs">
            <div class="flex items-center justify-between text-indigo-600 dark:text-indigo-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">KK Lansia & Disabilitas</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 border border-transparent dark:border-indigo-800/40 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
            <div class="flex items-baseline space-x-2">
                <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $totalLansiaDisabilitas }}</div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">KK Rentan</span>
            </div>
            <div class="mt-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                <span>Kelompok rentan prioritas sosial</span>
            </div>
        </div>
    </div>

    <!-- FILTER BAR & TABEL DATA KESEJAHTERAAN -->
    <div class="bg-white dark:bg-[#111b2f] rounded-2xl border border-slate-200 dark:border-[#1d2c4d] shadow-xs overflow-hidden">
        <!-- Toolbar Filter -->
        <div class="p-4 border-b border-slate-200 dark:border-[#1d2c4d] bg-slate-50/50 dark:bg-[#0c1427]/70 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
            <div class="flex-1 relative">
                <input type="text" 
                       wire:model.live.debounce.250ms="search"
                       placeholder="Cari nomor rumah, nama kepala keluarga, atau NIK..."
                       class="w-full pl-9 pr-3.5 py-2 text-xs bg-white dark:bg-[#111b2f] border border-slate-200 dark:border-[#1d2c4d] rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 shadow-2xs transition">
                <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if($user->isRwLevel())
                <select wire:model.live="selectedRtId" 
                        class="text-xs bg-white dark:bg-[#111b2f] border border-slate-200 dark:border-[#1d2c4d] rounded-xl px-3 py-2 font-semibold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer shadow-2xs transition">
                    <option value="">Semua Wilayah RT</option>
                    @foreach($rts as $rt)
                        <option value="{{ $rt->id }}">RT {{ str_pad((string)((int)$rt->nomor_rt), 2, '0', STR_PAD_LEFT) }}</option>
                    @endforeach
                </select>
                @endif

                <select wire:model.live="filterType" 
                        class="text-xs bg-white dark:bg-[#111b2f] border border-slate-200 dark:border-[#1d2c4d] rounded-xl px-3 py-2 font-semibold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer shadow-2xs transition">
                    <option value="semua">Semua Status Ekonomi</option>
                    <option value="miskin_rentan">Khusus Miskin & Rentan</option>
                    <option value="belum_dtks">Wajib Diusulkan (Non-DTKS)</option>
                    <option value="terdaftar_dtks">Sudah Terdaftar DTKS</option>
                </select>
            </div>
        </div>

        <!-- Tabel Profil Rumah & Kesejahteraan -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/90 dark:bg-[#0c1427] text-slate-600 dark:text-slate-400 text-[11px] font-black uppercase tracking-wider border-b border-slate-200 dark:border-[#1d2c4d]">
                        <th class="py-3.5 px-5">Rumah & RT</th>
                        <th class="py-3.5 px-4">Kepala Keluarga</th>
                        <th class="py-3.5 px-4 text-center">Status Ekonomi</th>
                        <th class="py-3.5 px-4 text-center">Status DTKS</th>
                        <th class="py-3.5 px-4">Indikator Kerentanan</th>
                        <th class="py-3.5 px-4 text-center">Skor Prioritas</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#1d2c4d]/60 font-medium text-slate-700 dark:text-slate-300">
                    @forelse($houses as $house)
                        @php
                            $rowProfile = $house->socialProfile ?? $house->getOrCreateSocialProfile();
                            $rowHead = $house->headOfFamily ?? $house->residents->first();
                            $rowHasLansia = $house->residents->contains(fn($r) => $r->status_warga === 'aktif' && $r->tanggal_lahir && $r->tanggal_lahir->age >= 60);
                        @endphp
                        <tr wire:key="house-row-{{ $house->id }}" class="hover:bg-slate-50/80 dark:hover:bg-[#162441]/70 transition-colors {{ $rowProfile->kategori_ekonomi === 'miskin_ekstrem' ? 'bg-rose-50/20 dark:bg-rose-950/20' : ($rowProfile->kategori_ekonomi === 'rentan_miskin' ? 'bg-amber-50/15 dark:bg-amber-950/15' : '') }}">
                            <!-- 1. Rumah & RT -->
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <a href="{{ route('houses.show', ['id' => $house->id, 'from' => 'welfare']) }}" wire:navigate class="font-bold text-slate-900 dark:text-slate-100 hover:text-emerald-600 dark:hover:text-emerald-400 transition text-sm">
                                    {{ $house->nomor_rumah }}
                                </a>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-[#172440] text-slate-700 dark:text-slate-300 border border-transparent dark:border-[#273d6b]/50">
                                        RT {{ str_pad((string)((int)$house->rt?->nomor_rt), 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span class="ml-1.5 text-slate-400 dark:text-slate-400 font-mono text-[11px]">{{ $house->residents->where('status_warga', 'aktif')->count() }} Jiwa</span>
                                </div>
                            </td>

                            <!-- 2. Kepala Keluarga -->
                            <td class="py-3.5 px-4">
                                @if($rowHead)
                                    <div class="font-bold text-slate-900 dark:text-slate-100 text-xs">{{ $rowHead->nama_lengkap }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">NIK: {{ $rowHead->nik }}</div>
                                    <div class="text-[11px] text-slate-400 dark:text-slate-400 mt-0.5">{{ $rowHead->pekerjaan ?: 'Belum bekerja' }}</div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum ada KK terdata</span>
                                @endif
                            </td>

                            <!-- 3. Kategori Ekonomi -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($rowProfile->kategori_ekonomi === 'miskin_ekstrem')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/60">
                                        Miskin Ekstrem
                                    </span>
                                @elseif($rowProfile->kategori_ekonomi === 'rentan_miskin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60">
                                        Rentan Miskin
                                    </span>
                                @elseif($rowProfile->kategori_ekonomi === 'menengah')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-200 dark:bg-sky-950/40 dark:text-sky-300 dark:border-sky-800/60">
                                        Menengah
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60">
                                        Mampu
                                    </span>
                                @endif
                                <div class="text-[10px] text-slate-400 dark:text-slate-400 mt-1 font-medium font-mono">{{ $rowProfile->rentang_penghasilan_label }}</div>
                            </td>

                            <!-- 4. Status DTKS -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($rowProfile->status_dtks === 'terdaftar')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60">
                                        ✓ DTKS Terdaftar
                                    </span>
                                    @if(!empty($rowProfile->bantuan_rutin_pemerintah))
                                        <div class="flex flex-wrap justify-center gap-1 mt-1">
                                            @foreach($rowProfile->bantuan_rutin_pemerintah as $bansosPusat)
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-slate-100 dark:bg-[#172440] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-[#273d6b]/50">
                                                    {{ $bansosPusat }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                @elseif($rowProfile->status_dtks === 'proses_usulan')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200 dark:bg-sky-950/40 dark:text-sky-300 dark:border-sky-800/60">
                                        Proses Usulan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-[#172440]/70 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-[#273d6b]/50">
                                        Belum DTKS
                                    </span>
                                @endif
                            </td>

                            <!-- 5. Indikator Kerentanan -->
                            <td class="py-3.5 px-4 text-xs">
                                <div class="flex flex-wrap gap-1">
                                    @if($rowHasLansia)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800/60" title="Terdapat lansia ≥ 60 tahun">
                                            Lansia
                                        </span>
                                    @endif
                                    @if($rowProfile->is_kepala_keluarga_janda_duda)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-pink-50 text-pink-700 border border-pink-200 dark:bg-pink-950/40 dark:text-pink-300 dark:border-pink-800/60" title="Kepala keluarga Janda/Duda">
                                            Janda/Duda
                                        </span>
                                    @endif
                                    @if($rowProfile->ada_anggota_disabilitas_sakit_kronis)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/60" title="Anggota disabilitas / sakit kronis">
                                            Disabilitas
                                        </span>
                                    @endif
                                    @if($rowProfile->kondisi_rumah_tidak_layak)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60" title="Rumah tidak layak huni">
                                            RTLH
                                        </span>
                                    @endif
                                    @if(!$rowHasLansia && !$rowProfile->is_kepala_keluarga_janda_duda && !$rowProfile->ada_anggota_disabilitas_sakit_kronis && !$rowProfile->kondisi_rumah_tidak_layak)
                                        <span class="text-slate-400 dark:text-slate-500 text-[11px]">-</span>
                                    @endif
                                </div>
                            </td>

                            <!-- 6. Skor Prioritas (0 - 100) -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center space-x-1.5">
                                    <span class="text-base font-black {{ $rowProfile->skor_prioritas >= 80 ? 'text-rose-600 dark:text-rose-400' : ($rowProfile->skor_prioritas >= 55 ? 'text-amber-600 dark:text-amber-400' : ($rowProfile->skor_prioritas >= 30 ? 'text-sky-600 dark:text-sky-400' : 'text-slate-600 dark:text-slate-400')) }}">
                                        {{ $rowProfile->skor_prioritas }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold">/100</span>
                                </div>
                                <div class="w-16 bg-slate-100 dark:bg-[#0c1427] rounded-full h-1.5 mx-auto mt-1 overflow-hidden border border-transparent dark:border-[#1d2c4d]">
                                    <div class="h-1.5 rounded-full {{ $rowProfile->skor_prioritas >= 80 ? 'bg-rose-500' : ($rowProfile->skor_prioritas >= 55 ? 'bg-amber-500' : ($rowProfile->skor_prioritas >= 30 ? 'bg-sky-500' : 'bg-slate-300 dark:bg-slate-600')) }}" 
                                         style="width: {{ $rowProfile->skor_prioritas }}%"></div>
                                </div>
                            </td>

                            <!-- 7. Aksi -->
                            <td class="py-3 px-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center space-x-1.5">
                                    <a href="{{ route('houses.show', ['id' => $house->id, 'from' => 'welfare']) }}" 
                                       wire:navigate
                                       class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-[#172440] hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-600 dark:text-slate-300 hover:text-emerald-700 dark:hover:text-emerald-300 border border-slate-200/80 dark:border-[#273d6b]/60 hover:border-emerald-300 dark:hover:border-emerald-600/50 inline-flex items-center justify-center transition shadow-2xs group cursor-pointer"
                                       title="Lihat Detail Rumah & Penghuni">
                                        <svg class="w-4 h-4 text-slate-500 dark:text-slate-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if($user->isRwLevel() || $house->rt_id === $user->rt_id)
                                    <button type="button" 
                                            wire:click="openEditModal({{ $house->id }})" 
                                            class="w-8 h-8 rounded-lg bg-white dark:bg-[#172440] hover:bg-slate-100 dark:hover:bg-[#21355a] text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white border border-slate-200 dark:border-[#273d6b]/60 hover:border-slate-300 dark:hover:border-[#3b558f] inline-flex items-center justify-center transition shadow-2xs group cursor-pointer"
                                            title="Edit Profil Sosial-Ekonomi & Bansos">
                                        <svg class="w-4 h-4 text-slate-500 dark:text-slate-400 group-hover:text-slate-800 dark:group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500 dark:text-slate-400">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-[#0c1427] text-slate-400 dark:text-slate-500 border border-transparent dark:border-[#1d2c4d] flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">Tidak ada data profil rumah yang cocok</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Sesuaikan kata kunci pencarian atau ubah opsi filter Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($houses->total() > 0)
            <div class="p-4 bg-slate-50/50 dark:bg-[#0c1427]/80 border-t border-slate-100 dark:border-[#1d2c4d]">
                {{ $houses->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL EDIT PROFIL SOSIAL EKONOMI RUMAH -->
    @if($showEditModal)
    <div wire:key="modal-edit-profile-{{ $editingHouseId }}" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#111b2f] rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 dark:border-[#1d2c4d] animate-in fade-in zoom-in-95 duration-150">
            <!-- Modal Header -->
            <div class="flex items-start justify-between pb-3 border-b border-slate-100 dark:border-[#1d2c4d] mb-3.5">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                        Profil Sosial-Ekonomi & Kelayakan Bansos
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $editingHouseNumber }} • Kepala Keluarga: <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $editingHeadName }}</span>
                    </p>
                </div>

                <button type="button" wire:click="$set('showEditModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#162441] rounded-lg transition p-1 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Form Body -->
            <form wire:submit="saveSocialProfile" class="space-y-3.5 text-xs">
                @if ($errors->any())
                    <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-rose-800 dark:text-rose-200 text-xs">
                        <div class="font-bold mb-1">Terdapat kesalahan pengisian:</div>
                        <ul class="list-disc pl-4 space-y-0.5 text-[11px]">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Banner Live Status & Skor Kelayakan (Ringkas & Proporsional) -->
                <div class="p-2.5 px-3.5 rounded-xl border flex items-center justify-between gap-3 transition-all {{ $previewScore >= 80 ? 'bg-rose-50/70 dark:bg-rose-950/30 border-rose-200 dark:border-rose-900/50' : ($previewScore >= 55 ? 'bg-amber-50/70 dark:bg-amber-950/30 border-amber-200 dark:border-amber-900/50' : ($previewScore >= 30 ? 'bg-sky-50/70 dark:bg-sky-950/30 border-sky-200 dark:border-sky-900/50' : 'bg-slate-50 dark:bg-[#0c1427] border-slate-200 dark:border-[#1d2c4d]')) }}">
                    <div class="flex items-center space-x-2">
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status:</span>
                        @if($form_kategori_ekonomi === 'miskin_ekstrem')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-black bg-rose-600 text-white shadow-2xs">
                                <span>Miskin Ekstrem</span>
                                <span class="text-[10px] font-normal opacity-90">(Prioritas 1)</span>
                            </span>
                        @elseif($form_kategori_ekonomi === 'rentan_miskin')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-black bg-amber-500 text-white shadow-2xs">
                                <span>Rentan Miskin</span>
                                <span class="text-[10px] font-normal opacity-90">(Prioritas 2)</span>
                            </span>
                        @elseif($form_kategori_ekonomi === 'menengah')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-black bg-sky-600 text-white shadow-2xs">
                                <span>Menengah</span>
                                <span class="text-[10px] font-normal opacity-90">(Bansos Darurat)</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-black bg-slate-200 dark:bg-[#172440] text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-[#273d6b]/50 shadow-2xs">
                                <span>Mampu / Sejahtera</span>
                                <span class="text-[10px] font-normal opacity-80">(Non-Prioritas)</span>
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center space-x-1.5 shrink-0">
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Total Skor:</span>
                        <span class="px-2 py-0.5 rounded-md text-xs font-bold {{ $previewScore >= 80 ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300' : ($previewScore >= 55 ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300' : ($previewScore >= 30 ? 'bg-sky-100 dark:bg-sky-950/60 text-sky-800 dark:text-sky-300' : 'bg-slate-200 dark:bg-[#172440] text-slate-800 dark:text-slate-300')) }}">
                            {{ $previewScore }} / 100 Poin
                        </span>
                    </div>
                </div>

                <!-- 1. Rentang Penghasilan Bulanan -->
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1.5">1. Rata-rata Penghasilan Total Kepala Keluarga / Bulan (Bobot Max 40 Poin)</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <!-- < 1.5 Jt -->
                        <label class="p-2.5 px-3 rounded-xl border cursor-pointer transition select-none flex items-center justify-between {{ $form_rentang_penghasilan === 'di_bawah_1_5_jt' ? 'border-emerald-600 dark:border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/40 text-emerald-950 dark:text-emerald-200 font-bold' : 'border-slate-200 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] hover:bg-slate-50 dark:hover:bg-[#162441] text-slate-700 dark:text-slate-300' }}">
                            <input type="radio" wire:model.live="form_rentang_penghasilan" value="di_bawah_1_5_jt" class="sr-only">
                            <span class="text-xs">&lt; Rp 1.500.000</span>
                            <span class="text-[11px] font-semibold {{ $form_rentang_penghasilan === 'di_bawah_1_5_jt' ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">+40 Poin</span>
                        </label>

                        <!-- 1.5 - 3 Jt -->
                        <label class="p-2.5 px-3 rounded-xl border cursor-pointer transition select-none flex items-center justify-between {{ $form_rentang_penghasilan === '1_5_sampai_3_jt' ? 'border-emerald-600 dark:border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/40 text-emerald-950 dark:text-emerald-200 font-bold' : 'border-slate-200 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] hover:bg-slate-50 dark:hover:bg-[#162441] text-slate-700 dark:text-slate-300' }}">
                            <input type="radio" wire:model.live="form_rentang_penghasilan" value="1_5_sampai_3_jt" class="sr-only">
                            <span class="text-xs">Rp 1,5 Jt - Rp 3 Jt</span>
                            <span class="text-[11px] font-semibold {{ $form_rentang_penghasilan === '1_5_sampai_3_jt' ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">+20 Poin</span>
                        </label>

                        <!-- > 3 Jt -->
                        <label class="p-2.5 px-3 rounded-xl border cursor-pointer transition select-none flex items-center justify-between {{ $form_rentang_penghasilan === 'di_atas_3_jt' ? 'border-emerald-600 dark:border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/40 text-emerald-950 dark:text-emerald-200 font-bold' : 'border-slate-200 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] hover:bg-slate-50 dark:hover:bg-[#162441] text-slate-700 dark:text-slate-300' }}">
                            <input type="radio" wire:model.live="form_rentang_penghasilan" value="di_atas_3_jt" class="sr-only">
                            <span class="text-xs">&gt; Rp 3.000.000</span>
                            <span class="text-[11px] font-semibold {{ $form_rentang_penghasilan === 'di_atas_3_jt' ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">0 Poin</span>
                        </label>
                    </div>
                </div>

                <!-- 2. Kondisi Fisik Rumah & Tempat Tinggal -->
                <div class="pt-3 border-t border-slate-100 dark:border-[#1d2c4d]">
                    <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1.5">2. Kondisi Fisik Rumah & Tempat Tinggal (Bobot Max 30 Poin)</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <!-- Rumah Tidak Layak Huni -->
                        <label class="p-2.5 px-3 rounded-xl border cursor-pointer select-none transition flex items-center justify-between {{ $form_rumah_tidak_layak ? 'border-emerald-600 dark:border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/40 text-emerald-950 dark:text-emerald-200 font-bold' : 'border-slate-200 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] hover:bg-slate-50 dark:hover:bg-[#162441] text-slate-700 dark:text-slate-300' }}">
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" wire:model.live="form_rumah_tidak_layak" class="rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-[#273d6b] dark:bg-[#172440]">
                                <div>
                                    <div class="text-xs font-semibold">Rumah Tidak Layak Huni (RTLH)</div>
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400">Lantai tanah, bilik lapuk, atap bocor parah</div>
                                </div>
                            </div>
                            <span class="text-[11px] font-semibold {{ $form_rumah_tidak_layak ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">+20 Poin</span>
                        </label>

                        <!-- Status Kepemilikan (Sistem) -->
                        <div class="p-2.5 px-3 rounded-xl border {{ $houseStatusHuni === 'kontrak' ? 'border-amber-300 dark:border-amber-800/60 bg-amber-50/40 dark:bg-amber-950/30 text-amber-950 dark:text-amber-200' : 'border-slate-200 dark:border-[#1d2c4d] bg-slate-50/60 dark:bg-[#0c1427]/60 text-slate-700 dark:text-slate-300' }} flex items-center justify-between">
                            <div>
                                <div class="text-xs font-semibold">Status Huni Tempat Tinggal</div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                    {{ $houseStatusHuni === 'kontrak' ? 'Kontrak / Sewa / Menumpang' : 'Milik Sendiri' }}
                                </div>
                            </div>
                            <span class="text-[11px] font-semibold {{ $houseStatusHuni === 'kontrak' ? 'text-amber-700 dark:text-amber-400' : 'text-slate-400 dark:text-slate-500' }}">
                                {{ $houseStatusHuni === 'kontrak' ? '+10 Poin' : '0 Poin' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 3. Indikator Kerentanan Sosial & Beban Keluarga -->
                <div class="pt-3 border-t border-slate-100 dark:border-[#1d2c4d]">
                    <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1.5">3. Indikator Kerentanan Sosial & Beban Keluarga (Bobot Max 40 Poin)</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <!-- Janda / Duda (Otomatis dari Data KK) -->
                        <div class="p-2.5 px-3 rounded-xl border {{ $form_is_janda_duda ? 'border-emerald-300 dark:border-emerald-800/60 bg-emerald-50/40 dark:bg-emerald-950/30 text-emerald-950 dark:text-emerald-200' : 'border-slate-200 dark:border-[#1d2c4d] bg-slate-50/60 dark:bg-[#0c1427]/60 text-slate-600 dark:text-slate-400' }} flex items-center justify-between">
                            <div>
                                <div class="text-xs font-semibold">
                                    {{ $form_is_janda_duda ? ($headGender === 'P' ? 'Kepala Keluarga Janda' : 'Kepala Keluarga Duda') : 'Kepala Keluarga Berpasangan' }}
                                </div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                    {{ $form_is_janda_duda ? ($headGender === 'P' ? 'Terdeteksi otomatis: KK perempuan tanpa suami' : 'Terdeteksi otomatis: KK tidak memiliki istri di KK') : 'Terdeteksi lengkap bersama pasangan / istri di KK' }}
                                </div>
                            </div>
                            <span class="text-[11px] font-semibold {{ $form_is_janda_duda ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' }}">
                                {{ $form_is_janda_duda ? '+10 Poin' : '0 Poin' }}
                            </span>
                        </div>

                        <!-- Disabilitas / Sakit Kronis -->
                        <label class="p-2.5 px-3 rounded-xl border cursor-pointer select-none transition flex items-center justify-between {{ $form_ada_disabilitas_kronis ? 'border-emerald-600 dark:border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/40 text-emerald-950 dark:text-emerald-200 font-bold' : 'border-slate-200 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] hover:bg-slate-50 dark:hover:bg-[#162441] text-slate-700 dark:text-slate-300' }}">
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" wire:model.live="form_ada_disabilitas_kronis" class="rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-[#273d6b] dark:bg-[#172440]">
                                <span class="text-xs">Anggota Disabilitas / Sakit Kronis</span>
                            </div>
                            <span class="text-[11px] font-semibold {{ $form_ada_disabilitas_kronis ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">+10 Poin</span>
                        </label>

                        <!-- Info Lansia (Sistem) -->
                        <div class="p-2.5 px-3 rounded-xl border {{ $hasLansia ? 'border-emerald-300 dark:border-emerald-800/60 bg-emerald-50/40 dark:bg-emerald-950/30 text-emerald-950 dark:text-emerald-200' : 'border-slate-200 dark:border-[#1d2c4d] bg-slate-50/60 dark:bg-[#0c1427]/60 text-slate-600 dark:text-slate-400' }} flex items-center justify-between">
                            <div>
                                <div class="text-xs font-semibold">Terdapat Anggota Lansia (&ge; 60 Tahun)</div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400">Terdeteksi otomatis dari tanggal lahir warga di KK</div>
                            </div>
                            <span class="text-[11px] font-semibold {{ $hasLansia ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' }}">
                                {{ $hasLansia ? '+10 Poin' : '0 Poin' }}
                            </span>
                        </div>

                        <!-- Info Tanggungan Anak (Sistem) -->
                        <div class="p-2.5 px-3 rounded-xl border {{ $residentChildCount >= 3 ? 'border-emerald-300 dark:border-emerald-800/60 bg-emerald-50/40 dark:bg-emerald-950/30 text-emerald-950 dark:text-emerald-200' : 'border-slate-200 dark:border-[#1d2c4d] bg-slate-50/60 dark:bg-[#0c1427]/60 text-slate-600 dark:text-slate-400' }} flex items-center justify-between">
                            <div>
                                <div class="text-xs font-semibold">Tanggungan Anak / Pelajar &ge; 3 Orang</div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400">Terdeteksi {{ $residentChildCount }} anak dari data keluarga KK</div>
                            </div>
                            <span class="text-[11px] font-semibold {{ $residentChildCount >= 3 ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' }}">
                                {{ $residentChildCount >= 3 ? '+10 Poin' : '0 Poin' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 4. Status di DTKS Kelurahan / Kemensos -->
                <div class="pt-3 border-t border-slate-100 dark:border-[#1d2c4d]">
                    <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1.5">4. Status di DTKS Kelurahan / Kemensos</label>
                    <select wire:model.live="form_status_dtks" class="w-full h-9 px-3 rounded-xl border border-slate-300 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] text-xs text-slate-800 dark:text-slate-100 font-medium focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 cursor-pointer">
                        <option value="belum_terdaftar">Belum Terdaftar (Perlu Diusulkan)</option>
                        <option value="terdaftar">Sudah Terdaftar Resmi di DTKS</option>
                        <option value="proses_usulan">Dalam Proses Usulan ke Kelurahan</option>
                    </select>
                </div>

                <!-- 5. Bantuan Rutin yang Sedang Diterima -->
                <div class="pt-3 border-t border-slate-100 dark:border-[#1d2c4d]">
                    <label class="block font-bold text-slate-700 dark:text-slate-200 mb-1.5">5. Bantuan Rutin yang Sedang Diterima</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach(['PKH', 'BPNT', 'KIP', 'PBI_JK'] as $b)
                            @php $isChecked = in_array($b, (array)$form_bantuan_rutin); @endphp
                            <label class="p-2.5 px-3 rounded-xl border cursor-pointer select-none transition flex items-center space-x-2.5 {{ $isChecked ? 'border-emerald-600 dark:border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/40 text-emerald-950 dark:text-emerald-200 font-bold' : 'border-slate-200 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] hover:bg-slate-50 dark:hover:bg-[#162441] text-slate-700 dark:text-slate-300' }}">
                                <input type="checkbox" wire:model.live="form_bantuan_rutin" value="{{ $b }}" class="rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-[#273d6b] dark:bg-[#172440]">
                                <span class="text-xs font-semibold">{{ $b === 'PBI_JK' ? 'PBI-JK' : $b }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="pt-4 border-t border-slate-100 dark:border-[#1d2c4d] flex items-center justify-end">
                    <div class="flex items-center space-x-2">
                        <button type="button" 
                                wire:click="$set('showEditModal', false)" 
                                class="h-9 px-4 rounded-xl border border-slate-300 dark:border-[#1d2c4d] bg-white dark:bg-[#111b2f] text-slate-700 dark:text-slate-200 font-semibold text-xs hover:bg-slate-50 dark:hover:bg-[#162441] transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="h-9 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white font-bold text-xs transition shadow-xs inline-flex items-center space-x-1.5 cursor-pointer">
                            <span wire:loading.remove wire:target="saveSocialProfile">Simpan Profil Ekonomi</span>
                            <span wire:loading wire:target="saveSocialProfile">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
