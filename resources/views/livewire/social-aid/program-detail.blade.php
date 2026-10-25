<div class="space-y-6">
    @php
        $bantuanText = $program->detail_bantuan;
        if (!$bantuanText || in_array(strtoupper(trim((string)$bantuanText)), ['RP', 'RP.', 'IDR'])) {
            $bantuanText = $program->jenis_bantuan === 'uang_tunai' ? 'Bantuan Tunai' : 'Bantuan Sembako/Barang';
        }
    @endphp

    <!-- Breadcrumb & Header Program -->
    <div class="bg-white rounded-xl p-5 sm:p-6 border border-slate-200 shadow-2xs space-y-5">
        <!-- Baris 1: Judul, Status & Aksi Utama -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <!-- Breadcrumb -->
                <nav class="flex items-center space-x-1.5 text-xs text-slate-500 mb-1.5 font-medium">
                    <a href="{{ route('social-aid.programs.index') }}" wire:navigate class="hover:text-emerald-600 transition">Bantuan Sosial</a>
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="text-slate-800 font-semibold">Detail & Penyaluran</span>
                </nav>

                <!-- Title & Status Badge -->
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                        {{ $program->nama_program }}
                    </h1>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold
                        {{ $program->status_program === 'pendataan_rt' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                        {{ $program->status_program === 'verifikasi_rw' && ! $program->isReadyForDistribution() ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                        {{ $program->status_program === 'verifikasi_rw' && $program->isReadyForDistribution() ? 'bg-sky-50 text-sky-800 border border-sky-200' : '' }}
                        {{ $program->status_program === 'penyaluran' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                        {{ $program->status_program === 'selesai' ? 'bg-slate-100 text-slate-700 border border-slate-200' : '' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $program->status_program === 'pendataan_rt' ? 'bg-amber-500' : ($program->status_program === 'verifikasi_rw' ? ($program->isReadyForDistribution() ? 'bg-sky-500' : 'bg-blue-500') : ($program->status_program === 'penyaluran' ? 'bg-emerald-500' : 'bg-slate-400')) }}"></span>
                        <span>{{ $program->status_label }}</span>
                    </span>
                </div>
            </div>

            <!-- Kontrol Cetak Dokumen & Pengubahan Status -->
            <div class="flex items-center gap-2.5 sm:shrink-0">
                <div class="inline-flex rounded-lg shadow-2xs border border-slate-300 bg-white p-0.5">
                    @if($program->status_program === 'pendataan_rt')
                        <span class="h-8 px-3 rounded-md text-slate-400 font-semibold text-xs inline-flex items-center space-x-1.5 cursor-not-allowed opacity-60" title="Dokumen daftar hadir aktif setelah tahap verifikasi usulan oleh RW">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Daftar Hadir</span>
                        </span>
                    @else
                        <a href="{{ route('social-aid.programs.attendance-print', ['program' => $program->id, 'rt_id' => $targetRtId]) }}" target="_blank" 
                           class="h-8 px-3 rounded-md hover:bg-slate-100 text-slate-700 font-semibold text-xs inline-flex items-center space-x-1.5 transition" title="Preview & Cetak Formulir Daftar Hadir Penyaluran">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Daftar Hadir</span>
                        </a>
                    @endif
                    <div class="w-px bg-slate-200 my-1"></div>
                    <a href="{{ route('social-aid.programs.replacement-print', ['program' => $program->id, 'rt_id' => $targetRtId]) }}" target="_blank" 
                       class="h-8 px-3 rounded-md hover:bg-slate-100 text-slate-700 font-semibold text-xs inline-flex items-center space-x-1.5 transition" title="Preview & Cetak Berita Acara Pergantian Penerima">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Berita Acara</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Baris 2: Kartu Informasi Program (4 Kolom Lega & Rapi) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 pt-1">
            <!-- 1. Sumber Dana -->
            <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-3.5 flex flex-col justify-between">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Sumber Dana</span>
                <div class="mt-2 flex items-center space-x-2 text-slate-800 font-bold text-xs sm:text-sm">
                    <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>{{ $program->sumber_dana_label }}</span>
                </div>
            </div>

            <!-- 2. Bentuk Bantuan -->
            <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-3.5 flex flex-col justify-between">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Bentuk Bantuan</span>
                <div class="mt-2 flex items-center space-x-2 text-emerald-800 font-bold text-xs sm:text-sm">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span>{{ $bantuanText }}</span>
                </div>
            </div>

            <!-- 3. Jadwal Penyaluran -->
            <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-3.5 flex flex-col justify-between">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Jadwal Penyaluran</span>
                <div class="mt-2 flex items-center space-x-2 text-slate-800 font-bold text-xs sm:text-sm">
                    <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>{{ $program->tanggal_penyaluran->locale('id')->translatedFormat('d F Y') }}</span>
                </div>
            </div>

            <!-- 4. Lokasi Penyaluran -->
            <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-3.5 flex flex-col justify-between">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Lokasi Penyaluran</span>
                <div class="mt-2 flex items-center space-x-2 text-slate-800 font-bold text-xs sm:text-sm">
                    <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="truncate" title="{{ $program->lokasi_pengambilan }}">{{ $program->lokasi_pengambilan }}</span>
                </div>
            </div>
        </div>

        <!-- Filter Wilayah RT (Khusus Pengurus RW) -->
        @if($user->isRwLevel())
        <div class="mt-5 pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2">
            <span class="text-xs font-semibold text-slate-500 mr-1.5">Wilayah RT:</span>
            
            <button type="button" 
                    wire:click="$set('selectedRtId', null)" 
                    class="h-8 px-3 rounded-lg text-xs font-semibold transition inline-flex items-center gap-1.5 {{ is_null($selectedRtId) ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900' }}">
                <span>Semua RT</span>
                <span class="text-[11px] font-mono px-1.5 py-0.2 rounded {{ is_null($selectedRtId) ? 'bg-emerald-700/60 text-white' : 'bg-slate-200 text-slate-600' }}">
                    {{ $program->recipients()->where('status_usulan', '!=', 'diganti')->count() }}/{{ $program->total_kuota }}
                </span>
            </button>

            @foreach($rts as $rt)
                @php
                    $isSel = ($selectedRtId == $rt->id);
                    $rtQ = $program->getQuotaForRt($rt->id);
                    $rtF = $program->recipients()->where('rt_id', $rt->id)->where('status_usulan', '!=', 'diganti')->count();
                @endphp
                <button type="button" 
                        wire:click="$set('selectedRtId', {{ $rt->id }})" 
                        class="h-8 px-3 rounded-lg text-xs font-semibold transition inline-flex items-center gap-1.5 {{ $isSel ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900' }}">
                    <span>RT {{ str_pad((string)((int)$rt->nomor_rt), 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="text-[11px] font-mono px-1.5 py-0.2 rounded {{ $isSel ? 'bg-emerald-700/60 text-white' : 'bg-slate-200 text-slate-600' }}">
                        {{ $rtF }}/{{ $rtQ }}
                    </span>
                </button>
            @endforeach
        </div>
        @endif
    </div>



    <!-- Kartu Statistik Kuota Wilayah Aktif -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- 1. Jatah Kuota -->
        <div class="bg-white p-4.5 rounded-xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    {{ is_null($targetRtId) ? 'Total Kuota RW' : 'Jatah Kuota RT' }}
                </p>
                <div class="mt-1 flex items-baseline space-x-1.5">
                    <span class="text-2xl font-black text-slate-900">{{ $currentRtQuota }}</span>
                    <span class="text-xs font-semibold text-slate-500">KK</span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>

        <!-- 2. Terisi / Diusulkan -->
        <div class="bg-white p-4.5 rounded-xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Usulan Penerima</p>
                <div class="mt-1 flex items-baseline space-x-1.5">
                    <span class="text-2xl font-black {{ $currentRtFilled >= $currentRtQuota ? 'text-emerald-700' : 'text-amber-600' }}">{{ $currentRtFilled }}</span>
                    <span class="text-xs font-semibold text-slate-500">/ {{ $currentRtQuota }} KK</span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl {{ $currentRtFilled >= $currentRtQuota ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
        </div>

        <!-- 3. Tersalurkan Hari-H (Tahap Penyaluran/Selesai) ATAU Verifikasi RW (Tahap Pendataan/Verifikasi) -->
        @if(in_array($program->status_program, ['penyaluran', 'selesai']))
        <div class="bg-white p-4.5 rounded-xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Tersalurkan</p>
                <div class="mt-1 flex items-baseline space-x-1.5">
                    <span class="text-2xl font-black text-emerald-700">{{ $currentRtDelivered }}</span>
                    <span class="text-xs font-semibold text-slate-500">/ {{ $currentRtFilled }} Paket</span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
        </div>
        @else
        <div class="bg-white p-4.5 rounded-xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Verifikasi RW</p>
                <div class="mt-1 flex items-baseline space-x-1.5">
                    <span class="text-2xl font-black {{ $currentRtApproved >= $currentRtQuota && $currentRtQuota > 0 ? 'text-blue-700' : 'text-slate-900' }}">{{ $currentRtApproved }}</span>
                    <span class="text-xs font-semibold text-slate-500">/ {{ $currentRtQuota }} Disetujui</span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl {{ $currentRtApproved >= $currentRtQuota && $currentRtQuota > 0 ? 'bg-blue-50 text-blue-600' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
        </div>
        @endif
    </div>

    <!-- Banner Siap Disalurkan (Menunggu Jadwal Penyaluran) -->
    @if($program->status_program === 'verifikasi_rw' && $program->isReadyForDistribution())
    <div class="p-4 rounded-xl bg-sky-50 border border-sky-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sky-900 shadow-2xs">
        <div class="flex items-start sm:items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <h4 class="text-xs font-bold text-sky-950 flex items-center gap-2">
                    <span>Seluruh Usulan Telah Disahkan RW</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-200/70 text-sky-900">Menunggu Jadwal</span>
                </h4>
                <p class="text-xs text-sky-800 mt-0.5 leading-relaxed">
                    Tahap penyerahan bantuan kepada warga otomatis aktif pada <strong>{{ $program->tanggal_penyaluran->locale('id')->translatedFormat('l, d F Y') }}</strong>. Dokumen Daftar Hadir & Kupon sudah dapat dicetak untuk persiapan.
                </p>
            </div>
        </div>
        @if($user->isRwLevel())
        <button type="button" 
                wire:click="advanceToPenyaluran" 
                class="shrink-0 h-9 px-4 rounded-xl bg-sky-700 hover:bg-sky-800 text-white text-xs font-bold transition shadow-xs inline-flex items-center gap-1.5 cursor-pointer">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span>Buka Penyaluran Sekarang</span>
        </button>
        @endif
    </div>
    @endif

    <!-- Tab Navigasi Utama -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="border-b border-slate-200 bg-slate-50/70 px-4 flex flex-wrap gap-1 pt-2">
            <!-- Tab Usulan -->
            <button type="button" 
                    wire:click="setTab('usulan')" 
                    class="pb-3 px-3.5 text-xs font-bold border-b-2 transition flex items-center space-x-2 {{ $activeTab === 'usulan' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                <span>Usulan Penerima</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $activeTab === 'usulan' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                    {{ $activeRecipients->count() }}
                </span>
            </button>

            <!-- Tab Sanggahan -->
            <button type="button" 
                    wire:click="setTab('sanggahan')" 
                    class="pb-3 px-3.5 text-xs font-bold border-b-2 transition flex items-center space-x-2 {{ $activeTab === 'sanggahan' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                <span>Sanggahan & Penggantian</span>
                @if($replacedRecipients->count() > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-rose-100 text-rose-800 font-bold">
                    {{ $replacedRecipients->count() }}
                </span>
                @endif
            </button>

            {{-- Tab Penyaluran (Hanya tampil saat tahap penyaluran & selesai) --}}
            @if(in_array($program->status_program, ['penyaluran', 'selesai']))
            <button type="button" 
                    wire:click="setTab('penyaluran')" 
                    class="pb-3 px-3.5 text-xs font-bold border-b-2 transition flex items-center space-x-2 {{ $activeTab === 'penyaluran' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                <span>Penyaluran Hari-H</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $activeTab === 'penyaluran' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                    {{ $currentRtDelivered }}/{{ $activeRecipients->count() }}
                </span>
            </button>
            @endif
        </div>

        <!-- ========================================== -->
        <!-- TAB CONTENT: USULAN PENERIMA -->
        <!-- ========================================== -->
        @if($activeTab === 'usulan')
        <div class="p-5 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Daftar Usulan Penerima</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar warga penerima bantuan sesuai alokasi kuota</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    {{-- Tombol Pengesahan Massal untuk Pengurus RW --}}
                    @if($user->isRwLevel() && $activeRecipients->where('status_usulan', 'diusulkan_rt')->count() > 0)
                    <button type="button" 
                            wire:click="confirmApproveAll" 
                            class="h-9 px-3.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs inline-flex items-center space-x-1.5 transition shadow-2xs">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Sahkan Usulan ({{ $activeRecipients->where('status_usulan', 'diusulkan_rt')->count() }})</span>
                    </button>
                    @endif

                    {{-- Tombol Pilih Warga (Hanya Tahap Perencanaan: Pendataan RT & Verifikasi RW) --}}
                    @if(in_array($program->status_program, ['pendataan_rt', 'verifikasi_rw']))
                    <button type="button" 
                            wire:click="openProposeModal" 
                            class="h-9 px-3.5 rounded-lg {{ $user->isRwLevel() && $activeRecipients->where('status_usulan', 'diusulkan_rt')->count() > 0 ? 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-300' : 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-2xs' }} font-semibold text-xs inline-flex items-center space-x-1.5 transition">
                        <svg class="w-4 h-4 {{ $user->isRwLevel() && $activeRecipients->where('status_usulan', 'diusulkan_rt')->count() > 0 ? 'text-slate-500' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Pilih Warga Penerima</span>
                    </button>
                    @endif
                </div>
            </div>

            <!-- Tabel Penerima Aktif -->
            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">No. Kupon</th>
                            <th class="py-3 px-4">Kepala Keluarga</th>
                            <th class="py-3 px-4">Rumah & RT</th>
                            <th class="py-3 px-4 text-center">Status Ekonomi</th>
                            <th class="py-3 px-4 text-center">Skor Prioritas</th>
                            <th class="py-3 px-4 text-center">Status Usulan</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($activeRecipients as $rec)
                            @php
                                $profile = $rec->house?->socialProfile;
                            @endphp
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                                    {{ $rec->nomor_urut_kupon ?: '-' }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900 text-xs">{{ $rec->resident?->nama_lengkap }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono mt-0.5">NIK: {{ $rec->resident?->nik }}</div>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-800">{{ $rec->house?->nomor_rumah }}</div>
                                    <div class="text-[11px] text-slate-400">RT {{ str_pad((string)((int)$rec->rt?->nomor_rt), 2, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $profile?->kategori_ekonomi === 'miskin_ekstrem' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        {{ $profile?->kategori_ekonomi_label ?? 'Rentan' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <span class="text-sm font-black text-rose-600">{{ $profile?->skor_prioritas ?? 0 }}</span>
                                    <span class="text-[10px] text-slate-400">Poin</span>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @if($rec->status_usulan === 'disetujui_rw')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Disetujui
                                        </span>
                                    @elseif($rec->status_usulan === 'ditolak_rw')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            Menunggu Verifikasi
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1.5 justify-end">
                                        @if(in_array($program->status_program, ['penyaluran', 'selesai']))
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200 shadow-2xs" title="Daftar penerima telah disahkan & terkunci pada tahap penyaluran">
                                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                <!-- <span>Terkunci</span> -->
                                            </span>
                                        @else
                                        {{-- Aksi RW --}}
                                        @if($user->isRwLevel() && $program->status_program !== 'selesai')
                                            @if($rec->status_usulan === 'diusulkan_rt')
                                                <!-- Tombol Setujui (Icon) -->
                                                <button type="button" 
                                                        wire:click="approveRecipient({{ $rec->id }})" 
                                                        class="w-7 h-7 rounded-lg bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 transition inline-flex items-center justify-center shadow-2xs"
                                                        title="Setujui Usulan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                </button>

                                                <!-- Tombol Tolak (Icon) -->
                                                <button type="button" 
                                                        wire:click="rejectRecipient({{ $rec->id }})" 
                                                        class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 transition inline-flex items-center justify-center shadow-2xs"
                                                        title="Tolak Usulan (Minta Revisi RT)">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            @else
                                                <!-- Tombol Batal/Kembalikan ke Usulan (Icon) -->
                                                <button type="button" 
                                                        wire:click="unapproveRecipient({{ $rec->id }})" 
                                                        class="w-7 h-7 rounded-lg bg-amber-50 hover:bg-amber-600 text-amber-700 hover:text-white border border-amber-200 transition inline-flex items-center justify-center shadow-2xs"
                                                        title="{{ $rec->status_usulan === 'disetujui_rw' ? 'Batalkan Persetujuan (Kembalikan ke Usulan RT)' : 'Batalkan Penolakan (Kembalikan ke Usulan RT)' }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        @endif

                                        {{-- Tombol Hapus (Icon) --}}
                                        @if($program->status_program !== 'selesai')
                                            @if($user->isRwLevel() || $rec->status_usulan !== 'disetujui_rw')
                                                <button type="button" 
                                                        wire:click="confirmRemoveRecipient({{ $rec->id }})" 
                                                        class="w-7 h-7 rounded-lg bg-slate-50 hover:bg-rose-50 text-slate-400 hover:text-rose-600 border border-slate-200 hover:border-rose-200 transition inline-flex items-center justify-center shadow-2xs" 
                                                        title="Hapus Usulan">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            @endif
                                        @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500">
                                    <p class="font-bold text-slate-700 text-sm">Belum ada usulan penerima untuk wilayah ini</p>
                                    <p class="text-xs text-slate-400 mt-1">Gunakan tombol <strong>Pilih Warga Penerima</strong> di kanan atas untuk menambahkan warga.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- ========================================== -->
        <!-- TAB B CONTENT: SANGGAHAN & PENGGANTIAN -->
        <!-- ========================================== -->
        @if($activeTab === 'sanggahan')
        <div class="p-5 space-y-4">
            <div class="p-4 rounded-xl bg-amber-50/60 border border-amber-200 text-xs text-amber-900 leading-relaxed">
                <div class="font-bold flex items-center space-x-1.5 text-amber-950 mb-1">
                    <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Mekanisme Sanggahan / Penggantian Penerima Bantuan</span>
                </div>
                <p>
                    Gunakan fitur ini apabila daftar penerima resmi dari Kelurahan/Pusat ternyata berhalangan menerima (contoh: <strong>Warga sudah meninggal dunia, pindah domisili, atau sudah mandiri/mampu</strong>). Setiap pergantian akan otomatis dicatat untuk dicetak menjadi <strong>Berita Acara Penggantian Resmi</strong>.
                </p>
            </div>

            <!-- Tabel Penggantian yang Sudah Dilakukan -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600">Riwayat Penggantian Penerima di RT Ini</h4>
                <div class="overflow-x-auto border border-slate-200 rounded-xl">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                            <tr>
                                <th class="py-2.5 px-4">Penerima Semula (Dibatalkan)</th>
                                <th class="py-2.5 px-4">Alasan Penggantian</th>
                                <th class="py-2.5 px-4">Digantikan Oleh</th>
                                <th class="py-2.5 px-4">Catatan Sanggahan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @forelse($replacedRecipients as $rep)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-900 line-through">{{ $rep->resident?->nama_lengkap }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">NIK: {{ $rep->resident?->nik }}</div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                            {{ $rep->alasan_penggantian_label }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-emerald-800">{{ $rep->replacementResident?->nama_lengkap ?? '-' }}</div>
                                        <div class="text-[11px] text-slate-500 font-mono">NIK: {{ $rep->replacementResident?->nik }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 text-[11px]">
                                        {{ $rep->catatan_penggantian ?: '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-slate-400">
                                        Belum ada data sanggahan atau penggantian penerima di RT ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Aksi Penggantian dari Penerima Aktif -->
            <div class="pt-4 border-t border-slate-100">
                <div class="mb-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Daftar Penerima Terdaftar:</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Penggantian penerima hanya dapat diajukan sebelum usulan disetujui RW dan sebelum bantuan diterima</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @forelse($activeRecipients as $rec)
                        @php
                            $isApproved = ($rec->status_usulan === 'disetujui_rw');
                            $isDelivered = ($rec->status_penyerahan !== 'belum_diambil');
                            $isProgramFinished = ($program->status_program === 'selesai');
                        @endphp
                        <div class="p-3.5 rounded-xl border border-slate-200 bg-white flex items-center justify-between">
                            <div>
                                <div class="font-bold text-slate-900 text-xs">{{ $rec->resident?->nama_lengkap }}</div>
                                <div class="text-[11px] text-slate-500">{{ $rec->house?->nomor_rumah }} • Kupon: {{ $rec->nomor_urut_kupon }}</div>
                            </div>

                            @if($isDelivered)
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center space-x-1" title="Bantuan sudah diserahkan / diterima">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Sudah Diterima</span>
                                </span>
                            @elseif($isProgramFinished)
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200 inline-flex items-center space-x-1" title="Program bantuan telah selesai & diarsipkan">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <span>Arsip Terkunci</span>
                                </span>
                            @elseif($isApproved)
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200 inline-flex items-center space-x-1" title="{{ in_array($program->status_program, ['penyaluran', 'selesai']) ? 'Daftar penerima telah disahkan dan terkunci pada tahap penyaluran.' : 'Usulan telah disahkan oleh RW. Batalkan persetujuan (↺) di Tab Usulan terlebih dahulu jika ingin mengganti.' }}">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <!-- <span>Terkunci (ACC RW)</span> -->
                                </span>
                            @else
                                <button type="button" 
                                        wire:click="openReplaceModal({{ $rec->id }})" 
                                        class="h-8 px-3 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-xs border border-amber-200 transition shadow-2xs">
                                    <span>Ajukan Pengganti</span>
                                </button>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-2 py-4 text-center text-slate-400 text-xs">
                            Belum ada penerima terdaftar di wilayah ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        {{-- ========================================== --}}
        {{-- TAB C CONTENT: PENYALURAN HARI-H --}}
        {{-- ========================================== --}}
        @if($activeTab === 'penyaluran' && in_array($program->status_program, ['penyaluran', 'selesai']))
        <div class="p-5 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Operasional Penyaluran Bantuan & Bukti Terima</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Kirim kupon undangan via WhatsApp dan tandai status pengambilan paket bantuan</p>
                </div>

                <div class="text-xs font-bold text-slate-700">
                    Progres: <span class="text-emerald-700 font-black text-sm">{{ $currentRtDelivered }}</span> dari {{ $activeRecipients->count() }} Paket
                </div>
            </div>

            @php
                $isAllDelivered = $activeRecipients->count() > 0 && $currentRtDelivered === $activeRecipients->count();
            @endphp

            @if($isAllDelivered && $program->status_program === 'selesai')
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h4 class="text-xs font-bold text-slate-900">Penyaluran Selesai & Telah Diarsipkan</h4>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">100% Tersalurkan</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">Seluruh paket bantuan telah diserahkan kepada warga yang berhak. Data program tersimpan rapi dalam arsip.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('social-aid.programs.attendance-print', ['program' => $program->id]) }}" target="_blank" class="h-8 px-3 rounded-lg bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-xs inline-flex items-center space-x-1.5 shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak & Unduh Laporan (PDF)</span>
                        </a>
                    </div>
                </div>
            @elseif($isAllDelivered && $program->status_program === 'penyaluran')
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 animate-in fade-in">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-emerald-950">Seluruh Bantuan Telah Tersalurkan (100%)</h4>
                            <p class="text-[11px] text-emerald-700 mt-0.5">Semua warga penerima telah mengambil paket bantuan. Klik tombol di samping untuk meresmikan penyelesaian & arsip program.</p>
                        </div>
                    </div>
                    @if($user->isRwLevel())
                        <button type="button" 
                                wire:click="advanceToSelesai" 
                                class="h-9 px-4 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-xs inline-flex items-center justify-center space-x-1.5 shrink-0 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Selesaikan & Arsipkan Program</span>
                        </button>
                    @endif
                </div>
            @endif

            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">No. Kupon</th>
                            <th class="py-3 px-4">Penerima & Rumah</th>
                            <th class="py-3 px-4 text-center">Kupon WhatsApp</th>
                            <th class="py-3 px-4 text-center">Status Terima</th>
                            <th class="py-3 px-4">Waktu & Penerima Aktual</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($activeRecipients as $rec)
                            @php
                                $phone = $rec->resident?->no_wa;
                                $cleanPhone = $phone ? preg_replace('/[^0-9]/', '', $phone) : null;
                                if ($cleanPhone && str_starts_with($cleanPhone, '0')) {
                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                }
                                $pesanWa = "Yth. Bapak/Ibu " . ($rec->resident?->nama_lengkap ?? '') . " (" . ($rec->house?->nomor_rumah ?? '') . "), Anda terdaftar sebagai penerima bantuan " . $program->nama_program . " (" . $program->detail_bantuan . "). Silakan mengambil paket pada " . $program->tanggal_penyaluran->locale('id')->translatedFormat('d F Y') . " di " . $program->lokasi_pengambilan . " dengan menunjukkan kupon nomor: " . ($rec->nomor_urut_kupon ?? '-') . ". Terima kasih.";
                                $waUrl = $cleanPhone ? "https://wa.me/{$cleanPhone}?text=" . rawurlencode($pesanWa) : null;
                            @endphp
                            <tr class="hover:bg-slate-50/50 {{ $rec->status_penyerahan !== 'belum_diambil' ? 'bg-emerald-50/20' : '' }}">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                                    {{ $rec->nomor_urut_kupon ?: '-' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 text-xs">{{ $rec->resident?->nama_lengkap }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $rec->house?->nomor_rumah }} • RT {{ str_pad((string)((int)$rec->rt?->nomor_rt), 2, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                <!-- Kirim Kupon WA -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($waUrl)
                                        <a href="{{ $waUrl }}" target="_blank" 
                                           class="w-7 h-7 rounded-lg bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 inline-flex items-center justify-center transition shadow-2xs"
                                           title="Kirim Kupon via WhatsApp ({{ $cleanPhone }})">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.203c.043.072.043.419-.101.824z"/></svg>
                                        </a>
                                    @else
                                        <span class="text-slate-300 text-xs select-none" title="Nomor WhatsApp tidak tersedia">—</span>
                                    @endif
                                </td>

                                <!-- Status Penyerahan -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($rec->status_penyerahan === 'sudah_diterima')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            <span>Sudah Diterima</span>
                                        </span>
                                    @elseif($rec->status_penyerahan === 'diwakilkan')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-800 border border-sky-200">
                                            Diwakilkan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            Belum Diambil
                                        </span>
                                    @endif
                                </td>

                                <!-- Waktu & Penerima Aktual -->
                                <td class="py-3.5 px-4 text-xs">
                                    @if($rec->waktu_diterima)
                                        <div class="font-bold text-slate-800">{{ $rec->penerima_aktual }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $rec->waktu_diterima->locale('id')->translatedFormat('d M, H:i') }} WIB</div>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">-</span>
                                    @endif
                                </td>

                                <!-- Aksi Penyerahan -->
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1.5 justify-end">
                                        @if($rec->status_penyerahan === 'belum_diambil')
                                            <!-- Tombol Tandai Diterima (Icon) -->
                                            <button type="button" 
                                                    wire:click="openDeliveryModal({{ $rec->id }})" 
                                                    class="w-7 h-7 rounded-lg bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 transition inline-flex items-center justify-center shadow-2xs"
                                                    title="Tandai Sudah Diterima">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        @else
                                            <!-- Tombol Edit Data Terima (Icon) -->
                                            <button type="button" 
                                                    wire:click="openDeliveryModal({{ $rec->id }})" 
                                                    class="w-7 h-7 rounded-lg bg-slate-50 hover:bg-slate-200 text-slate-600 hover:text-slate-900 border border-slate-200 transition inline-flex items-center justify-center shadow-2xs"
                                                    title="Ubah Data Penyerahan">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </button>

                                            <!-- Tombol Batal/Kembalikan ke Belum Diambil (Icon Back / Undo) -->
                                            <button type="button" 
                                                    wire:click="resetDelivery({{ $rec->id }})" 
                                                    class="w-7 h-7 rounded-lg bg-amber-50 hover:bg-amber-600 text-amber-700 hover:text-white border border-amber-200 transition inline-flex items-center justify-center shadow-2xs"
                                                    title="Batalkan Tanda Terima (Kembalikan ke Belum Diambil)">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Belum ada data usulan warga di RT ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- MODAL 1: USULKAN WARGA DARI REKOMENDASI (TAB A) -->
    <!-- ========================================== -->
    @if($showProposeModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150 my-6">
            {{-- Header Modal --}}
            <div class="flex items-start justify-between pb-4 border-b border-slate-100 mb-4 gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">
                        Rekomendasi Calon Penerima Bantuan
                    </h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Daftar warga otomatis diurutkan dari prioritas tertinggi dengan mempertimbangkan riwayat penerimaan sebelumnya. Kuota RT: <strong class="text-slate-800">{{ $currentRtQuota }} KK</strong>.
                    </p>
                </div>
                <div class="flex items-center space-x-2 shrink-0">
                    <span class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ count(array_filter($selectedHouseIds)) > $currentRtQuota ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                        Dipilih: {{ count(array_filter($selectedHouseIds)) }} / {{ $currentRtQuota }} KK
                    </span>
                    <button type="button" wire:click="$set('showProposeModal', false)" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            @error('selectedHouseIds')
                <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center space-x-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ $message }}</span>
                </div>
            @enderror

            {{-- Toolbar: Filter & Tombol Aksi --}}
            <div class="mb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="inline-flex items-center p-1 rounded-xl bg-slate-100 text-xs font-medium overflow-x-auto">
                    <button type="button" 
                            wire:click="$set('candidateFilter', 'all')" 
                            class="px-3 py-1.5 rounded-lg font-semibold transition cursor-pointer {{ $candidateFilter === 'all' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Semua Warga ({{ $rotationCounts['all'] ?? 0 }})
                    </button>
                    <button type="button" 
                            wire:click="$set('candidateFilter', 'never')" 
                            class="px-3 py-1.5 rounded-lg font-semibold transition cursor-pointer {{ $candidateFilter === 'never' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Belum Pernah ({{ $rotationCounts['never'] ?? 0 }})
                    </button>
                    <button type="button" 
                            wire:click="$set('candidateFilter', 'ready')" 
                            class="px-3 py-1.5 rounded-lg font-semibold transition cursor-pointer {{ $candidateFilter === 'ready' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Siap Giliran ({{ $rotationCounts['ready'] ?? 0 }})
                    </button>
                    <button type="button" 
                            wire:click="$set('candidateFilter', 'cooldown')" 
                            class="px-3 py-1.5 rounded-lg font-semibold transition cursor-pointer {{ $candidateFilter === 'cooldown' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Masa Jeda ({{ $rotationCounts['cooldown'] ?? 0 }})
                    </button>
                </div>

                <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                    @if(count(array_filter($selectedHouseIds)) > 0)
                        <button type="button" 
                                wire:click="clearCandidateSelection" 
                                class="h-8 px-3 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 text-xs font-medium transition cursor-pointer">
                            Reset Pilihan
                        </button>
                    @endif
                    <button type="button" 
                            wire:click="autoSelectByFairness" 
                            class="h-8 px-3.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-2xs inline-flex items-center space-x-1.5 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Pilih Otomatis (Top {{ $currentRtQuota }})</span>
                    </button>
                </div>
            </div>

            {{-- Tabel Rekomendasi Warga --}}
            <div class="max-h-96 overflow-y-auto border border-slate-200 rounded-xl">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 sticky top-0 z-10">
                        <tr>
                            <th class="py-2.5 px-3 text-center w-10">Pilih</th>
                            <th class="py-2.5 px-3">Kepala Keluarga & Rumah</th>
                            <th class="py-2.5 px-3 text-center">Status Ekonomi</th>
                            <th class="py-2.5 px-3">Riwayat Bansos Terakhir</th>
                            <th class="py-2.5 px-3 text-center">Skor Prioritas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($candidateHouses as $cand)
                            @php
                                $cProfile = $cand->socialProfile;
                                $cHead = $cand->headOfFamily ?? $cand->residents->first();
                                $hasLansia = $cand->residents->contains(fn($r) => $r->status_warga === 'aktif' && $r->tanggal_lahir && $r->tanggal_lahir->diffInYears(now()) >= 60);
                                $isChecked = !empty($selectedHouseIds[$cand->id]);
                                $metrics = $cand->rotation_metrics ?? $cand->getAidRotationMetrics($program->id);
                                $fairness = $cand->fairness_score ?? $metrics['fairness_score'];
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors {{ $isChecked ? 'bg-emerald-50/40' : '' }}">
                                <td class="py-2.5 px-3 text-center">
                                    <input type="checkbox" 
                                           wire:model.live="selectedHouseIds.{{ $cand->id }}" 
                                           class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer">
                                </td>
                                <td class="py-2.5 px-3">
                                    <div class="font-bold text-slate-900 flex items-center space-x-1.5">
                                        <span>{{ $cHead?->nama_lengkap ?? 'Tanpa Nama' }}</span>
                                        @if($hasLansia)
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200">Lansia</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-500">{{ $cand->nomor_rumah }} • NIK: {{ $cHead?->nik ?? '-' }}</div>
                                </td>
                                <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $cProfile?->kategori_ekonomi === 'miskin_ekstrem' ? 'bg-rose-50 text-rose-700 border border-rose-200' : ($cProfile?->kategori_ekonomi === 'rentan_miskin' ? 'bg-amber-50 text-amber-700 border border-amber-200' : ($cProfile?->kategori_ekonomi === 'mampu' ? 'bg-slate-50 text-slate-500 border border-slate-200' : 'bg-slate-100 text-slate-700 border border-slate-200')) }}">
                                        {{ $cProfile?->kategori_ekonomi_label ?? 'Menengah' }}
                                    </span>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        {{ $cProfile?->status_dtks === 'terdaftar' ? 'DTKS Pusat' : 'Non-DTKS' }}
                                    </div>
                                </td>
                                <td class="py-2.5 px-3">
                                    @if($metrics['status'] === 'never')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Belum pernah terima
                                        </span>
                                    @elseif($metrics['is_pending'] ?? false)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                            Terdaftar di Program Lain
                                        </span>
                                        <div class="text-[10px] text-slate-500 mt-0.5 truncate max-w-[220px]" title="{{ $metrics['last_program_name'] }}">
                                            {{ $metrics['last_program_name'] }} • {{ $metrics['time_label'] }}
                                        </div>
                                    @elseif($metrics['status'] === 'cooldown')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                            Masa jeda ({{ $metrics['time_label'] }})
                                        </span>
                                        <div class="text-[10px] text-slate-400 truncate max-w-[220px] mt-0.5" title="{{ $metrics['last_program_name'] }}">
                                            {{ $metrics['last_program_name'] }}
                                        </div>
                                    @elseif($metrics['status'] === 'recent')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                            Jeda ringan ({{ $metrics['time_label'] }})
                                        </span>
                                        <div class="text-[10px] text-slate-400 truncate max-w-[220px] mt-0.5" title="{{ $metrics['last_program_name'] }}">
                                            {{ $metrics['last_program_name'] }}
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-sky-50 text-sky-700 border border-sky-200">
                                            Siap giliran ({{ $metrics['time_label'] }})
                                        </span>
                                        <div class="text-[10px] text-slate-400 truncate max-w-[220px] mt-0.5" title="{{ $metrics['last_program_name'] }}">
                                            {{ $metrics['last_program_name'] }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                    <div class="text-sm font-bold text-slate-800">
                                        {{ $fairness }} <span class="text-[10px] font-normal text-slate-400">Poin</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        @if($metrics['adjustment'] > 0)
                                            <span class="text-emerald-600 font-medium">+{{ $metrics['adjustment'] }} prioritas baru</span>
                                        @elseif($metrics['adjustment'] < 0)
                                            @if($metrics['is_pending'] ?? false)
                                                <span class="text-amber-700 font-medium">{{ $metrics['adjustment'] }} kuota aktif lain</span>
                                            @else
                                                <span class="text-slate-400">{{ $metrics['adjustment'] }} jeda bansos</span>
                                            @endif
                                        @else
                                            <span class="text-slate-400">Normal</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    Tidak ada data rumah yang sesuai dengan filter di RT ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Modal Footer --}}
            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <span class="text-xs text-slate-500 font-normal">
                    Pastikan jumlah warga yang dicentang tidak melebihi kuota <strong>{{ $currentRtQuota }} KK</strong>.
                </span>
                <div class="flex items-center space-x-2 justify-end">
                    <button type="button" 
                            wire:click="$set('showProposeModal', false)" 
                            class="h-9 px-4 rounded-xl bg-white border border-slate-300 text-slate-700 font-medium text-xs hover:bg-slate-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" 
                            wire:click="saveProposedRecipients" 
                            class="h-9 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition shadow-xs cursor-pointer">
                        Simpan Usulan RT
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ========================================== -->
    <!-- MODAL 2: AJUKAN PENGGANTIAN / SANGGAHAN (TAB B) -->
    <!-- ========================================== -->
    @if($showReplaceModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Ajukan Penggantian Penerima Bantuan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Penerima Semula: <strong class="text-slate-800">{{ $targetRecipientName }}</strong></p>
                </div>
                <button type="button" wire:click="$set('showReplaceModal', false)" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit="confirmReplacement" class="space-y-4 text-xs">
                <!-- 1. Alasan Penggantian -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Alasan Warga Semula Dibatalkan</label>
                    <select wire:model="alasan_penggantian" class="w-full h-10 px-2.5 rounded-xl border border-slate-300 text-slate-800 font-medium focus:ring-1 focus:ring-emerald-500">
                        <option value="meninggal">Meninggal Dunia</option>
                        <option value="pindah_domisili">Pindah Domisili Keluar RT/RW</option>
                        <option value="sudah_mampu">Kondisi Ekonomi Sudah Mampu (Graduasi Mandiri)</option>
                        <option value="data_ganda">Data Ganda / Tidak Ditemukan di Lapangan</option>
                    </select>
                </div>

                <!-- 2. Pilih Warga Pengganti -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Pilih Warga Pengganti (Satu RT yang Sama)</label>
                    <select wire:model="digantikan_oleh_resident_id" class="w-full h-10 px-2.5 rounded-xl border border-slate-300 text-slate-800 font-medium focus:ring-1 focus:ring-emerald-500">
                        <option value="">-- Pilih Warga Pengganti --</option>
                        @foreach($availableReplacements as $rep)
                            @php
                                $rHouse = $rep->house;
                                $rMetrics = $rHouse?->rotation_metrics;
                                $rAdjStr = ($rMetrics['adjustment'] ?? 0) >= 0 ? '+' . ($rMetrics['adjustment'] ?? 0) : ($rMetrics['adjustment'] ?? 0);
                            @endphp
                            <option value="{{ $rep->id }}">
                                {{ $rep->nama_lengkap }} (NIK: {{ $rep->nik }}) • {{ $rHouse?->nomor_rumah }} • Skor Giliran: {{ $rep->fairness_score }} Poin [Base {{ $rMetrics['base_score'] ?? 0 }} {{ $rAdjStr }} | {{ $rMetrics['status_label'] ?? 'Siap' }}]
                            </option>
                        @endforeach
                    </select>
                    @error('digantikan_oleh_resident_id') <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 3. Catatan Penggantian -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Keterangan / Berita Acara Lapangan</label>
                    <textarea wire:model="catatan_penggantian" 
                              rows="2" 
                              placeholder="Contoh: Warga telah meninggal dunia pada awal bulan dan tidak memiliki ahli waris yang berhak..."
                              class="w-full p-2.5 rounded-xl border border-slate-300 text-slate-800 placeholder-slate-400 focus:ring-1 focus:ring-emerald-500"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <button type="button" wire:click="$set('showReplaceModal', false)" class="h-10 px-4 rounded-xl bg-white border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="h-10 px-5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold shadow-xs">
                        Simpan Penggantian
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- ========================================== -->
    <!-- MODAL 3: PENYERAHAN BANTUAN HARI-H (TAB C) -->
    <!-- ========================================== -->
    @if($showDeliveryModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Konfirmasi Penyerahan Bantuan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Penerima: <strong class="text-slate-800">{{ $deliveringRecipientName }}</strong></p>
                </div>
                <button type="button" wire:click="$set('showDeliveryModal', false)" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit="saveDelivery" class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status Pengambilan</label>
                    <select wire:model="status_penyerahan" class="w-full h-10 px-2.5 rounded-xl border border-slate-300 text-slate-800 font-medium focus:ring-1 focus:ring-emerald-500">
                        <option value="sudah_diterima">Diambil Sendiri oleh Kepala Keluarga</option>
                        <option value="diwakilkan">Diwakilkan Anggota Keluarga Lain</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Orang yang Mengambil Bantuan</label>
                    <input type="text" 
                           wire:model="penerima_aktual" 
                           class="w-full h-10 px-3 rounded-xl border border-slate-300 text-slate-800 font-medium focus:ring-1 focus:ring-emerald-500">
                    @error('penerima_aktual') <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Foto Bukti Penyerahan (Opsional)</label>
                    <input type="file" 
                           wire:model="bukti_foto" 
                           accept="image/*"
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    @error('bukti_foto') <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    <div wire:loading wire:target="bukti_foto" class="text-[11px] text-emerald-700 font-semibold mt-1">
                        Mengunggah foto...
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <button type="button" wire:click="$set('showDeliveryModal', false)" class="h-10 px-4 rounded-xl bg-white border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="h-10 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-xs">
                        Konfirmasi Penyerahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- ========================================== -->
    <!-- MODAL 4: KONFIRMASI HAPUS USULAN WARGA -->
    <!-- ========================================== -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center space-x-3.5">
                <div class="w-11 h-11 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 shadow-xs shadow-rose-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">Hapus Usulan Calon Penerima?</h3>
                    <p class="text-xs text-slate-500">Kupon dan alokasi kuota RT akan diperbarui</p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1">
                <div class="text-xs font-bold text-slate-800">{{ $deletingRecipientName }}</div>
                <div class="text-[11px] font-mono text-slate-500">Nomor Kupon: {{ $deletingRecipientKupon }}</div>
            </div>

            <p class="text-xs text-slate-500 leading-relaxed">
                Warga ini akan dikeluarkan dari daftar usulan penerima bantuan. Kuota alokasi RT akan kembali tersedia untuk diisi warga lain.
            </p>

            <div class="flex items-center justify-end space-x-2.5 pt-2 border-t border-slate-100">
                <button type="button" 
                        wire:click="cancelRemoveRecipient" 
                        class="h-9 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                    Batal
                </button>
                <button type="button" 
                        wire:click="executeRemoveRecipient" 
                        class="h-9 px-5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                    Ya, Hapus Usulan
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- ========================================== -->
    <!-- MODAL 5: KONFIRMASI PENGESAHAN MASSAL RW -->
    <!-- ========================================== -->
    @if($showApproveAllModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center space-x-3.5">
                <div class="w-11 h-11 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 shadow-xs shadow-emerald-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">Sahkan Seluruh Usulan?</h3>
                    <p class="text-xs text-slate-500">
                        @if(!$program->areAllRtQuotasFilled())
                            Program tetap di Tahap Pendataan RT (Kuota belum penuh)
                        @elseif($program->isDistributionDue())
                            Status program otomatis beralih ke Tahap Penyaluran (Hari-H)
                        @else
                            Program berstatus Siap Disalurkan (Menunggu Jadwal Penyaluran)
                        @endif
                    </p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-200/80 space-y-1">
                <div class="text-xs font-bold text-emerald-950">
                    {{ $activeRecipients->where('status_usulan', 'diusulkan_rt')->count() }} Usulan Warga akan Disahkan
                </div>
                <div class="text-[11px] text-emerald-800">
                    @if(!$program->areAllRtQuotasFilled())
                        Usulan yang masuk akan disahkan RW. Tahap Penyaluran baru akan aktif setelah kuota seluruh RT ({{ $program->total_kuota }} KK) terpenuhi.
                    @elseif($program->isDistributionDue())
                        Nomor kupon resmi akan dikunci dan akses Tahap Penyaluran Hari-H akan langsung dibuka.
                    @else
                        Nomor kupon resmi akan dikunci. Program berstatus Siap Disalurkan dan tahap Penyaluran Hari-H otomatis aktif pada {{ $program->tanggal_penyaluran->locale('id')->translatedFormat('d F Y') }}.
                    @endif
                </div>
            </div>

            <p class="text-xs text-slate-500 leading-relaxed">
                @if(!$program->areAllRtQuotasFilled())
                    Pengurus RW menyetujui {{ $activeRecipients->where('status_usulan', 'diusulkan_rt')->count() }} usulan yang sudah masuk. RT lain tetap dapat mengisi sisa slot kuota yang belum lengkap.
                @elseif($program->isDistributionDue())
                    Pengurus RW akan menyetujui seluruh usulan RT yang tersisa dan membuka akses ke Tab Penyaluran Hari-H untuk serah terima paket dan pencatatan tanda terima.
                @else
                    Pengurus RW akan menyetujui seluruh usulan RT. Dokumen Daftar Hadir dan Kupon dapat langsung dicetak untuk persiapan. Penyaluran Hari-H akan otomatis dibuka saat tanggal jadwal penyaluran tiba.
                @endif
            </p>

            <div class="flex items-center justify-end space-x-2.5 pt-2 border-t border-slate-100">
                <button type="button" 
                        wire:click="cancelApproveAll" 
                        class="h-9 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                    Batal
                </button>
                <button type="button" 
                        wire:click="executeApproveAll" 
                        class="h-9 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                    Ya, Sahkan Usulan
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
