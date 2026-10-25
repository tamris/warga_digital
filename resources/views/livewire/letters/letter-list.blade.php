<div class="space-y-6">
    <!-- Header Modul -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-2xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Modul Layanan Warga
                </span>
                <span class="text-xs text-slate-300">•</span>
                <span class="text-xs text-slate-500 font-semibold">
                    {{ $user->rt ? 'Buku Register RT ' . str_pad((string)((int)$user->rt->nomor_rt), 2, '0', STR_PAD_LEFT) : 'Buku Register Seluruh RW 01' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                Register Surat Pengantar RT/RW
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Pencatatan resmi penomoran, arsip historis, dan cetak surat pengantar warga
            </p>
        </div>

        @if($user->isAdminRw() || in_array($user->role, ['ketua_rt', 'sekretaris_rt']))
            <a href="{{ route('letters.create') }}" 
               wire:navigate
               class="h-10 px-4 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold inline-flex items-center justify-center space-x-2 transition shadow-2xs shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Buat Surat Pengantar</span>
            </a>
        @endif
    </div>

    <!-- Kartu Statistik Cepat (4 Kolom Rapi) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Terbit (Sah)</span>
                <div class="text-2xl font-extrabold text-emerald-700 mt-1">
                    {{ $totalTerbitTahunIni }} <span class="text-xs font-semibold text-slate-400">Surat</span>
                </div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center justify-between {{ $totalPending > 0 ? 'ring-1 ring-amber-400 bg-amber-50/20' : '' }}">
            <div>
                <span class="text-xs font-bold text-amber-700 uppercase tracking-wider">Pending</span>
                <div class="text-2xl font-extrabold text-amber-700 mt-1">
                    {{ $totalPending }} <span class="text-xs font-semibold text-amber-600/70">Berkas</span>
                </div>
                <div class="text-[10.5px] font-semibold text-amber-800/80 mt-0.5">
                    RT: {{ $totalMenungguRt }} • RW: {{ $totalMenungguRw }}
                </div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Terbit Bulan Ini</span>
                <div class="text-2xl font-extrabold text-sky-700 mt-1">
                    {{ $totalTerbitBulanIni }} <span class="text-xs font-semibold text-slate-400">Surat</span>
                </div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-sky-50 text-sky-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ditolak / Batal</span>
                <div class="text-2xl font-extrabold text-rose-700 mt-1">
                    {{ $totalDibatalkan }} <span class="text-xs font-semibold text-slate-400">Surat</span>
                </div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
        </div>
    </div>

    <!-- Banner Aksi Khusus Ketua RT jika ada surat menunggu persetujuan RT -->
    @if(($user->role === 'ketua_rt' || $user->isAdminRw()) && $totalMenungguRt > 0)
        <div class="bg-gradient-to-r from-amber-50 via-amber-50/80 to-amber-100/50 border border-amber-300 rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-2xs">
            <div class="flex items-start sm:items-center space-x-3.5">
                <div class="w-11 h-11 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-amber-800 bg-amber-200/70 px-2 py-0.5 rounded">
                            Tugas Ketua RT
                        </span>
                        <span class="text-xs font-bold text-amber-700">• {{ $totalMenungguRt }} Surat Perlu Persetujuan RT</span>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900 mt-0.5">
                        Ada {{ $totalMenungguRt }} Surat Pengantar buatan Sekretaris RT yang Menunggu Tanda Tangan Anda (TTE RT)
                    </h3>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Surat resmi perlu Anda setujui terlebih dahulu sebelum diteruskan ke Ketua RW untuk pengesahan akhir.
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-2 shrink-0">
                <button type="button" 
                        wire:click="$set('selectedStatus', 'menunggu_rt')" 
                        class="h-9 px-3.5 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition shadow-2xs">
                    Tampilkan Pending
                </button>
                <button type="button" 
                        wire:click="openBulkApproveRtModal" 
                        class="h-9 px-4 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold inline-flex items-center space-x-1.5 transition shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Setujui Semua</span>
                </button>
            </div>
        </div>
    @endif

    <!-- Banner Aksi Khusus Ketua RW jika ada surat menunggu persetujuan RW -->
    @if($user->isAdminRw() && $totalMenungguRw > 0)
        <div class="bg-gradient-to-r from-emerald-50 via-emerald-50/80 to-teal-50 border border-emerald-300 rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-2xs">
            <div class="flex items-start sm:items-center space-x-3.5">
                <div class="w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-800 bg-emerald-200/70 px-2 py-0.5 rounded">
                            Tugas Ketua RW
                        </span>
                        <span class="text-xs font-bold text-emerald-700">• {{ $totalMenungguRw }} Surat Perlu Pengesahan RW</span>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900 mt-0.5">
                        Ada {{ $totalMenungguRw }} Surat Pengantar yang Menunggu Tanda Tangan Anda (TTE RW)
                    </h3>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Surat telah disetujui Ketua RT dan menunggu pengesahan Anda sebelum berstatus Terbit resmi.
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-2 shrink-0">
                <button type="button" 
                        wire:click="$set('selectedStatus', 'menunggu_rw')" 
                        class="h-9 px-3.5 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition shadow-2xs">
                    Tampilkan Pending
                </button>
                <button type="button" 
                        wire:click="openBulkApproveModal" 
                        class="h-9 px-4 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold inline-flex items-center space-x-1.5 transition shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Setujui Semua</span>
                </button>
            </div>
        </div>
    @endif

    <!-- Filter Toolbar & Table Container -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <!-- Filter Bar -->
        <div class="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
            <div class="flex-1 relative">
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="Cari nomor surat, nama pemohon, NIK, atau keperluan..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 text-slate-800 placeholder-slate-400 shadow-2xs">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if($user->isAdminRw())
                    <select wire:model.live="selectedRtId" 
                            class="text-xs bg-white border border-slate-200 rounded-lg px-2.5 py-2 font-medium text-slate-700 focus:outline-none focus:ring-1 focus:ring-emerald-500 shadow-2xs">
                        <option value="">Semua Wilayah RT</option>
                        @foreach($rts as $rt)
                            <option value="{{ $rt->id }}">RT {{ str_pad((string)((int)$rt->nomor_rt), 2, '0', STR_PAD_LEFT) }}</option>
                        @endforeach
                    </select>
                @endif

                <select wire:model.live="selectedMonth" 
                        class="text-xs bg-white border border-slate-200 rounded-lg px-2.5 py-2 font-medium text-slate-700 focus:outline-none focus:ring-1 focus:ring-emerald-500 shadow-2xs">
                    <option value="">Semua Bulan</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create(2026, $m, 1)->locale('id')->translatedFormat('F') }}</option>
                    @endfor
                </select>

                <select wire:model.live="selectedStatus" 
                        class="text-xs bg-white border border-slate-200 rounded-lg px-2.5 py-2 font-medium text-slate-700 focus:outline-none focus:ring-1 focus:ring-emerald-500 shadow-2xs">
                    <option value="">Semua Status</option>
                    <option value="menunggu_rt">Pending RT</option>
                    <option value="menunggu_rw">Pending RW</option>
                    <option value="terbit">Terbit (Sah)</option>
                    <option value="ditolak_rt">Ditolak RT</option>
                    <option value="ditolak_rw">Ditolak RW</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>

                <select wire:model.live="perPage" 
                        class="text-xs bg-white border border-slate-200 rounded-lg px-2.5 py-2 font-medium text-slate-700 focus:outline-none focus:ring-1 focus:ring-emerald-500 shadow-2xs"
                        title="Jumlah data per halaman">
                    <option value="10">10 / hal</option>
                    <option value="25">25 / hal</option>
                    <option value="50">50 / hal</option>
                    <option value="100">100 / hal</option>
                </select>
            </div>
        </div>

        <!-- Tabel Register Surat -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-5">Nomor Surat</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Pemohon</th>
                        <th class="py-3 px-3 text-center">RT</th>
                        <th class="py-3 px-4">Keperluan Surat</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($letters as $letter)
                    <tr class="hover:bg-slate-50/80 transition-colors {{ in_array($letter->status, ['dibatalkan', 'ditolak', 'ditolak_rt', 'ditolak_rw']) ? 'opacity-75 bg-rose-50/20' : ($letter->status === 'menunggu_rt' ? 'bg-amber-50/35' : ($letter->status === 'menunggu_rw' ? 'bg-amber-50/25' : '')) }}">
                        <td class="py-3.5 px-5">
                            <div class="font-bold text-slate-900 text-sm font-mono tracking-tight">{{ $letter->nomor_surat }}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Dibuat oleh: {{ $letter->creator_name }}</div>
                        </td>

                        <td class="py-3.5 px-4 whitespace-nowrap text-xs font-semibold text-slate-700">
                            {{ \Carbon\Carbon::parse($letter->tanggal_surat)->locale('id')->translatedFormat('d M Y') }}
                        </td>

                        <td class="py-3.5 px-4">
                            <div class="font-bold text-slate-900 text-sm">{{ $letter->snapshot_nama }}</div>
                            <div class="text-xs font-mono text-slate-400 mt-0.5">NIK: {{ $letter->snapshot_nik }}</div>
                        </td>

                        <td class="py-3.5 px-3 text-center">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                RT {{ $letter->rt?->nomor_rt ? str_pad((string)((int)$letter->rt->nomor_rt), 2, '0', STR_PAD_LEFT) : '01' }}
                            </span>
                        </td>

                        <td class="py-3.5 px-4">
                            <div class="font-semibold text-slate-900 text-xs">{{ $letter->jenis_keperluan }}</div>
                            @if($letter->keterangan_keperluan)
                                <div class="text-[11px] text-slate-500 mt-0.5 italic line-clamp-1" title="{{ $letter->keterangan_keperluan }}">
                                    {{ $letter->keterangan_keperluan }}
                                </div>
                            @endif
                        </td>

                        <td class="py-3.5 px-3 text-center whitespace-nowrap">
                            @if($letter->status === 'terbit')
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Terbit
                                </span>
                            @elseif($letter->status === 'menunggu_rt')
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-300" title="Pending: Menunggu persetujuan & TTE Ketua RT">
                                    Pending RT
                                </span>
                            @elseif($letter->status === 'menunggu_rw')
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200" title="Pending: Menunggu persetujuan & TTE Ketua RW">
                                    Pending RW
                                </span>
                            @elseif($letter->status === 'ditolak_rt')
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200" title="Alasan Tolak RT: {{ $letter->alasan_tolak }}">
                                    Ditolak RT
                                </span>
                            @elseif(in_array($letter->status, ['ditolak_rw', 'ditolak']))
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200" title="Alasan Tolak RW: {{ $letter->alasan_tolak }}">
                                    Ditolak RW
                                </span>
                            @else
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200" title="Alasan: {{ $letter->alasan_batal }}">
                                    Dibatalkan
                                </span>
                            @endif
                        </td>

                        <td class="py-3.5 px-5 text-right whitespace-nowrap">
                            <div class="inline-flex items-center space-x-1.5">
                                <!-- Tombol Aksi Persetujuan Khusus Ketua RT untuk Surat Menunggu RT -->
                                @if($letter->status === 'menunggu_rt' && (($user->role === 'ketua_rt' && $letter->rt_id === $user->rt_id) || $user->isAdminRw()))
                                    <button type="button" 
                                            wire:click="openApproveRtModal({{ $letter->id }})" 
                                            class="h-7 px-2.5 rounded-md bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold inline-flex items-center space-x-1 transition shadow-2xs"
                                            title="Buka Konfirmasi Pengesahan & TTE RT">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Setujui</span>
                                    </button>

                                    <button type="button" 
                                            wire:click="openRejectModal({{ $letter->id }})" 
                                            class="h-7 px-2 rounded-md bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-300 text-rose-600 text-xs font-semibold inline-flex items-center space-x-1 transition shadow-2xs"
                                            title="Tolak Surat Pengantar">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        <span>Tolak</span>
                                    </button>
                                @endif

                                <!-- Tombol Aksi Persetujuan Khusus Ketua RW untuk Surat Menunggu RW -->
                                @if($user->isAdminRw() && $letter->status === 'menunggu_rw')
                                    <button type="button" 
                                            wire:click="openApproveModal({{ $letter->id }})" 
                                            class="h-7 px-2.5 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold inline-flex items-center space-x-1 transition shadow-2xs"
                                            title="Buka Konfirmasi Pengesahan & TTE RW">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Setujui</span>
                                    </button>

                                    <button type="button" 
                                            wire:click="openRejectModal({{ $letter->id }})" 
                                            class="h-7 px-2 rounded-md bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-300 text-rose-600 text-xs font-semibold inline-flex items-center space-x-1 transition shadow-2xs"
                                            title="Tolak Surat Pengantar">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        <span>Tolak</span>
                                    </button>
                                @endif

                                <!-- Cetak / Pratinjau Draf -->
                                @if($user->isAdminRw() || in_array($user->role, ['ketua_rt', 'sekretaris_rt']))
                                    <a href="{{ route('letters.print', $letter->id) }}" 
                                       target="_blank"
                                       class="h-7 px-2.5 rounded-md bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold inline-flex items-center space-x-1 transition shadow-2xs"
                                       title="{{ in_array($letter->status, ['menunggu_rt', 'menunggu_rw']) ? 'Pratinjau Draf Surat' : 'Cetak Surat (Tab Baru)' }}">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        <span>{{ in_array($letter->status, ['menunggu_rt', 'menunggu_rw']) ? '' : '' }}</span>
                                    </a>

                                    <a href="{{ route('letters.download', $letter->id) }}" 
                                       class="h-7 w-7 rounded-md bg-white hover:bg-slate-100 border border-slate-200 text-slate-600 inline-flex items-center justify-center transition shadow-2xs"
                                       title="Unduh File PDF">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>

                                    <a href="{{ $letter->verification_url }}" 
                                       target="_blank"
                                       class="h-7 w-7 rounded-md bg-sky-50 hover:bg-sky-100 border border-sky-200 text-sky-700 inline-flex items-center justify-center transition shadow-2xs"
                                       title="Verifikasi Keaslian TTE (Scan / Link Publik)">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    </a>
                                @endif

                                <!-- Batalkan Surat -->
                                @if($letter->status === 'terbit' && ($user->isAdminRw() || $user->role === 'ketua_rt'))
                                    <button type="button" 
                                            wire:click="openCancelModal({{ $letter->id }})" 
                                            class="h-7 w-7 rounded-md bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-300 text-slate-500 hover:text-rose-700 inline-flex items-center justify-center transition shadow-2xs"
                                            title="Batalkan Surat">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-500">
                            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="text-sm font-bold text-slate-800">Tidak ada data surat pengantar</p>
                            <p class="text-xs text-slate-400 mt-1">Belum ada surat yang diterbitkan atau sesuaikan filter pencarian Anda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($letters->total() > 0)
            <div class="p-4 bg-slate-50/50 border-t border-slate-100">
                {{ $letters->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Konfirmasi Pembatalan Surat -->
    @if($showCancelModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200">
            <div class="flex items-center space-x-3 text-rose-600 mb-3">
                <div class="w-10 h-10 rounded-full bg-rose-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Batalkan Surat Pengantar</h3>
                    <p class="text-xs text-slate-500">Tindakan ini akan menandai surat sebagai tidak berlaku.</p>
                </div>
            </div>

            <form wire:submit="confirmCancelLetter" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alasan Pembatalan <span class="text-rose-500">*</span>
                    </label>
                    <textarea wire:model="cancelReason" 
                              rows="3"
                              placeholder="Contoh: Salah penulisan keperluan pemohon / Warga membatalkan permohonan..."
                              class="w-full text-xs p-3 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 text-slate-800 placeholder-slate-400"></textarea>
                    @error('cancelReason') <span class="text-xs text-rose-600 font-semibold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" 
                            wire:click="$set('showCancelModal', false)"
                            class="h-9 px-4 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="h-9 px-4 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-2xs">
                        Konfirmasi Pembatalan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Modal Penolakan Surat oleh RW -->
    @if($showRejectModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200">
            <div class="flex items-center space-x-3 text-rose-600 mb-3">
                <div class="w-10 h-10 rounded-full bg-rose-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Tolak Surat Pengantar</h3>
                    <p class="text-xs text-slate-500">Berikan catatan / alasan penolakan untuk pengurus RT.</p>
                </div>
            </div>

            <form wire:submit="confirmRejectLetter" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alasan Penolakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea wire:model="rejectReason" 
                              rows="3"
                              placeholder="Contoh: Berkas lampiran belum lengkap / Data keperluan perlu diperjelas..."
                              class="w-full text-xs p-3 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 text-slate-800 placeholder-slate-400"></textarea>
                    @error('rejectReason') <span class="text-xs text-rose-600 font-semibold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" 
                            wire:click="$set('showRejectModal', false)"
                            class="h-9 px-4 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="h-9 px-4 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-2xs">
                        Konfirmasi Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Modal Konfirmasi Persetujuan & TTE RT (Single Letter) -->
    @if($showApproveRtModal && $letterToApproveRt)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
            <!-- Header Modal -->
            <div class="flex items-start space-x-3.5 mb-4 pb-4 border-b border-slate-100">
                <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </div>
                <div class="flex-1">
                    <span class="inline-block text-[10px] font-extrabold uppercase tracking-wider bg-amber-100 text-amber-800 px-2 py-0.5 rounded">
                        Persetujuan Ketua RT
                    </span>
                    <h3 class="text-base font-bold text-slate-900 mt-1">
                        Persetujuan & Tanda Tangan Elektronik RT
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Surat ini dibuat oleh Sekretaris RT. Periksa kelayakan data sebelum Anda sahkan & teruskan ke Ketua RW.
                    </p>
                </div>
                <button type="button" wire:click="$set('showApproveRtModal', false)" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Detail Dokumen Card -->
            <div class="bg-slate-50/80 rounded-xl p-4 border border-slate-200/80 space-y-2.5 text-xs mb-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                    <span class="text-slate-500 font-semibold">Nomor Surat:</span>
                    <span class="font-mono font-bold text-slate-900">{{ $letterToApproveRt->nomor_surat }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">Nama Pemohon:</span>
                    <span class="font-bold text-slate-900">{{ $letterToApproveRt->snapshot_nama }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">NIK:</span>
                    <span class="font-mono text-slate-700">{{ $letterToApproveRt->snapshot_nik }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">Asal Wilayah:</span>
                    <span class="font-semibold text-slate-800">RT {{ $letterToApproveRt->rt?->nomor_rt ? str_pad((string)((int)$letterToApproveRt->rt->nomor_rt), 2, '0', STR_PAD_LEFT) : '01' }} / RW 01</span>
                </div>
                <div class="pt-2 border-t border-slate-200/60">
                    <span class="text-slate-500 font-semibold block mb-0.5">Keperluan Surat:</span>
                    <span class="font-bold text-slate-900 block">{{ $letterToApproveRt->jenis_keperluan }}</span>
                    @if($letterToApproveRt->keterangan_keperluan)
                        <span class="text-[11px] text-slate-500 italic block mt-0.5">{{ $letterToApproveRt->keterangan_keperluan }}</span>
                    @endif
                </div>
            </div>

            <!-- Catatan Pengesahan RT -->
            <div class="p-3 bg-amber-50/70 border border-amber-200 rounded-xl text-[11px] text-amber-800 flex items-start space-x-2.5 mb-5">
                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>
                    Dengan menekan tombol setujui, tanda tangan elektronik resmi (TTE) Ketua RT atas nama <strong>{{ $letterToApproveRt->nama_ketua_rt }}</strong> akan dibubuhkan dan status surat beralih menjadi <strong>Pending RW</strong> untuk pengesahan akhir oleh Ketua RW.
                </span>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-2.5 pt-2 border-t border-slate-100">
                <button type="button" 
                        wire:click="$set('showApproveRtModal', false)"
                        class="h-9 px-4 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition">
                    Batal
                </button>
                <button type="button" 
                        wire:click="confirmApproveRtLetter"
                        class="h-9 px-4 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold inline-flex items-center space-x-1.5 transition shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Ya, Sahkan & Bubuhkan TTE RT</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Konfirmasi Persetujuan Masal RT (Bulk Approve) -->
    @if($showBulkApproveRtModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-start space-x-3.5 mb-4">
                <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Sahkan Semua Surat RT Sekaligus?</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Anda akan menyetujui dan membubuhkan TTE Ketua RT untuk seluruh <strong>{{ $totalMenungguRt }} surat pengantar</strong> buatan Sekretaris RT agar diteruskan ke Ketua RW.
                    </p>
                </div>
            </div>

            <div class="p-3 bg-amber-50/70 border border-amber-200 rounded-xl text-xs text-amber-800 mb-5">
                Pastikan Anda telah memeriksa ringkasan berkas sebelum melakukan pengesahan masal.
            </div>

            <div class="flex items-center justify-end space-x-2.5 pt-2 border-t border-slate-100">
                <button type="button" 
                        wire:click="$set('showBulkApproveRtModal', false)"
                        class="h-9 px-4 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition">
                    Batal
                </button>
                <button type="button" 
                        wire:click="confirmBulkApproveRt"
                        class="h-9 px-4 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold inline-flex items-center space-x-1.5 transition shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Ya, Sahkan {{ $totalMenungguRt }} Surat (RT)</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Konfirmasi Persetujuan & TTE RW (Single Letter) -->
    @if($showApproveModal && $letterToApprove)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
            <!-- Header Modal -->
            <div class="flex items-start space-x-3.5 mb-4 pb-4 border-b border-slate-100">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div class="flex-1">
                    <span class="inline-block text-[10px] font-extrabold uppercase tracking-wider bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded">
                        Pengesahan Resmi RW 01
                    </span>
                    <h3 class="text-base font-bold text-slate-900 mt-1">
                        Persetujuan & Tanda Tangan Elektronik (TTE)
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pastikan data pemohon dan keperluan surat di bawah ini sudah sesuai.
                    </p>
                </div>
                <button type="button" wire:click="$set('showApproveModal', false)" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Detail Dokumen Card -->
            <div class="bg-slate-50/80 rounded-xl p-4 border border-slate-200/80 space-y-2.5 text-xs mb-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                    <span class="text-slate-500 font-semibold">Nomor Surat:</span>
                    <span class="font-mono font-bold text-slate-900">{{ $letterToApprove->nomor_surat }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">Nama Pemohon:</span>
                    <span class="font-bold text-slate-900">{{ $letterToApprove->snapshot_nama }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">NIK:</span>
                    <span class="font-mono text-slate-700">{{ $letterToApprove->snapshot_nik }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">Asal Wilayah:</span>
                    <span class="font-semibold text-slate-800">RT {{ $letterToApprove->rt?->nomor_rt ? str_pad((string)((int)$letterToApprove->rt->nomor_rt), 2, '0', STR_PAD_LEFT) : '01' }} / RW 01</span>
                </div>
                <div class="pt-2 border-t border-slate-200/60">
                    <span class="text-slate-500 font-semibold block mb-0.5">Keperluan Surat:</span>
                    <span class="font-bold text-emerald-900 block">{{ $letterToApprove->jenis_keperluan }}</span>
                    @if($letterToApprove->keterangan_keperluan)
                        <span class="text-[11px] text-slate-500 italic block mt-0.5">{{ $letterToApprove->keterangan_keperluan }}</span>
                    @endif
                </div>
            </div>

            <!-- Catatan Keamanan / TTE -->
            <div class="p-3 bg-emerald-50/60 border border-emerald-200/70 rounded-xl text-[11px] text-emerald-800 flex items-start space-x-2.5 mb-5">
                <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>
                    Dengan menekan tombol setujui, tanda tangan elektronik resmi (TTE) Ketua RW 01 atas nama <strong>AMAT MAJA</strong> akan dibubuhkan dan status surat otomatis menjadi <strong>Terbit (Sah)</strong>.
                </span>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-2.5 pt-2 border-t border-slate-100">
                <button type="button" 
                        wire:click="$set('showApproveModal', false)"
                        class="h-9 px-4 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition">
                    Batal
                </button>
                <button type="button" 
                        wire:click="confirmApproveLetter"
                        class="h-9 px-4 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold inline-flex items-center space-x-1.5 transition shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Ya, Sahkan & Bubuhkan TTE</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Konfirmasi Persetujuan Masal (Bulk Approve) -->
    @if($showBulkApproveModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-start space-x-3.5 mb-4">
                <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Sahkan Semua Surat Sekaligus?</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Anda akan menyetujui dan menandatangani secara elektronik (TTE RW) seluruh <strong>{{ $totalMenungguRw }} surat pengantar</strong> yang saat ini berstatus Pending.
                    </p>
                </div>
            </div>

            <div class="p-3 bg-amber-50/70 border border-amber-200 rounded-xl text-xs text-amber-800 mb-5">
                Pastikan Anda telah memeriksa ringkasan permohonan surat-surat tersebut sebelum melakukan pengesahan masal.
            </div>

            <div class="flex items-center justify-end space-x-2.5 pt-2 border-t border-slate-100">
                <button type="button" 
                        wire:click="$set('showBulkApproveModal', false)"
                        class="h-9 px-4 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition">
                    Batal
                </button>
                <button type="button" 
                        wire:click="confirmBulkApprove"
                        class="h-9 px-4 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold inline-flex items-center space-x-1.5 transition shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Ya, Sahkan {{ $totalMenungguRw }} Surat</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
