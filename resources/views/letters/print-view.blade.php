<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Surat Pengantar - {{ $letter->nomor_surat }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .a4-page {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
            }
        }

        .a4-page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm 25mm 20mm 25mm;
            margin: 20px auto;
            background: #fff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            box-sizing: border-box;
            position: relative;
            font-family: 'Times New Roman', Times, serif;
            color: #000;
        }

        /* Kop Surat */
        .kop-header {
            text-align: center;
            margin-bottom: 12px;
        }

        .kop-header .line-1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .kop-header .line-2 {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }

        .kop-header .line-3 {
            font-size: 10.5pt;
            font-style: italic;
            color: #222;
        }

        .kop-divider {
            border: 0;
            border-top: 3px double #000;
            margin: 8px 0 20px 0;
        }

        /* Judul Surat */
        .judul-surat {
            text-align: center;
            margin-bottom: 22px;
        }

        .judul-surat h1 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin: 0 0 4px 0;
            letter-spacing: 1px;
        }

        .judul-surat p {
            font-size: 11pt;
            margin: 0;
        }

        /* Konten Surat */
        .paragraf {
            text-align: justify;
            text-indent: 36px;
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .paragraf-no-indent {
            text-align: justify;
            margin-bottom: 10px;
            line-height: 1.5;
        }

        /* Tabel Biodata */
        .table-biodata {
            width: 100%;
            margin: 12px 0 16px 20px;
            border-collapse: collapse;
        }

        .table-biodata td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 11.5pt;
        }

        .table-biodata td.label {
            width: 175px;
        }

        .table-biodata td.separator {
            width: 18px;
            text-align: center;
        }

        .table-biodata td.value {
            font-weight: normal;
        }

        /* Keperluan Box */
        .keperluan-highlight {
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 4px;
            padding: 8px 14px;
            background-color: #f8fafc;
            border-left: 4px solid #000;
            font-size: 11.5pt;
        }

        /* Tanda Tangan */
        .ttd-wrapper {
            margin-top: 40px;
            width: 100%;
            page-break-inside: avoid;
        }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ttd-table td {
            vertical-align: top;
            width: 50%;
            font-size: 11.5pt;
        }

        .ttd-space {
            height: 10px;
        }

        .tte-box {
            margin: 8px auto;
            text-align: center;
        }

        .tte-qr {
            width: 70px;
            height: 70px;
            display: block;
            margin: 0 auto;
        }

        .tte-caption {
            font-size: 7pt;
            color: #475569;
            margin-top: 4px;
            font-weight: bold;
            line-height: 1.15;
        }

        .tte-footer-note {
            margin-top: 25px;
            font-size: 8pt;
            color: #64748b;
            border-top: 1px dashed #cbd5e1;
            padding-top: 6px;
            font-style: italic;
            line-height: 1.35;
        }

        .watermark-batal {
            position: absolute;
            top: 40%;
            left: 15%;
            transform: rotate(-30deg);
            font-size: 54pt;
            font-weight: bold;
            color: rgba(220, 38, 38, 0.25);
            border: 4px dashed rgba(220, 38, 38, 0.35);
            padding: 10px 30px;
            text-transform: uppercase;
            letter-spacing: 5px;
            pointer-events: none;
        }

        .watermark-menunggu {
            position: absolute;
            top: 40%;
            left: 8%;
            transform: rotate(-30deg);
            font-size: 42pt;
            font-weight: bold;
            color: rgba(217, 119, 6, 0.25);
            border: 4px dashed rgba(217, 119, 6, 0.35);
            padding: 10px 25px;
            text-transform: uppercase;
            letter-spacing: 3px;
            pointer-events: none;
            text-align: center;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">
    <!-- Utility Top Bar (Non-Printable) -->
    <header class="no-print sticky top-0 z-50 bg-white/95 backdrop-blur-xs border-b border-slate-200 shadow-2xs px-4 sm:px-6 py-2.5">
        <div class="max-w-6xl mx-auto flex flex-wrap items-center justify-between gap-3">
            <!-- Sisi Kiri: Kembali & Identitas Surat -->
            <div class="flex items-center space-x-2.5 min-w-0">
                <a href="{{ ($isPublic ?? false) || !auth()->check() ? route('letters.verify', $letter->verification_code) : route('letters.index') }}" 
                   class="h-9 px-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center space-x-1.5 transition shadow-2xs whitespace-nowrap shrink-0">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali</span>
                </a>
                
                <span class="text-slate-300">|</span>

                <div class="inline-flex items-center space-x-1.5 px-2.5 py-1.5 rounded-lg bg-slate-100 border border-slate-200/80 text-xs font-bold text-slate-800 whitespace-nowrap">
                    <span class="text-slate-400 font-semibold text-[11px]">No:</span>
                    <span class="font-mono text-slate-900 tracking-tight">{{ $letter->nomor_surat }}</span>
                </div>

                @if($letter->status === 'menunggu_rt')
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                        Pending (TTE RT)
                    </span>
                @elseif($letter->status === 'menunggu_rw')
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                        Pending (TTE RW)
                    </span>
                @elseif($letter->status === 'ditolak_rt')
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                        Ditolak Ketua RT
                    </span>
                @elseif($letter->status === 'ditolak_rw' || $letter->status === 'ditolak')
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                        Ditolak Ketua RW
                    </span>
                @elseif($letter->status === 'dibatalkan')
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                        Dibatalkan
                    </span>
                @endif
            </div>

            <!-- Sisi Kanan: Action Buttons Terpadu & Rapi -->
            <div class="flex items-center flex-wrap gap-2">
                @if(!($isPublic ?? false) && auth()->check() && $letter->wa_share_link && $letter->status === 'terbit')
                    <a href="{{ $letter->wa_share_link }}" 
                       target="_blank"
                       class="h-9 px-3 rounded-lg bg-[#25D366]/10 hover:bg-[#25D366]/20 border border-[#25D366]/30 text-emerald-800 text-xs font-semibold inline-flex items-center space-x-1.5 transition shadow-2xs whitespace-nowrap"
                       title="Kirim ke WhatsApp Warga">
                        <svg class="w-3.5 h-3.5 fill-[#25D366]" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        <span>Kirim WhatsApp</span>
                    </a>
                @endif

                <a href="{{ $letter->verification_url }}" 
                   target="_blank"
                   class="h-9 px-3 rounded-lg bg-sky-50 hover:bg-sky-100 border border-sky-200 text-sky-800 text-xs font-semibold inline-flex items-center space-x-1.5 transition shadow-2xs whitespace-nowrap"
                   title="Buka Laman Hasil Scan QR Verifikasi">
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Verifikasi TTE</span>
                </a>

                @if(($isPublic ?? false) || !auth()->check())
                    <a href="{{ route('letters.public.download', $letter->verification_code) }}" 
                       class="h-9 px-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center space-x-1.5 transition shadow-2xs whitespace-nowrap"
                       title="Unduh File PDF">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Unduh PDF</span>
                    </a>
                @else
                    <a href="{{ route('letters.download', $letter) }}" 
                       class="h-9 px-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center space-x-1.5 transition shadow-2xs whitespace-nowrap"
                       title="Unduh File PDF">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Unduh PDF</span>
                    </a>
                @endif

                <button onclick="window.print()" 
                        class="h-9 px-3.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold inline-flex items-center space-x-1.5 transition shadow-2xs whitespace-nowrap"
                        title="Cetak langsung menggunakan printer (Ctrl + P)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Surat</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Dokumen Cetak Ukuran A4 -->
    <main class="py-6 px-4">
        <div class="a4-page">
            @if($letter->status === 'dibatalkan')
                <div class="watermark-batal">DIBATALKAN</div>
            @elseif($letter->status === 'ditolak_rt')
                <div class="watermark-batal">DITOLAK RT</div>
            @elseif($letter->status === 'ditolak_rw' || $letter->status === 'ditolak')
                <div class="watermark-batal">DITOLAK RW</div>
            @elseif($letter->status === 'menunggu_rt')
                <div class="watermark-menunggu">PENDING TTE RT</div>
            @elseif($letter->status === 'menunggu_rw')
                <div class="watermark-menunggu">PENDING TTE RW</div>
            @endif

            <!-- Kop Surat -->
            <div class="kop-header">
                <div class="line-1">RUKUN WARGA 01</div>
                <div class="line-2">RUKUN TETANGGA {{ $letter->rt?->nomor_rt ? str_pad((string)((int)$letter->rt->nomor_rt), 2, '0', STR_PAD_LEFT) : '01' }}</div>
                <div class="line-3">Kelurahan Slawi Kulon, Kecamatan Slawi, Kab. Tegal</div>
            </div>
            <div class="kop-divider"></div>

            <!-- Judul Dokumen -->
            <div class="judul-surat">
                <h1>SURAT PENGANTAR</h1>
                <p>Nomor: <strong>{{ $letter->nomor_surat }}</strong></p>
            </div>

            <!-- Paragraf Pembuka -->
            <div class="paragraf">
                Yang bertanda tangan di bawah ini Ketua Rukun Tetangga {{ $letter->rt?->nomor_rt ? str_pad((string)((int)$letter->rt->nomor_rt), 2, '0', STR_PAD_LEFT) : '01' }} Rukun Warga 01 Kelurahan Slawi Kulon Kecamatan Slawi Kab. Tegal, dengan ini menerangkan bahwa:
            </div>

            <!-- Tabel Biodata Pemohon -->
            <table class="table-biodata">
                <tr>
                    <td class="label">Nama Lengkap</td>
                    <td class="separator">:</td>
                    <td class="value"><strong>{{ strtoupper($letter->snapshot_nama) }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Nomor NIK</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $letter->snapshot_nik }}</td>
                </tr>
                <tr>
                    <td class="label">Nomor Kartu Keluarga</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $letter->snapshot_no_kk }}</td>
                </tr>
                <tr>
                    <td class="label">Tempat / Tgl Lahir</td>
                    <td class="separator">:</td>
                    <td class="value">{{ strtoupper($letter->snapshot_ttl) }}</td>
                </tr>
                <tr>
                    <td class="label">Jenis Kelamin</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $letter->snapshot_jenis_kelamin === 'L' ? 'LAKI-LAKI' : 'PEREMPUAN' }}</td>
                </tr>
                <tr>
                    <td class="label">Agama</td>
                    <td class="separator">:</td>
                    <td class="value">{{ strtoupper($letter->snapshot_agama) }}</td>
                </tr>
                <tr>
                    <td class="label">Pekerjaan</td>
                    <td class="separator">:</td>
                    <td class="value">{{ strtoupper($letter->snapshot_pekerjaan) }}</td>
                </tr>
                <tr>
                    <td class="label">Alamat Lengkap</td>
                    <td class="separator">:</td>
                    <td class="value">{{ strtoupper($letter->snapshot_alamat) }}</td>
                </tr>
            </table>

            <!-- Paragraf Keperluan -->
            <div class="paragraf-no-indent">
                Orang tersebut di atas adalah benar-benar warga kami yang berdomisili di lingkungan RT.{{ $letter->rt?->nomor_rt ? str_pad((string)((int)$letter->rt->nomor_rt), 2, '0', STR_PAD_LEFT) : '01' }} RW.01 dan bermaksud untuk mengurus:
            </div>

            <div class="keperluan-highlight">
                {{ $letter->jenis_keperluan }}
                @if($letter->keterangan_keperluan)
                    <div style="font-weight: normal; font-style: italic; font-size: 10.5pt; text-transform: none; margin-top: 2px;">
                        (Keperluan: {{ $letter->keterangan_keperluan }})
                    </div>
                @endif
            </div>

            <!-- Paragraf Penutup -->
            <div class="paragraf" style="margin-top: 14px;">
                Demikian surat pengantar ini kami buat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
            </div>

            <!-- Blok Tanda Tangan -->
            <div class="ttd-wrapper">
                <table class="ttd-table">
                    <tr>
                        <td style="text-align: center;">
                            Mengetahui,<br>
                            <strong>Ketua RW 01</strong>
                            @if($letter->status === 'menunggu_rt')
                                <div class="tte-box flex flex-col items-center justify-center border border-dashed border-amber-400 bg-amber-50/70 rounded-lg p-2.5 my-2 w-[140px] mx-auto text-center min-h-[70px]">
                                    <span class="text-[10.5px] text-amber-800 font-bold leading-tight">
                                        [ Menunggu Pengesahan<br>Ketua RT ]
                                    </span>
                                </div>
                            @elseif($letter->status === 'menunggu_rw')
                                <div class="tte-box flex flex-col items-center justify-center border border-dashed border-amber-400 bg-amber-50/70 rounded-lg p-2.5 my-2 w-[140px] mx-auto text-center min-h-[70px]">
                                    <span class="text-[10.5px] text-amber-800 font-bold leading-tight">
                                        [ Pending Pengesahan<br>& TTE Ketua RW ]
                                    </span>
                                </div>
                            @elseif($letter->status === 'ditolak_rt')
                                <div class="tte-box flex flex-col items-center justify-center border border-dashed border-rose-400 bg-rose-50/70 rounded-lg p-2.5 my-2 w-[140px] mx-auto text-center min-h-[70px]">
                                    <span class="text-[10.5px] text-rose-800 font-bold leading-tight">
                                        [ Dibatalkan / Ditolak<br>di Tingkat RT ]
                                    </span>
                                </div>
                            @elseif($letter->status === 'ditolak_rw' || $letter->status === 'ditolak')
                                <div class="tte-box flex flex-col items-center justify-center border border-dashed border-rose-400 bg-rose-50/70 rounded-lg p-2.5 my-2 w-[140px] mx-auto text-center min-h-[70px]">
                                    <span class="text-[10.5px] text-rose-800 font-bold leading-tight">
                                        [ Pengesahan Ditolak<br>Ketua RW ]
                                    </span>
                                </div>
                            @elseif($letter->status === 'dibatalkan')
                                <div class="tte-box flex flex-col items-center justify-center border border-dashed border-rose-400 bg-rose-50/70 rounded-lg p-2.5 my-2 w-[140px] mx-auto text-center min-h-[70px]">
                                    <span class="text-[10.5px] text-rose-800 font-bold leading-tight">
                                        [ Surat Dibatalkan<br>Tidak Berlaku ]
                                    </span>
                                </div>
                            @else
                                <div class="tte-box">
                                    <img src="data:image/svg+xml;base64,{{ $letter->qr_code_svg_base64 }}" class="tte-qr">
                                    <div class="tte-caption">Ditandatangani secara elektronik (TTE)<br>Scan QR untuk cek keabsahan</div>
                                </div>
                            @endif
                            <strong>( {{ $letter->nama_ketua_rw ?: 'AMAT MAJA' }} )</strong>
                        </td>
                        <td style="text-align: center;">
                            Slawi, {{ $letter->formatted_tanggal_surat }}<br>
                            <strong>Ketua RT {{ $letter->rt?->nomor_rt ? str_pad((string)((int)$letter->rt->nomor_rt), 2, '0', STR_PAD_LEFT) : '01' }}</strong>
                            @if($letter->status === 'menunggu_rt')
                                <div class="tte-box flex flex-col items-center justify-center border border-dashed border-amber-400 bg-amber-50/70 rounded-lg p-2.5 my-2 w-[140px] mx-auto text-center min-h-[70px]">
                                    <span class="text-[10.5px] text-amber-800 font-bold leading-tight">
                                        [ Pending Pengesahan<br>& TTE Ketua RT ]
                                    </span>
                                </div>
                            @elseif($letter->status === 'ditolak_rt')
                                <div class="tte-box flex flex-col items-center justify-center border border-dashed border-rose-400 bg-rose-50/70 rounded-lg p-2.5 my-2 w-[140px] mx-auto text-center min-h-[70px]">
                                    <span class="text-[10.5px] text-rose-800 font-bold leading-tight">
                                        [ Pengesahan Ditolak<br>Ketua RT ]
                                    </span>
                                </div>
                            @elseif($letter->status === 'dibatalkan')
                                <div class="tte-box flex flex-col items-center justify-center border border-dashed border-rose-400 bg-rose-50/70 rounded-lg p-2.5 my-2 w-[140px] mx-auto text-center min-h-[70px]">
                                    <span class="text-[10.5px] text-rose-800 font-bold leading-tight">
                                        [ Surat Dibatalkan<br>Tidak Berlaku ]
                                    </span>
                                </div>
                            @elseif($letter->status === 'ditolak_rw' || $letter->status === 'ditolak')
                                <div class="tte-box flex flex-col items-center justify-center border border-dashed border-rose-400 bg-rose-50/70 rounded-lg p-2.5 my-2 w-[140px] mx-auto text-center min-h-[70px]">
                                    <span class="text-[10.5px] text-rose-800 font-bold leading-tight">
                                        [ Pengesahan Ditolak<br>Ketua RW ]
                                    </span>
                                </div>
                            @else
                                <div class="tte-box">
                                    <img src="data:image/svg+xml;base64,{{ $letter->qr_code_svg_base64 }}" class="tte-qr">
                                    <div class="tte-caption">Ditandatangani secara elektronik (TTE)<br>Scan QR untuk cek keabsahan</div>
                                </div>
                            @endif
                            <strong>( {{ $letter->nama_ketua_rt }} )</strong>
                        </td>
                    </tr>
                </table>

                <!-- Catatan Kaki Integritas TTE -->
                <div class="tte-footer-note">
                    Catatan: Surat ini sah diterbitkan melalui Sistem Digital RW 01 Kelurahan Slawi Kulon dan menggunakan Tanda Tangan Elektronik (TTE). Keaslian dokumen dapat diverifikasi langsung melalui pemindaian QR Code di atas.
                </div>
            </div>
        </div>
    </main>

    @if(request()->query('print') == 1)
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                window.print();
            });
        </script>
    @endif
</body>
</html>
