<div class="space-y-6">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 dark:text-slate-400 mb-1">
                <span>Portal Pengelolaan Warga</span>
                <span>/</span>
                <span>Modul Keuangan</span>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200 font-bold">Buku Iuran Bulanan</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center space-x-3">
                <span>Matriks Iuran Warga</span>
                <span class="px-3 py-0.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800/60 shadow-2xs">
                    Tahun {{ $year }}
                </span>
            </h1>
            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                Pencatatan setoran iuran bulanan per unit rumah, monitoring tunggakan, dan kirim kuitansi digital WhatsApp.
            </p>
        </div>

        <div class="flex items-center space-x-3 shrink-0">
            <!-- Navigasi Tahun -->
            <div class="inline-flex items-center rounded-xl border border-slate-200 dark:border-[#1d2c4d] bg-white dark:bg-[#111b2f] shadow-xs p-1">
                <button type="button" 
                        wire:click="prevYear" 
                        class="h-8 w-8 rounded-lg flex items-center justify-center hover:bg-slate-100 dark:hover:bg-[#162441] text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition cursor-pointer" 
                        title="Tahun Sebelumnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <span class="px-3.5 text-xs font-black font-mono text-slate-800 dark:text-slate-200 tracking-wide">{{ $year }}</span>
                <button type="button" 
                        wire:click="nextYear" 
                        class="h-8 w-8 rounded-lg flex items-center justify-center hover:bg-slate-100 dark:hover:bg-[#162441] text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition cursor-pointer" 
                        title="Tahun Berikutnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <!-- Tombol Atur Tarif RT -->
            @if($user && $user->canManageFinance() && !$user->isBendaharaRw())
                <button type="button" 
                        wire:click="openSettingsModal"
                        class="h-10 px-4 rounded-xl border border-slate-200 dark:border-[#1d2c4d] bg-white dark:bg-[#111b2f] hover:bg-slate-50 dark:hover:bg-[#162441] text-slate-700 dark:text-slate-200 text-xs font-bold inline-flex items-center space-x-2 transition shadow-xs hover:border-slate-300 dark:hover:border-[#273d6b] cursor-pointer">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Tarif Iuran RT</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Banner Read-Only Mode untuk Bendahara RW -->
    @if($user && $user->isBendaharaRw())
        <div class="bg-gradient-to-r from-blue-50 via-sky-50 to-blue-50 dark:from-blue-950/30 dark:via-sky-950/20 dark:to-blue-950/30 border border-blue-200/90 dark:border-blue-800/50 rounded-2xl p-4 sm:p-5 flex items-start sm:items-center space-x-3.5 shadow-xs animate-in fade-in duration-200">
            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-blue-600/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1">
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-black text-blue-900 dark:text-blue-200 tracking-tight">Mode Pemantauan & Rekapitulasi (Read-Only)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-900/50 dark:text-blue-300 dark:border-blue-700/60">
                        Bendahara RW
                    </span>
                </div>
                <p class="text-xs text-blue-700/90 dark:text-blue-300/80 mt-0.5 leading-relaxed">
                    Anda memiliki akses monitoring penuh terhadap matriks kelunasan iuran warga di seluruh wilayah RT 01–04. Penerimaan fisik iuran dan pencatatan buku kas warga dikelola langsung oleh Bendahara RT masing-masing.
                </p>
            </div>
        </div>
    @endif

    <!-- Banner Kuitansi Pembayaran Terakhir (Instan WhatsApp) -->
    @if($lastPaymentReceipt)
        <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-50 dark:from-emerald-950/30 dark:via-teal-950/20 dark:to-emerald-950/30 border border-emerald-300/80 dark:border-emerald-800/50 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-sm animate-in fade-in duration-200">
            <div class="flex items-start sm:items-center space-x-3.5">
                <div class="w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-600/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-800 dark:text-emerald-400 block">
                        Pembayaran Iuran Berhasil Disimpan
                    </span>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mt-0.5">
                        {{ $lastPaymentReceipt['house_number'] }} • {{ $lastPaymentReceipt['head_name'] }} ({{ $lastPaymentReceipt['periode'] }})
                    </h3>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">
                        Total diterima: <strong class="text-emerald-700 dark:text-emerald-400 font-mono">{{ $lastPaymentReceipt['nominal'] }}</strong> ({{ $lastPaymentReceipt['metode'] }}) pada {{ $lastPaymentReceipt['tanggal'] }}.
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-2.5 shrink-0">
                @if($lastPaymentReceipt['wa_link'])
                    <a href="{{ $lastPaymentReceipt['wa_link'] }}" 
                       target="_blank" 
                       class="h-10 px-4 rounded-xl bg-[#25D366] hover:bg-[#20ba5a] text-white text-xs font-bold inline-flex items-center space-x-2 transition shadow-sm hover:shadow-md cursor-pointer">
                        <svg class="w-4 h-4 fill-white" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        <span>Kirim Kuitansi WhatsApp</span>
                    </a>
                @endif
                <button type="button" 
                        wire:click="$set('lastPaymentReceipt', null)" 
                        class="h-10 px-4 rounded-xl bg-white dark:bg-[#111b2f] border border-slate-200 dark:border-[#1d2c4d] hover:bg-slate-50 dark:hover:bg-[#162441] text-slate-700 dark:text-slate-200 text-xs font-bold transition shadow-xs cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    @endif

    <!-- Kartu Statistik Ringkas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-[#111b2f] rounded-2xl p-5 border border-slate-200 dark:border-[#1d2c4d] shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Total Iuran Terkumpul ({{ $year }})</span>
                <div class="text-2xl font-black text-emerald-700 dark:text-emerald-400 mt-1 font-mono">
                    Rp {{ number_format($totalTerkumpulTahunIni, 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 block">Akumulasi uang kas masuk dari iuran</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-transparent dark:border-emerald-800/40 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-[#111b2f] rounded-2xl p-5 border border-slate-200 dark:border-[#1d2c4d] shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Kelunasan Bulan Ini (Bulan {{ date('n') }})</span>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">
                    {{ $totalLunasBulanIni }} <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">/ {{ $totalRumah }} Rumah</span>
                </div>
                <span class="text-[11px] font-bold {{ $persenBulanIni >= 75 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }} mt-0.5 block">
                    {{ $persenBulanIni }}% unit rumah telah melunasi
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 border border-transparent dark:border-sky-800/40 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-[#111b2f] rounded-2xl p-5 border border-slate-200 dark:border-[#1d2c4d] shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Total Rumah Terdaftar</span>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">
                    {{ $totalRumah }} <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">Unit</span>
                </div>
                <span class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 block">Objek wajib iuran lingkungan</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 border border-transparent dark:border-purple-800/40 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
        </div>
    </div>

    <!-- Filter & Toolbar Bar -->
    <div class="bg-white dark:bg-[#111b2f] rounded-2xl p-4 border border-slate-200 dark:border-[#1d2c4d] shadow-xs space-y-3">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <!-- Pencarian -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Cari nomor rumah atau nama kepala keluarga..." 
                       class="w-full h-10 pl-10 pr-3.5 text-xs rounded-xl border border-slate-200 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
            </div>

            <!-- Filter Dropdowns -->
            <div class="flex flex-wrap items-center gap-2.5">
                @if($user && $user->isRwLevel())
                    <select wire:model.live="selectedRtId" 
                            class="h-10 px-3 text-xs font-semibold rounded-xl border border-slate-200 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none cursor-pointer shadow-2xs">
                        <option value="">Semua Wilayah RT</option>
                        @foreach($rts as $rt)
                            <option value="{{ $rt->id }}">RT {{ $rt->nomor_rt }} ({{ $rt->nama_ketua }})</option>
                        @endforeach
                    </select>
                @endif

                <select wire:model.live="kategoriTarif" 
                        class="h-10 px-3 text-xs font-semibold rounded-xl border border-slate-200 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] text-slate-700 dark:text-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none cursor-pointer shadow-2xs">
                    <option value="">Semua Kategori Tarif</option>
                    <option value="standar">Standar (Hunian)</option>
                    <option value="usaha">Tempat Usaha / Toko</option>
                    <option value="kosong">Rumah Kosong</option>
                </select>

                <!-- Petunjuk Warna -->
                <div class="hidden lg:flex items-center space-x-3.5 text-[11px] font-bold text-slate-600 dark:text-slate-400 pl-3 border-l border-slate-200 dark:border-[#1d2c4d]">
                    <div class="flex items-center space-x-1.5">
                        <span class="w-3.5 h-3.5 rounded-md bg-emerald-600 dark:bg-emerald-500 inline-block shadow-2xs"></span>
                        <span>Lunas</span>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <span class="w-3.5 h-3.5 rounded-md bg-slate-100 dark:bg-[#0c1427] border border-slate-300 dark:border-[#1d2c4d] inline-block shadow-2xs"></span>
                        <span>Belum Bayar</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Matriks Grid 12 Bulan -->
    <div class="bg-white dark:bg-[#111b2f] rounded-2xl border border-slate-200 dark:border-[#1d2c4d] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/90 dark:bg-[#0c1427] border-b border-slate-200 dark:border-[#1d2c4d] text-[11px] font-black text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-4 sticky left-0 bg-slate-50 dark:bg-[#0c1427] z-20 w-64 min-w-[240px] shadow-[4px_0_12px_-2px_rgba(0,0,0,0.08)] dark:shadow-[4px_0_12px_-2px_rgba(0,0,0,0.45)] border-r border-slate-200/80 dark:border-[#1d2c4d]">
                            Unit Rumah
                        </th>
                        <th class="py-3.5 px-2.5 w-16 text-center">RT</th>
                        <th class="py-3.5 px-3 text-center w-28">Tarif</th>
                        @for($m = 1; $m <= 12; $m++)
                            @php
                                $isCurrentMonth = ($m === (int)date('n'));
                            @endphp
                            <th class="py-3 px-1.5 text-center min-w-[64px] font-mono font-black {{ $isCurrentMonth ? 'matrix-current-month bg-emerald-50/90 text-emerald-900 border-x border-emerald-300/80 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60' : 'text-slate-600 dark:text-slate-400' }}">
                                <div class="flex flex-col items-center">
                                    <span>{{ substr(\App\Models\HouseDue::getMonthName($m), 0, 3) }}</span>
                                    @if($isCurrentMonth)
                                        <span class="text-[8px] font-black uppercase px-1.5 py-0.5 rounded-full bg-emerald-600 text-white dark:bg-emerald-500/20 dark:text-emerald-300 dark:border dark:border-emerald-500/40 tracking-wider leading-none mt-0.5">Ini</span>
                                    @endif
                                </div>
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#1d2c4d]/60">
                    @forelse($houses as $house)
                        @php
                            $houseDues = $duesRecords->get($house->id) ?? collect();
                            $feeNominal = (float) ($feeSettings->get($house->rt_id)?->get($house->kategori_tarif)?->nominal ?? match($house->kategori_tarif) {
                                'usaha' => 100000,
                                'kosong' => 20000,
                                default => 50000,
                            });
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-[#162441]/70 transition group">
                            <!-- Kolom Rumah Sticky -->
                            <td class="py-3 px-4 sticky left-0 bg-white dark:bg-[#111b2f] group-hover:bg-slate-50/90 dark:group-hover:bg-[#162441] z-10 shadow-[4px_0_12px_-2px_rgba(0,0,0,0.08)] dark:shadow-[4px_0_12px_-2px_rgba(0,0,0,0.45)] border-r border-slate-200/80 dark:border-[#1d2c4d]">
                                <div class="font-black text-slate-900 dark:text-slate-100 text-xs sm:text-sm tracking-tight leading-tight">
                                    {{ $house->nomor_rumah }}
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate max-w-[200px] mt-0.5">
                                    {{ $house->headOfFamily?->nama_lengkap ?? 'Tanpa Penghuni' }}
                                </div>
                            </td>

                            <!-- Wilayah RT -->
                            <td class="py-3 px-2 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-[#172440] text-slate-700 dark:text-slate-300 border border-transparent dark:border-[#273d6b]/50">
                                    RT {{ $house->rt ? str_pad((string)((int)$house->rt->nomor_rt), 2, '0', STR_PAD_LEFT) : '01' }}
                                </span>
                            </td>

                            <!-- Label Kategori -->
                            <td class="py-3 px-2 text-center">
                                @php
                                    $badgeColor = match($house->kategori_tarif) {
                                        'usaha' => 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-700/60',
                                        'kosong' => 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-800/60 dark:text-slate-400 dark:border-slate-700/60',
                                        default => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-700/60',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $badgeColor }} inline-block" title="Tarif: Rp {{ number_format($feeNominal, 0, ',', '.') }}/bulan">
                                    {{ ucfirst($house->kategori_tarif) }}
                                </span>
                            </td>

                            <!-- 12 Kolom Bulan -->
                            @for($m = 1; $m <= 12; $m++)
                                @php
                                    $due = $houseDues->get($m);
                                    $isPaid = $due && $due->status === 'lunas';
                                    $isCurrentMonth = ($m === (int)date('n'));
                                @endphp
                                <td class="py-1.5 px-1 text-center align-middle {{ $isCurrentMonth ? 'matrix-current-month bg-emerald-50/30 border-x border-emerald-100/60 dark:bg-emerald-950/15 dark:border-emerald-800/40' : '' }}">
                                    @if($isPaid)
                                        <!-- Tombol LUNAS: Hijau Segar & Ikon Jelas -->
                                        <button type="button" 
                                                wire:click="openDetailModal({{ $due->id }})"
                                                class="dues-cell-paid w-full h-9 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold flex items-center justify-center transition-all duration-150 shadow-xs hover:shadow hover:scale-105 active:scale-95 cursor-pointer"
                                                title="LUNAS: Rp {{ number_format($due->nominal, 0, ',', '.') }} pada {{ $due->tanggal_bayar?->format('d/m/Y') }} ({{ $due->metode_label }}). Klik untuk detail.">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    @else
                                        <!-- Tombol BELUM LUNAS -->
                                        @if($user && $user->canManageFinance() && !$user->isBendaharaRw())
                                            <button type="button" 
                                                    wire:click="openPayModal({{ $house->id }}, {{ $m }})"
                                                    class="dues-cell-unpaid w-full h-9 rounded-lg text-[11px] font-bold font-mono flex items-center justify-center transition-all duration-150 shadow-2xs hover:shadow-xs hover:scale-105 active:scale-95 cursor-pointer"
                                                    title="Belum bayar. Klik untuk lunasi bulan {{ \App\Models\HouseDue::getMonthName($m) }}.">
                                                <span>{{ number_format($feeNominal / 1000, 0) }}k</span>
                                            </button>
                                        @elseif($user && $user->isBendaharaRw())
                                            <span class="dues-cell-unpaid w-full h-9 rounded-lg text-[11px] font-bold font-mono flex items-center justify-center cursor-default select-none opacity-75"
                                                  title="Belum bayar (Mode Pemantauan Bendahara RW - Dikelola oleh Bendahara RT)">
                                                {{ number_format($feeNominal / 1000, 0) }}k
                                            </span>
                                        @else
                                            <span class="dues-cell-unpaid w-full h-9 rounded-lg text-[11px] flex items-center justify-center opacity-60">
                                                -
                                            </span>
                                        @endif
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                Tidak ada data unit rumah yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-3.5 border-t border-slate-200 dark:border-[#1d2c4d] bg-slate-50/60 dark:bg-[#0c1427]/80">
            {{ $houses->links() }}
        </div>
    </div>

    <!-- MODAL 1: PEMBAYARAN CEPAT (MULTI-BULAN) - BESPOKE & CLEAN -->
    @if($showPayModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-xs p-4 sm:p-6 overflow-y-auto">
            <div class="bg-white dark:bg-[#111b2f] rounded-2xl max-w-3xl w-full shadow-2xl border border-slate-200/80 dark:border-[#1d2c4d] overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                
                <!-- Modal Header: Minimalist & Crisp -->
                <div class="px-6 py-4 border-b border-slate-100 dark:border-[#1d2c4d] flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-2">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Penerimaan Iuran Warga</h3>
                            <span class="px-2 py-0.5 text-[11px] font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-[#172440] rounded-md">
                                RT {{ $payRtNomor }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Unit <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $payHouseNumber }}</span> 
                            <span class="mx-1 text-slate-300 dark:text-slate-600">•</span>
                            Penanggung jawab: <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $payHeadName }}</span>
                        </p>
                    </div>
                    <button type="button" 
                            wire:click="$set('showPayModal', false)" 
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-[#162441] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body: 2 Columns Layout -->
                <div class="p-6 grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                    
                    <!-- Left Column: Periode Pembayaran (Col 7) -->
                    <div class="md:col-span-7 space-y-3">
                        <div class="flex items-center justify-between pb-1">
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white block">Pilih Bulan (Tahun {{ $year }})</span>
                                <span class="text-[11px] text-slate-400 dark:text-slate-500">Tarif Rp {{ number_format($payNominalPerMonth, 0, ',', '.') }} / bulan</span>
                            </div>
                            <div class="flex items-center space-x-2 text-xs">
                                <button type="button" 
                                        wire:click="selectAllUnpaidMonths"
                                        class="font-semibold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 hover:underline transition cursor-pointer">
                                    Pilih Semua
                                </button>
                                <span class="text-slate-300 dark:text-slate-600">•</span>
                                <button type="button" 
                                        wire:click="clearPayMonths"
                                        class="font-medium text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 transition cursor-pointer">
                                    Reset
                                </button>
                            </div>
                        </div>

                        <!-- 12 Months Grid: 3 cols x 4 rows -->
                        <div class="grid grid-cols-3 gap-2">
                            @for($m = 1; $m <= 12; $m++)
                                @php
                                    $monthName = \App\Models\HouseDue::getMonthName($m);
                                    $isAlreadyPaid = in_array($m, $payAlreadyPaidMonths, true);
                                    $isSelected = in_array($m, $payMonths, true);
                                @endphp

                                @if($isAlreadyPaid)
                                    <!-- Sudah Lunas: Soft muted, unclickable -->
                                    <div class="h-14 px-3 py-2 rounded-xl bg-slate-50/80 dark:bg-[#0c1427]/60 border border-slate-200/60 dark:border-[#1d2c4d]/60 flex flex-col justify-between opacity-60 select-none"
                                         title="Bulan {{ $monthName }} sudah lunas">
                                        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ $monthName }}</span>
                                        <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 flex items-center space-x-1">
                                            <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            <span>Lunas</span>
                                        </span>
                                    </div>
                                @elseif($isSelected)
                                    <!-- Terpilih: High-contrast, clean & confident -->
                                    <button type="button" 
                                            wire:click="togglePayMonth({{ $m }})"
                                            class="h-14 px-3 py-2 rounded-xl bg-emerald-600 border border-emerald-500 text-white flex flex-col justify-between text-left transition shadow-xs cursor-pointer">
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-xs font-bold text-white">{{ $monthName }}</span>
                                            <svg class="w-3.5 h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <span class="text-[10px] font-medium text-emerald-100">Dipilih</span>
                                    </button>
                                @else
                                    <!-- Belum Lunas / Tersedia -->
                                    <button type="button" 
                                            wire:click="togglePayMonth({{ $m }})"
                                            class="h-14 px-3 py-2 rounded-xl bg-white dark:bg-[#0c1427] hover:bg-slate-50 dark:hover:bg-[#162441] border border-slate-200 dark:border-[#1d2c4d] hover:border-slate-300 dark:hover:border-emerald-600/50 text-slate-700 dark:text-slate-200 flex flex-col justify-between text-left transition cursor-pointer group">
                                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-200 group-hover:text-slate-900 dark:group-hover:text-white">{{ $monthName }}</span>
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">+ Bayar</span>
                                    </button>
                                @endif
                            @endfor
                        </div>
                    </div>

                    <!-- Right Column: Form Pembayaran & Kasir (Col 5) -->
                    <div class="md:col-span-5 space-y-4">
                        
                        <!-- Summary Card: Clean & Modern -->
                        <div class="bg-slate-50 dark:bg-[#0c1427] rounded-xl p-4 border border-slate-200/80 dark:border-[#1d2c4d] space-y-1">
                            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                Total Pembayaran
                            </div>
                            <div class="text-2xl font-black font-mono tracking-tight text-slate-900 dark:text-white">
                                Rp {{ number_format(count($payMonths) * $payNominalPerMonth, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                @if(count($payMonths) > 0)
                                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ count($payMonths) }} bulan dipilih</span>
                                @else
                                    <span class="text-rose-500 font-normal">Pilih minimal 1 bulan di sebelah kiri</span>
                                @endif
                            </div>
                        </div>

                        <!-- Form Input Transaksi -->
                        <div class="space-y-3">
                            <!-- Nama Pembayar -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Pembayar</label>
                                <input type="text" 
                                       wire:model="payPayerName" 
                                       placeholder="Nama yang menyetorkan"
                                       class="w-full h-9 px-3 text-xs rounded-lg border border-slate-200 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none text-slate-800 dark:text-slate-100 transition">
                            </div>

                            <!-- Tanggal Bayar -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Bayar</label>
                                <input type="date" 
                                       wire:model="payDate" 
                                       class="w-full h-9 px-3 text-xs rounded-lg border border-slate-200 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none text-slate-800 dark:text-slate-100 transition">
                            </div>

                            <!-- Metode Bayar (Segmented Pill Group) -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Metode Bayar</label>
                                <div class="grid grid-cols-3 gap-1 p-1 bg-slate-100 dark:bg-[#0c1427] border border-transparent dark:border-[#1d2c4d] rounded-lg">
                                    <button type="button" 
                                            wire:click="$set('payMethod', 'tunai')" 
                                            class="h-7.5 rounded-md text-xs font-medium transition cursor-pointer {{ $payMethod === 'tunai' ? 'bg-white dark:bg-[#172440] text-slate-900 dark:text-white font-bold shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
                                        Tunai
                                    </button>
                                    <button type="button" 
                                            wire:click="$set('payMethod', 'transfer')" 
                                            class="h-7.5 rounded-md text-xs font-medium transition cursor-pointer {{ $payMethod === 'transfer' ? 'bg-white dark:bg-[#172440] text-slate-900 dark:text-white font-bold shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
                                        Transfer
                                    </button>
                                    <button type="button" 
                                            wire:click="$set('payMethod', 'qris')" 
                                            class="h-7.5 rounded-md text-xs font-medium transition cursor-pointer {{ $payMethod === 'qris' ? 'bg-white dark:bg-[#172440] text-slate-900 dark:text-white font-bold shadow-2xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
                                        QRIS
                                    </button>
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan (Opsional)</label>
                                <input type="text" 
                                       wire:model="payNotes" 
                                       placeholder="Misal: Dititipkan ke pos satpam" 
                                       class="w-full h-9 px-3 text-xs rounded-lg border border-slate-200 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none text-slate-700 dark:text-slate-200 transition">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-2 pt-2">
                            <button type="button" 
                                    wire:click="$set('showPayModal', false)" 
                                    class="h-10 px-4 rounded-xl border border-slate-200 dark:border-[#1d2c4d] hover:bg-slate-50 dark:hover:bg-[#162441] text-slate-600 dark:text-slate-300 text-xs font-semibold transition cursor-pointer">
                                Batal
                            </button>
                            <button type="button" 
                                    wire:click="savePayment" 
                                    wire:loading.attr="disabled"
                                    @if(count($payMonths) === 0) disabled @endif
                                    class="flex-1 h-10 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-bold transition shadow-xs active:scale-[0.99] cursor-pointer inline-flex items-center justify-center space-x-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Simpan & Terbitkan Kuitansi</span>
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    @endif

    <!-- MODAL 2: DETAIL BULAN LUNAS & PEMBATALAN -->
    @if($showDetailModal && $detailDue)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-xs p-4 overflow-y-auto"
             x-data="{ confirmingVoid: false }">
            <div class="bg-white dark:bg-[#111b2f] rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200/80 dark:border-[#1d2c4d] space-y-4 animate-in fade-in zoom-in-95 duration-150">
                
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#1d2c4d] pb-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Rincian Pelunasan Iuran</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Unit {{ $detailDue->house?->nomor_rumah }} • {{ $detailDue->formatted_periode }}</p>
                    </div>
                    <button type="button" wire:click="$set('showDetailModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-[#162441] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="bg-slate-50 dark:bg-[#0c1427] rounded-xl p-3.5 space-y-2 text-xs border border-slate-200/70 dark:border-[#1d2c4d]">
                    <div class="flex justify-between items-center py-0.5">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Nominal Lunas:</span>
                        <span class="font-black text-slate-900 dark:text-white font-mono text-sm">{{ $detailDue->formatted_nominal }}</span>
                    </div>
                    <div class="flex justify-between items-center py-0.5">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Tanggal Bayar:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $detailDue->tanggal_bayar?->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-0.5">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Metode Pembayaran:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $detailDue->metode_label }}</span>
                    </div>
                    <div class="flex justify-between items-center py-0.5">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Nama Pembayar:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $detailDue->nama_pembayar ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-0.5">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Penerima Kas:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $detailDue->receivedBy?->nama ?? 'Pengurus RT' }}</span>
                    </div>
                    @if($detailDue->catatan)
                        <div class="pt-2 border-t border-slate-200 dark:border-[#1d2c4d]">
                            <span class="text-slate-400 dark:text-slate-500 block text-[10px] font-bold uppercase">Catatan:</span>
                            <span class="text-slate-700 dark:text-slate-300 italic">{{ $detailDue->catatan }}</span>
                        </div>
                    @endif
                </div>

                <!-- Alert Konfirmasi Pembatalan (Inline - Bebas Popup Browser) -->
                <div x-show="confirmingVoid" 
                     x-cloak
                     class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-xs text-rose-900 dark:text-rose-200 space-y-1 animate-in fade-in zoom-in-95 duration-100">
                    <div class="font-bold flex items-center space-x-1.5 text-rose-700 dark:text-rose-400">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Konfirmasi Batalkan Pelunasan?</span>
                    </div>
                    <p class="text-[11px] text-rose-600 dark:text-rose-300 leading-relaxed">
                        Status lunas bulan ini akan dicabut dan pencatatan pemasukan terkait di Buku Kas RT akan otomatis dihapus.
                    </p>
                </div>

                <!-- Footer Buttons -->
                <div class="pt-1">
                    <!-- State 1: Normal View -->
                    <div x-show="!confirmingVoid" class="flex items-center justify-between w-full">
                        @if($user && $user->canManageFinance() && !$user->isBendaharaRw())
                            <button type="button" 
                                    @click="confirmingVoid = true" 
                                    class="h-9 px-3 rounded-lg border border-rose-200 dark:border-rose-800/60 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-semibold transition inline-flex items-center space-x-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Batalkan Pelunasan</span>
                            </button>
                        @else
                            <div></div>
                        @endif
                        <button type="button" 
                                wire:click="$set('showDetailModal', false)" 
                                class="h-9 px-4 rounded-lg bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-bold transition cursor-pointer">
                            Tutup
                        </button>
                    </div>

                    <!-- State 2: Confirming Void View (Clean, In-App Confirmation) -->
                    <div x-show="confirmingVoid" x-cloak class="flex items-center justify-end space-x-2 w-full">
                        <button type="button" 
                                @click="confirmingVoid = false" 
                                class="h-9 px-3.5 rounded-lg border border-slate-200 dark:border-[#1d2c4d] hover:bg-slate-50 dark:hover:bg-[#162441] text-slate-600 dark:text-slate-300 text-xs font-semibold transition cursor-pointer">
                            Batal
                        </button>
                        <button type="button" 
                                wire:click="voidPayment({{ $detailDue->id }})" 
                                wire:loading.attr="disabled"
                                class="h-9 px-4 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs cursor-pointer inline-flex items-center space-x-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Ya, Hapus & Batalkan</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif

    <!-- MODAL 3: PENGATURAN BESARAN TARIF IURAN RT -->
    @if($showSettingsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 dark:bg-slate-950/70 backdrop-blur-xs p-4 overflow-y-auto">
            <div class="bg-white dark:bg-[#111b2f] rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 dark:border-[#1d2c4d] space-y-5 animate-in fade-in zoom-in-95 duration-200">
                
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#1d2c4d] pb-3.5">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Atur Besaran Tarif Iuran Bulanan</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Nominal iuran per kategori rumah di wilayah RT</p>
                    </div>
                    <button type="button" wire:click="$set('showSettingsModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-[#162441] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                @if($user && $user->isRwLevel())
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Wilayah RT</label>
                        <select wire:model.live="settingsRtId" wire:change="openSettingsModal($event.target.value)" class="w-full h-10 px-3 text-xs rounded-xl border border-slate-200 dark:border-[#1d2c4d] focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none bg-white dark:bg-[#0c1427] font-semibold text-slate-800 dark:text-slate-100 cursor-pointer">
                            @foreach($rts as $rt)
                                <option value="{{ $rt->id }}">RT {{ $rt->nomor_rt }} ({{ $rt->nama_ketua }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div x-data="{
                    format(val) {
                        if (val === null || val === undefined || val === '') return '';
                        let num = val.toString().replace(/[^0-9]/g, '');
                        return num ? new Intl.NumberFormat('id-ID').format(num) : '';
                    },
                    parse(str) {
                        let clean = (str || '').toString().replace(/[^0-9]/g, '');
                        return clean ? parseInt(clean, 10) : 0;
                    }
                }" class="space-y-3.5">

                    <!-- 1. Tarif Standar / Hunian -->
                    <div x-data="{ raw: @entangle('feeStandar'), display: '' }"
                         x-init="display = format(raw); $watch('raw', v => display = format(v))"
                         class="p-3.5 rounded-2xl border border-slate-200 dark:border-[#1d2c4d] bg-slate-50/60 dark:bg-[#0c1427]/60 hover:bg-slate-50 dark:hover:bg-[#0c1427] transition space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="w-6 h-6 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800/40 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                </span>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Rumah Hunian / Standar</span>
                            </div>
                            <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500">Warga Reguler</span>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400 dark:text-slate-500 pointer-events-none select-none">Rp</span>
                            <input type="text"
                                   inputmode="numeric"
                                   x-model="display"
                                   @input="raw = parse($event.target.value); display = format(raw)"
                                   placeholder="50.000"
                                   class="w-full h-10 pl-10 pr-3.5 text-xs rounded-xl border border-slate-200 dark:border-[#1d2c4d] bg-white dark:bg-[#111b2f] focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none font-mono font-bold text-slate-900 dark:text-white shadow-2xs transition">
                        </div>
                    </div>

                    <!-- 2. Tarif Tempat Usaha -->
                    <div x-data="{ raw: @entangle('feeUsaha'), display: '' }"
                         x-init="display = format(raw); $watch('raw', v => display = format(v))"
                         class="p-3.5 rounded-2xl border border-slate-200 dark:border-[#1d2c4d] bg-slate-50/60 dark:bg-[#0c1427]/60 hover:bg-slate-50 dark:hover:bg-[#0c1427] transition space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="w-6 h-6 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/40 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </span>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Tempat Usaha / Toko / Ruko</span>
                            </div>
                            <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500">Komersial</span>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400 dark:text-slate-500 pointer-events-none select-none">Rp</span>
                            <input type="text"
                                   inputmode="numeric"
                                   x-model="display"
                                   @input="raw = parse($event.target.value); display = format(raw)"
                                   placeholder="100.000"
                                   class="w-full h-10 pl-10 pr-3.5 text-xs rounded-xl border border-slate-200 dark:border-[#1d2c4d] bg-white dark:bg-[#111b2f] focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none font-mono font-bold text-slate-900 dark:text-white shadow-2xs transition">
                        </div>
                    </div>

                    <!-- 3. Tarif Rumah Kosong -->
                    <div x-data="{ raw: @entangle('feeKosong'), display: '' }"
                         x-init="display = format(raw); $watch('raw', v => display = format(v))"
                         class="p-3.5 rounded-2xl border border-slate-200 dark:border-[#1d2c4d] bg-slate-50/60 dark:bg-[#0c1427]/60 hover:bg-slate-50 dark:hover:bg-[#0c1427] transition space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                </span>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Rumah Kosong / Kavling</span>
                            </div>
                            <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500">Tanpa Penghuni</span>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400 dark:text-slate-500 pointer-events-none select-none">Rp</span>
                            <input type="text"
                                   inputmode="numeric"
                                   x-model="display"
                                   @input="raw = parse($event.target.value); display = format(raw)"
                                   placeholder="20.000"
                                   class="w-full h-10 pl-10 pr-3.5 text-xs rounded-xl border border-slate-200 dark:border-[#1d2c4d] bg-white dark:bg-[#111b2f] focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none font-mono font-bold text-slate-900 dark:text-white shadow-2xs transition">
                        </div>
                    </div>

                </div>

                <div class="flex items-center justify-end space-x-2.5 border-t border-slate-100 dark:border-[#1d2c4d] pt-3.5">
                    <button type="button" wire:click="$set('showSettingsModal', false)" class="h-10 px-4 rounded-xl bg-white dark:bg-[#111b2f] border border-slate-200 dark:border-[#1d2c4d] hover:bg-slate-50 dark:hover:bg-[#162441] text-slate-700 dark:text-slate-200 text-xs font-bold transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" 
                            wire:click="saveSettings" 
                            class="h-10 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                        Simpan Tarif
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
