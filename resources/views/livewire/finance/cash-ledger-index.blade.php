<div class="space-y-6">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <span>Portal Pengelolaan Warga</span>
                <span>/</span>
                <span>Modul Keuangan</span>
                <span>/</span>
                <span class="text-slate-800 font-bold">Buku Kas Operasional</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center space-x-2.5">
                <span>Buku Kas Umum</span>
                @if($user && $user->isRwLevel())
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold {{ $scopeType === 'rw' ? 'bg-purple-100 text-purple-800 border border-purple-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                        {{ $scopeType === 'rw' ? 'Kas Induk RW 01' : ($selectedRtId ? 'Kas Operasional RT ' . str_pad((string)((int)$rts->firstWhere('id', $selectedRtId)?->nomor_rt), 2, '0', STR_PAD_LEFT) : 'Kas Seluruh RT') }}
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                        Kas Operasional RT {{ $user->rt ? str_pad((string)((int)$user->rt->nomor_rt), 2, '0', STR_PAD_LEFT) : '01' }}
                    </span>
                @endif
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Pembukuan arus kas masuk dan kas keluar transparan beserta lampiran foto nota dan kuitansi fisik.
            </p>
        </div>

        <!-- Tombol Aksi Kas Baru -->
        @if($user && $user->canManageFinance())
            <div class="flex items-center space-x-2.5 shrink-0">
                <button type="button" 
                        wire:click="openCreateModal('masuk')"
                        class="h-9 px-3.5 rounded-lg border-2 border-emerald-600 text-emerald-700 hover:bg-emerald-50 text-xs font-bold inline-flex items-center space-x-1.5 transition shadow-2xs">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Catat Kas Masuk</span>
                </button>
                <button type="button" 
                        wire:click="openCreateModal('keluar')"
                        class="h-9 px-4 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold inline-flex items-center space-x-1.5 transition shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                    <span>Catat Pengeluaran</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Toggle Tab Pemisahan Kas (Khusus Level RW) -->
    @if($user && $user->isRwLevel())
        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
            <div class="flex items-center space-x-1 bg-slate-100 p-1 rounded-xl">
                <button type="button" 
                        wire:click="$set('scopeType', 'rw')" 
                        class="h-8 px-4 rounded-lg text-xs font-bold transition {{ $scopeType === 'rw' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Buku Kas Induk RW
                </button>
                <button type="button" 
                        wire:click="$set('scopeType', 'rt')" 
                        class="h-8 px-4 rounded-lg text-xs font-bold transition {{ $scopeType === 'rt' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Buku Kas Operasional RT
                </button>
            </div>

            @if($scopeType === 'rt')
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-semibold text-slate-500">Pilih RT:</span>
                    <select wire:model.live="selectedRtId" 
                            class="h-8 px-2.5 text-xs font-semibold rounded-lg border border-slate-300 text-slate-700 focus:border-emerald-500 outline-none bg-white">
                        <option value="">Semua Wilayah RT</option>
                        @foreach($rts as $rt)
                            <option value="{{ $rt->id }}">RT {{ $rt->nomor_rt }} ({{ $rt->nama_ketua }})</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    @endif

    <!-- Banner Notifikasi Setoran Kas RT Menunggu Konfirmasi (1-Click Opsi A1) -->
    @if(isset($pendingTransfers) && $pendingTransfers->isNotEmpty())
        <div class="rounded-2xl border-2 border-amber-300 bg-gradient-to-r from-amber-50 to-orange-50 p-4 sm:p-5 shadow-xs space-y-3.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold shadow-xs shrink-0">
                        <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-amber-950">
                            {{ $pendingTransfers->count() }} Setoran Kas Wajib RT Menunggu Konfirmasi Penerimaan
                        </h2>
                        <p class="text-xs text-amber-800">
                            Pengurus RT telah mencatat pengeluaran setor ke RW. Klik <strong>Terima Kas Masuk</strong> untuk langsung membukukan ke Kas Induk RW dalam 1 klik tanpa perlu input ulang.
                        </p>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-amber-200/80 bg-white/90 backdrop-blur-xs rounded-xl border border-amber-200 shadow-2xs overflow-hidden">
                @foreach($pendingTransfers as $pending)
                    <div class="p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-white transition">
                        <div class="space-y-1">
                            <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300">
                                    RT {{ str_pad((string)((int)$pending->rt?->nomor_rt), 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="text-sm font-black text-slate-900 font-mono">
                                    Rp {{ number_format($pending->nominal, 0, ',', '.') }}
                                </span>
                                <span class="text-[11px] text-slate-500 font-mono">
                                    ({{ $pending->tanggal?->format('d/m/Y') }})
                                </span>
                            </div>
                            <p class="text-xs text-slate-700 font-medium">
                                {{ $pending->clean_keterangan }}
                            </p>
                            <div class="text-[11px] text-slate-500 flex items-center space-x-2">
                                <span>Dicatat oleh: <strong class="text-slate-700">{{ $pending->recorder_name }}</strong></span>
                                @if($pending->bukti_foto)
                                    <span>•</span>
                                    <button type="button" 
                                            wire:click="viewPhoto('{{ $pending->bukti_foto }}', 'Bukti Setoran RT {{ $pending->rt?->nomor_rt }}')"
                                            class="text-emerald-700 hover:text-emerald-800 font-bold underline inline-flex items-center space-x-1 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Lihat Bukti Foto</span>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 shrink-0">
                            <button type="button" 
                                    wire:click="rejectRtTransfer({{ $pending->id }})"
                                    class="h-8 px-3 rounded-lg border border-slate-300 hover:bg-rose-50 text-slate-600 hover:text-rose-700 text-xs font-bold transition cursor-pointer">
                                Tolak
                            </button>
                            <button type="button" 
                                    wire:click="acceptRtTransfer({{ $pending->id }})"
                                    class="h-8 px-4 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold inline-flex items-center space-x-1.5 transition shadow-xs cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Terima & Catat Kas Masuk</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 3 Widget Kartu Saldo (Font Besar & Kontras Tinggi) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-4">
        <!-- Pemasukan Bulan Ini -->
        <div class="bg-white rounded-xl p-4 sm:p-5 border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Pemasukan Bulan Ini</span>
                <div class="text-2xl font-black text-emerald-700 mt-1 font-mono">
                    Rp {{ number_format($totalMasukBulanIni, 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Iuran warga & penerimaan kas</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
            </div>
        </div>

        <!-- Pengeluaran Bulan Ini -->
        <div class="bg-white rounded-xl p-4 sm:p-5 border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Pengeluaran Bulan Ini</span>
                <div class="text-2xl font-black text-rose-700 mt-1 font-mono">
                    Rp {{ number_format($totalKeluarBulanIni, 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Biaya operasional & kebersihan</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
            </div>
        </div>

        <!-- Sisa Saldo Kas Aktif -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-xl p-4 sm:p-5 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block">Sisa Saldo Kas Aktif</span>
                <div class="text-3xl font-black text-emerald-400 mt-1 font-mono tracking-tight">
                    Rp {{ number_format($saldoKasAktif, 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Saldo riil yang siap digunakan</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-white/10 backdrop-blur-xs text-white flex items-center justify-center shrink-0 border border-white/10">
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-xl p-3 sm:p-4 border border-slate-200 shadow-2xs space-y-3">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <!-- Pencarian -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Cari transaksi kas atau keterangan..." 
                       class="w-full h-9 pl-9 pr-3 text-xs rounded-lg border border-slate-300 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
            </div>

            <!-- Filter Dropdowns -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Filter Bulan -->
                <select wire:model.live="filterBulan" 
                        class="h-9 px-2.5 text-xs font-semibold rounded-lg border border-slate-300 text-slate-700 focus:border-emerald-500 outline-none bg-white">
                    <option value="">Semua Bulan</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}">{{ \App\Models\HouseDue::getMonthName($m) }}</option>
                    @endfor
                </select>

                <!-- Filter Tahun -->
                <select wire:model.live="filterTahun" 
                        class="h-9 px-2.5 text-xs font-semibold rounded-lg border border-slate-300 text-slate-700 focus:border-emerald-500 outline-none bg-white">
                    @for($y = 2025; $y <= 2028; $y++)
                        <option value="{{ $y }}">Tahun {{ $y }}</option>
                    @endfor
                </select>

                <!-- Filter Jenis -->
                <select wire:model.live="jenisTransaksi" 
                        class="h-9 px-2.5 text-xs font-semibold rounded-lg border border-slate-300 text-slate-700 focus:border-emerald-500 outline-none bg-white">
                    <option value="">Semua Jenis</option>
                    <option value="masuk">Kas Masuk (+)</option>
                    <option value="keluar">Kas Keluar (-)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Transaksi Buku Kas -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-28 whitespace-nowrap">Tanggal</th>
                        <th class="py-3.5 px-3 w-20 text-center whitespace-nowrap">Wilayah</th>
                        <th class="py-3.5 px-3.5 w-48">Kategori</th>
                        <th class="py-3.5 px-4">Uraian / Keterangan</th>
                        <th class="py-3.5 px-4 w-44 whitespace-nowrap">Dicatat Oleh</th>
                        <th class="py-3.5 px-3 w-28 text-center whitespace-nowrap">Bukti Fisik</th>
                        <th class="py-3.5 px-4 text-right w-36 whitespace-nowrap">Masuk (+)</th>
                        <th class="py-3.5 px-4 text-right w-36 whitespace-nowrap">Keluar (-)</th>
                        @if($user && $user->canManageFinance())
                            <th class="py-3.5 px-3 text-center w-14 whitespace-nowrap">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Tanggal -->
                            <td class="py-3.5 px-4 font-mono font-semibold text-slate-800 whitespace-nowrap">
                                {{ $trx->tanggal?->format('d/m/Y') }}
                            </td>

                            <!-- Wilayah -->
                            <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                @if($trx->rt_id)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        RT {{ str_pad((string)((int)$trx->rt?->nomor_rt), 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                        RW 01
                                    </span>
                                @endif
                            </td>

                            <!-- Kategori & Status Transfer -->
                            <td class="py-3.5 px-3.5">
                                <div class="flex flex-col gap-1 items-start">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold inline-block {{ $trx->jenis_transaksi === 'masuk' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                                        {{ $trx->kategori_label }}
                                    </span>
                                    @if($trx->status_transfer === 'menunggu_konfirmasi')
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-amber-100 text-amber-800 border border-amber-300 inline-flex items-center space-x-1" title="Menunggu Konfirmasi Bendahara RW">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span>Menunggu RW</span>
                                        </span>
                                    @elseif($trx->status_transfer === 'diterima')
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300 inline-flex items-center space-x-1" title="Telah dikonfirmasi dan masuk ke Kas RW">
                                            <svg class="w-2.5 h-2.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            <span>Diterima RW</span>
                                        </span>
                                    @elseif($trx->status_transfer === 'ditolak')
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-rose-100 text-rose-800 border border-rose-300 inline-flex items-center space-x-1" title="Setoran ditolak oleh RW">
                                            <svg class="w-2.5 h-2.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                            <span>Ditolak</span>
                                        </span>
                                    @elseif($trx->rt_id === null && $trx->kategori === 'setoran_rt' && $trx->related_ledger_id)
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-purple-100 text-purple-800 border border-purple-200 inline-flex items-center space-x-1" title="Otomatis dari Setoran RT">
                                            <span>Dari RT {{ str_pad((string)((int)$trx->relatedLedger?->rt?->nomor_rt), 2, '0', STR_PAD_LEFT) }}</span>
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Uraian / Keterangan -->
                            <td class="py-3.5 px-4">
                                @php
                                    $rawKet = $trx->clean_keterangan;
                                    $hasSplit = str_contains($rawKet, ' - ');
                                @endphp
                                @if($hasSplit)
                                    @php
                                        [$periodeTag, $objek] = explode(' - ', $rawKet, 2);
                                    @endphp
                                    <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                                        <span class="font-extrabold text-slate-900 text-xs tracking-tight leading-snug">{{ $objek }}</span>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs">
                                            {{ $periodeTag }}
                                        </span>
                                    </div>
                                @else
                                    <div class="font-extrabold text-slate-900 text-xs leading-snug">{{ $rawKet }}</div>
                                @endif
                            </td>

                            <!-- Dicatat Oleh (Petugas) -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600 shrink-0">
                                        {{ strtoupper(substr($trx->recorder_name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-semibold text-slate-800 truncate max-w-[140px]">{{ $trx->recorder_name }}</div>
                                        <div class="text-[10px] text-slate-400 truncate max-w-[140px]">{{ $trx->user?->role_label ?? 'Pengurus RT/RW' }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Bukti Fisik Nota -->
                            <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                @if($trx->bukti_foto)
                                    <button type="button" 
                                            wire:click="viewPhoto('{{ $trx->bukti_foto }}', '{{ addslashes($trx->keterangan) }}')"
                                            class="h-7 px-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 inline-flex items-center space-x-1.5 transition text-[11px] font-bold cursor-pointer"
                                            title="Lihat lampiran bukti kuitansi/nota">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span>Lihat Nota</span>
                                    </button>
                                @else
                                    <span class="text-slate-300 text-xs font-medium">-</span>
                                @endif
                            </td>

                            <!-- Kolom Masuk -->
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-700 whitespace-nowrap">
                                @if($trx->jenis_transaksi === 'masuk')
                                    Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-200">-</span>
                                @endif
                            </td>

                            <!-- Kolom Keluar -->
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-rose-700 whitespace-nowrap">
                                @if($trx->jenis_transaksi === 'keluar')
                                    Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-200">-</span>
                                @endif
                            </td>

                            <!-- Tombol Aksi Hapus -->
                            @if($user && $user->canManageFinance())
                                <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                    <button type="button" 
                                            wire:click="confirmDelete({{ $trx->id }})"
                                            class="h-7 w-7 rounded-lg hover:bg-rose-50 text-slate-300 hover:text-rose-600 inline-flex items-center justify-center transition cursor-pointer"
                                            title="Hapus transaksi">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                Belum ada transaksi kas yang dicatat untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-3 border-t border-slate-200 bg-slate-50/50">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- MODAL 1: CATAT TRANSAKSI KAS BARU - SPACIOUS & CLEAN -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 overflow-y-auto">
            <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-7 shadow-2xl border border-slate-200 space-y-5 animate-in fade-in zoom-in-95 duration-200">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
                    <div class="flex items-center space-x-3">
                        <div class="w-11 h-11 rounded-2xl {{ $form_jenis === 'masuk' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} flex items-center justify-center font-bold shadow-xs shrink-0">
                            @if($form_jenis === 'masuk')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900">
                                {{ $form_jenis === 'masuk' ? 'Catat Kas Masuk (Pemasukan)' : 'Catat Kas Keluar (Pengeluaran)' }}
                            </h3>
                            <p class="text-xs text-slate-500 font-medium">
                                Target buku kas: <strong class="text-slate-800">{{ $form_rt_id ? 'Kas Operasional RT' : 'Kas Induk RW 01' }}</strong>
                            </p>
                        </div>
                    </div>
                    <button type="button" wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="saveTransaction" class="space-y-4">
                    
                    <!-- Wilayah RT Target (Jika pengurus RW) -->
                    @if($user && $user->isRwLevel())
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Buku Kas Tujuan</label>
                            <select wire:model.live="form_rt_id" class="w-full h-10 px-3 text-xs rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none bg-white font-semibold text-slate-800 cursor-pointer transition">
                                <option value="">Kas Induk RW 01</option>
                                @foreach($rts as $rt)
                                    <option value="{{ $rt->id }}">Kas Operasional RT {{ $rt->nomor_rt }} ({{ $rt->nama_ketua }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- Tanggal -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Transaksi</label>
                            <input type="date" wire:model="form_tanggal" class="w-full h-10 px-3 text-xs rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none font-medium text-slate-800 transition">
                            @error('form_tanggal') <span class="text-[11px] text-rose-600 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nominal -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nominal (Rp)</label>
                            <div x-data="{
                                raw: @entangle('form_nominal'),
                                display: '',
                                format(val) {
                                    if (val === null || val === undefined || val === '') return '';
                                    let num = val.toString().replace(/[^0-9]/g, '');
                                    return num ? new Intl.NumberFormat('id-ID').format(num) : '';
                                },
                                update(e) {
                                    let clean = e.target.value.replace(/[^0-9]/g, '');
                                    this.raw = clean ? parseInt(clean, 10) : null;
                                    this.display = this.format(this.raw);
                                },
                                addAmount(amount) {
                                    this.raw = (this.raw ? parseInt(this.raw, 10) : 0) + amount;
                                    this.display = this.format(this.raw);
                                },
                                init() {
                                    this.display = this.format(this.raw);
                                    this.$watch('raw', v => { this.display = this.format(v); });
                                }
                            }" class="space-y-1.5">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400 pointer-events-none select-none">Rp</span>
                                    <input type="text" 
                                           inputmode="numeric" 
                                           x-model="display" 
                                           @input="update($event)" 
                                           placeholder="150.000" 
                                           class="w-full h-10 pl-10 pr-3.5 text-xs rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none font-mono font-bold text-slate-900 transition">
                                </div>
                                <div class="flex items-center space-x-1.5">
                                    <button type="button" @click="addAmount(50000)" class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-600 text-[10px] font-bold font-mono transition">
                                        +50rb
                                    </button>
                                    <button type="button" @click="addAmount(100000)" class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-600 text-[10px] font-bold font-mono transition">
                                        +100rb
                                    </button>
                                    <button type="button" @click="addAmount(500000)" class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-600 text-[10px] font-bold font-mono transition">
                                        +500rb
                                    </button>
                                    <button type="button" @click="raw = null; display = ''" class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-rose-100 text-slate-500 hover:text-rose-700 text-[10px] font-bold transition">
                                        Reset
                                    </button>
                                </div>
                            </div>
                            @error('form_nominal') <span class="text-[11px] text-rose-600 font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Transaksi</label>
                        <select wire:model="form_kategori" class="w-full h-10 px-3 text-xs rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none bg-white font-semibold text-slate-800 cursor-pointer transition">
                            @if($form_jenis === 'masuk')
                                <option value="donasi_warga">Donasi / Sumbangan Warga</option>
                                <option value="setoran_rt">Setoran Wajib dari RT</option>
                                <option value="bantuan_rw">Dana Bantuan RW</option>
                                <option value="bantuan_pemerintah">Bantuan Kelurahan / Pemda</option>
                                <option value="donasi_luar">Donasi Pihak Luar</option>
                                <option value="lainnya">Pemasukan Lainnya</option>
                            @else
                                <option value="gaji_keamanan">Gaji Petugas Ronda / Keamanan</option>
                                <option value="angkut_sampah">Retribusi Petugas Angkut Sampah</option>
                                <option value="lampu_jalan">Pemeliharaan Lampu Jalan / Listrik</option>
                                <option value="setor_ke_rw">Setor Kas Wajib Bulanan ke RW</option>
                                <option value="kas_sosial">Santunan Kematian / Kas Sosial Warga</option>
                                <option value="acara_lingkungan">Kegiatan Lingkungan (PHBI / 17-an / Kerja Bakti)</option>
                                <option value="operasional_rt">Operasional RT (ATK / Konsumsi Rapat)</option>
                                <option value="operasional_rw">Operasional RW (ATK / Balai Warga)</option>
                                <option value="lainnya">Pengeluaran Lainnya</option>
                            @endif
                        </select>
                        @error('form_kategori') <span class="text-[11px] text-rose-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Uraian Keterangan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Uraian / Keterangan Transaksi</label>
                        <textarea wire:model="form_keterangan" rows="2" placeholder="Tuliskan keterangan detail penggunaan uang kas..." class="w-full p-3 text-xs rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none text-slate-800 transition"></textarea>
                        @error('form_keterangan') <span class="text-[11px] text-rose-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Upload Foto Nota / Kuitansi -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Foto Bukti Nota / Kuitansi Fisik (Opsional)</label>
                        <input type="file" wire:model="form_bukti_foto" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <span class="text-[10px] text-slate-400 block mt-1">Dapat difoto langsung menggunakan kamera HP (maks. 3 MB).</span>
                        @error('form_bukti_foto') <span class="text-[11px] text-rose-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-3 border-t border-slate-100 pt-3.5">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="h-10 px-4 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                            Batal
                        </button>
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="h-10 px-5 rounded-xl {{ $form_jenis === 'masuk' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700' }} text-white text-xs font-bold transition shadow-xs cursor-pointer">
                            Simpan Transaksi Kas
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif

    <!-- MODAL 2: LIHAT BUKTI FOTO KUITANSI -->
    @if($showPhotoModal && $photoUrl)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-xs p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-4 shadow-2xl space-y-3 animate-in fade-in duration-150">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h3 class="text-xs font-bold text-slate-900 truncate max-w-sm">{{ $photoTitle }}</h3>
                    <button type="button" wire:click="$set('showPhotoModal', false)" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="max-h-[70vh] overflow-auto flex items-center justify-center bg-slate-100 rounded-xl p-2">
                    <img src="{{ $photoUrl }}" alt="Bukti Kuitansi" class="max-h-[65vh] w-auto rounded-lg shadow-sm">
                </div>
                <div class="text-right pt-1">
                    <button type="button" wire:click="$set('showPhotoModal', false)" class="h-8 px-4 rounded-lg bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 3: KONFIRMASI HAPUS TRANSAKSI BUKU KAS -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 animate-in fade-in duration-150">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4 animate-in zoom-in-95 duration-150">
                <div class="flex items-center space-x-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 shadow-sm shadow-rose-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900">Hapus Transaksi Buku Kas?</h3>
                        <p class="text-xs text-slate-500">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                    <div class="text-xs font-bold text-slate-800">{{ $deletingTransactionDesc }}</div>
                    <div class="text-xs font-mono font-black text-rose-600">{{ $deletingTransactionNominal }}</div>
                </div>

                <p class="text-xs text-slate-500 leading-relaxed">
                    Data transaksi ini akan dihapus dari buku kas. Jika transaksi ini terkait pelunasan iuran warga, status lunas pada matriks iuran juga akan otomatis dibatalkan.
                </p>

                <div class="flex items-center justify-end space-x-2.5 pt-2 border-t border-slate-100">
                    <button type="button" 
                            wire:click="cancelDelete" 
                            class="h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                        Batal
                    </button>
                    <button type="button" 
                            wire:click="executeDelete" 
                            class="h-10 px-5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                        Ya, Hapus Transaksi
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
