<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Keaslian Surat Pengantar RT/RW - Digital RW</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-['Plus_Jakarta_Sans',sans-serif] text-slate-800 antialiased min-h-screen flex flex-col justify-between py-6 sm:py-12 px-4">
    
    <div class="max-w-2xl mx-auto w-full space-y-6">
        
        <!-- Header Identitas RW -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-100/80 border border-emerald-300 text-emerald-800 text-xs font-bold shadow-2xs">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Sistem Verifikasi Tanda Tangan Elektronik (TTE)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Validasi Keaslian Dokumen
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 max-w-md mx-auto">
                Pemeriksaan keabsahan Surat Pengantar resmi Rukun Warga 01 Kelurahan Slawi Kulon, Kec. Slawi, Kab. Tegal
            </p>
        </div>

        @if($letter)
            <!-- Status Card -->
            @if($letter->status === 'terbit')
                <div class="bg-emerald-600 text-white rounded-2xl p-5 shadow-lg border border-emerald-500 flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-xs flex items-center justify-center shrink-0 border border-white/20">
                        <svg class="w-7 h-7 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <span class="inline-block text-[11px] font-extrabold tracking-wider uppercase bg-white/20 px-2 py-0.5 rounded text-emerald-100">
                            Terverifikasi Sah
                        </span>
                        <h2 class="text-base sm:text-lg font-bold text-white mt-0.5">
                            Dokumen Resmi & Valid Terdaftar
                        </h2>
                        <p class="text-xs text-emerald-100 mt-0.5">
                            Surat ini sah diterbitkan oleh Pengurus RT & disahkan oleh Ketua RW 01 melalui sistem tanda tangan elektronik resmi.
                        </p>
                    </div>
                </div>
            @elseif($letter->status === 'menunggu_rt')
                <div class="bg-amber-600 text-white rounded-2xl p-5 shadow-lg border border-amber-500 flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-xs flex items-center justify-center shrink-0 border border-white/20">
                        <svg class="w-7 h-7 text-amber-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <span class="inline-block text-[11px] font-extrabold tracking-wider uppercase bg-white/20 px-2 py-0.5 rounded text-amber-100">
                            Pending RT
                        </span>
                        <h2 class="text-base sm:text-lg font-bold text-white mt-0.5">
                            Pending - Menunggu Persetujuan & TTE Ketua RT {{ $letter->rt?->nomor_rt ? str_pad((string)((int)$letter->rt->nomor_rt), 2, '0', STR_PAD_LEFT) : '01' }}
                        </h2>
                        <p class="text-xs text-amber-100 mt-0.5">
                            Surat ini dibuat oleh Sekretaris RT dan saat ini masih menunggu verifikasi pengesahan dan TTE Ketua RT.
                        </p>
                    </div>
                </div>
            @elseif($letter->status === 'menunggu_rw')
                <div class="bg-amber-600 text-white rounded-2xl p-5 shadow-lg border border-amber-500 flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-xs flex items-center justify-center shrink-0 border border-white/20">
                        <svg class="w-7 h-7 text-amber-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <span class="inline-block text-[11px] font-extrabold tracking-wider uppercase bg-white/20 px-2 py-0.5 rounded text-amber-100">
                            Pending RW
                        </span>
                        <h2 class="text-base sm:text-lg font-bold text-white mt-0.5">
                            Pending - Menunggu Persetujuan & TTE Ketua RW 01
                        </h2>
                        <p class="text-xs text-amber-100 mt-0.5">
                            Surat ini telah disetujui oleh Ketua RT {{ $letter->rt?->nomor_rt ? str_pad((string)((int)$letter->rt->nomor_rt), 2, '0', STR_PAD_LEFT) : '01' }} dan saat ini masih menunggu pengesahan TTE Ketua RW 01.
                        </p>
                    </div>
                </div>
            @elseif($letter->status === 'ditolak_rt')
                <div class="bg-rose-600 text-white rounded-2xl p-5 shadow-lg border border-rose-500 flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-xs flex items-center justify-center shrink-0 border border-white/20">
                        <svg class="w-7 h-7 text-rose-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div>
                        <span class="inline-block text-[11px] font-extrabold tracking-wider uppercase bg-white/20 px-2 py-0.5 rounded text-rose-100">
                            Pengesahan Ditolak RT
                        </span>
                        <h2 class="text-base sm:text-lg font-bold text-white mt-0.5">
                            Surat Ditolak oleh Ketua RT
                        </h2>
                        <p class="text-xs text-rose-100 mt-0.5">
                            Catatan Penolakan: {{ $letter->alasan_tolak ?: 'Dokumen tidak memenuhi syarat pengesahan RT' }}
                        </p>
                    </div>
                </div>
            @elseif(in_array($letter->status, ['ditolak_rw', 'ditolak']))
                <div class="bg-rose-600 text-white rounded-2xl p-5 shadow-lg border border-rose-500 flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-xs flex items-center justify-center shrink-0 border border-white/20">
                        <svg class="w-7 h-7 text-rose-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div>
                        <span class="inline-block text-[11px] font-extrabold tracking-wider uppercase bg-white/20 px-2 py-0.5 rounded text-rose-100">
                            Pengesahan Ditolak RW
                        </span>
                        <h2 class="text-base sm:text-lg font-bold text-white mt-0.5">
                            Surat Ditolak oleh Ketua RW 01
                        </h2>
                        <p class="text-xs text-rose-100 mt-0.5">
                            Catatan Penolakan: {{ $letter->alasan_tolak ?: 'Dokumen tidak memenuhi syarat pengesahan RW' }}
                        </p>
                    </div>
                </div>
            @else
                <div class="bg-rose-600 text-white rounded-2xl p-5 shadow-lg border border-rose-500 flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-xs flex items-center justify-center shrink-0 border border-white/20">
                        <svg class="w-7 h-7 text-rose-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                    <div>
                        <span class="inline-block text-[11px] font-extrabold tracking-wider uppercase bg-white/20 px-2 py-0.5 rounded text-rose-100">
                            Tidak Berlaku
                        </span>
                        <h2 class="text-base sm:text-lg font-bold text-white mt-0.5">
                            Surat Telah Dibatalkan
                        </h2>
                        <p class="text-xs text-rose-100 mt-0.5">
                            Alasan: {{ $letter->alasan_batal ?? 'Dibatalkan oleh pengurus' }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Rincian Surat -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-6">
                
                <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nomor Surat Resmi</span>
                        <div class="text-base sm:text-lg font-extrabold font-mono text-slate-900 tracking-tight">
                            {{ $letter->nomor_surat }}
                        </div>
                    </div>
                    <div class="sm:text-right">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tanggal Diterbitkan</span>
                        <span class="text-xs sm:text-sm font-bold text-slate-800">
                            {{ $letter->formatted_tanggal_surat }}
                        </span>
                    </div>
                </div>

                <!-- Informasi Pemohon -->
                <div class="space-y-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Data Pemohon Surat</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs bg-slate-50/70 p-4 rounded-xl border border-slate-100">
                        <div>
                            <span class="text-slate-400 font-semibold block">Nama Lengkap</span>
                            <span class="font-extrabold text-slate-900 text-sm">{{ strtoupper($letter->snapshot_nama) }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block">Nomor Induk Kependudukan (NIK)</span>
                            <span class="font-bold font-mono text-slate-800">{{ substr($letter->snapshot_nik, 0, 6) }}******{{ substr($letter->snapshot_nik, -4) }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block">Wilayah Domisili</span>
                            <span class="font-bold text-slate-800">RT {{ $letter->rt?->nomor_rt ? str_pad((string)((int)$letter->rt->nomor_rt), 2, '0', STR_PAD_LEFT) : '01' }} / RW 01</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block">Tempat / Tanggal Lahir</span>
                            <span class="font-bold text-slate-800">{{ strtoupper($letter->snapshot_ttl) }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block">Jenis Kelamin / Agama</span>
                            <span class="font-bold text-slate-800">{{ $letter->snapshot_jenis_kelamin === 'L' ? 'LAKI-LAKI' : 'PEREMPUAN' }} • {{ strtoupper($letter->snapshot_agama) }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block">Pekerjaan</span>
                            <span class="font-bold text-slate-800">{{ strtoupper($letter->snapshot_pekerjaan) }}</span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-slate-400 font-semibold block">Alamat Lengkap</span>
                            <span class="font-bold text-slate-800">{{ strtoupper($letter->snapshot_alamat) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Keperluan Surat -->
                <div class="space-y-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Tujuan / Keperluan Surat</span>
                    </h3>
                    <div class="p-4 rounded-xl bg-emerald-50/50 border border-emerald-100">
                        <div class="font-bold text-emerald-950 text-sm">
                            {{ $letter->jenis_keperluan }}
                        </div>
                        @if($letter->keterangan_keperluan)
                            <div class="text-xs text-slate-600 mt-1 italic">
                                Keterangan: {{ $letter->keterangan_keperluan }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Pejabat Penandatangan Elektronik -->
                <div class="space-y-3 pt-2">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        <span>Penandatangan Elektronik (TTE)</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-3.5 rounded-xl border border-slate-200 bg-white space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Ketua RT {{ $letter->rt?->nomor_rt ? str_pad((string)((int)$letter->rt->nomor_rt), 2, '0', STR_PAD_LEFT) : '01' }}</span>
                            <div class="font-extrabold text-slate-900">{{ $letter->nama_ketua_rt }}</div>
                            @if($letter->status === 'dibatalkan')
                                <div class="text-[11px] text-rose-700 font-semibold flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Surat Dibatalkan</span>
                                </div>
                            @elseif($letter->status === 'ditolak_rt')
                                <div class="text-[11px] text-rose-700 font-semibold flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Ditolak oleh Ketua RT</span>
                                </div>
                            @elseif(in_array($letter->status, ['ditolak_rw', 'ditolak']))
                                <div class="text-[11px] text-rose-700 font-semibold flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Pengesahan Ditolak RW</span>
                                </div>
                            @elseif($letter->status === 'menunggu_rt')
                                <div class="text-[11px] text-amber-700 font-semibold flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Pending: Menunggu Pengesahan & TTE Ketua RT</span>
                                </div>
                            @else
                                <div class="text-[11px] text-emerald-700 font-semibold flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Telah ditandatangani secara elektronik {{ $letter->signed_by_rt_at ? '(' . \Carbon\Carbon::parse($letter->signed_by_rt_at)->locale('id')->translatedFormat('d M Y H:i') . ' WIB)' : '' }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="p-3.5 rounded-xl border border-slate-200 bg-white space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Ketua RW 01</span>
                            <div class="font-extrabold text-slate-900">{{ $letter->nama_ketua_rw ?: 'AMAT MAJA' }}</div>
                            @if($letter->status === 'terbit')
                                <div class="text-[11px] text-emerald-700 font-semibold flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Telah disahkan & TTE RW {{ $letter->signed_by_rw_at ? '(' . \Carbon\Carbon::parse($letter->signed_by_rw_at)->locale('id')->translatedFormat('d M Y H:i') . ' WIB)' : '' }}</span>
                                </div>
                            @elseif($letter->status === 'menunggu_rt')
                                <div class="text-[11px] text-amber-700 font-semibold flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Menunggu Persetujuan RT Terlebih Dahulu</span>
                                </div>
                            @elseif($letter->status === 'menunggu_rw')
                                <div class="text-[11px] text-amber-700 font-semibold flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Pending: Menunggu Pengesahan & TTE Ketua RW</span>
                                </div>
                            @elseif($letter->status === 'ditolak_rt')
                                <div class="text-[11px] text-rose-700 font-semibold flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Dokumen Ditolak di Tingkat RT</span>
                                </div>
                            @elseif(in_array($letter->status, ['ditolak_rw', 'ditolak']))
                                <div class="text-[11px] text-rose-700 font-semibold flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Ditolak oleh Ketua RW</span>
                                </div>
                            @else
                                <div class="text-[11px] text-rose-700 font-semibold flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Surat Dibatalkan</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Footer Keamanan & Integritas -->
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-500 space-y-1">
                    <div class="font-bold text-slate-700 flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Informasi Keaslian Dokumen Elektronik</span>
                    </div>
                    <p>
                        Dokumen ini diterbitkan secara sah melalui Sistem Manajemen Digital RW 01 Kelurahan Slawi Kulon. Dokumen resmi tidak memerlukan tanda tangan basah manual karena telah diverifikasi menggunakan QR Code TTE yang terlacak pada sistem database resmi.
                    </p>
                    <div class="pt-1 font-mono text-[10px] text-slate-400 truncate">
                        ID Token: {{ $code }}
                    </div>
                </div>

            </div>
        @else
            <!-- Dokumen Tidak Ditemukan -->
            <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm text-center space-y-4">
                <div class="w-16 h-16 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto border border-amber-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Dokumen Tidak Ditemukan</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-md mx-auto">
                        Kode verifikasi tidak terdaftar pada basis data kami. Pastikan Anda memindai QR Code resmi dari surat pengantar yang sah.
                    </p>
                </div>
                <div class="text-[11px] font-mono text-slate-400 bg-slate-50 p-2.5 rounded-lg max-w-sm mx-auto truncate border border-slate-200">
                    Token: {{ $code }}
                </div>
            </div>
        @endif

        <!-- Footer Copyright -->
        <div class="text-center text-xs text-slate-400 pt-4">
            &copy; {{ date('Y') }} Rukun Warga 01 Kelurahan Slawi Kulon • Kec. Slawi, Kab. Tegal • Sistem Informasi Warga Digital
        </div>

    </div>

</body>
</html>
