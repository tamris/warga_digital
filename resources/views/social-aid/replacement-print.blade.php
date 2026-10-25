<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Berita Acara Penggantian - {{ $program->nama_program }}</title>
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
            @page {
                size: A4 portrait;
                margin: 1.5cm 1.5cm 2cm 1.5cm;
            }
        }

        body {
            background-color: #f8fafc;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .a4-page {
            width: 210mm;
            min-height: 297mm;
            padding: 16mm 20mm 20mm 20mm;
            margin: 24px auto;
            background: #fff;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            box-sizing: border-box;
            position: relative;
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            font-size: 10pt;
            line-height: 1.4;
        }

        /* Kop Surat Resmi */
        .kop-header {
            text-align: center;
            margin-bottom: 8px;
        }

        .kop-header .line-1 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .kop-header .line-2 {
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .kop-header .line-3 {
            font-size: 9.5pt;
            font-style: italic;
            color: #333;
        }

        .kop-divider {
            border: 0;
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            height: 3px;
            margin: 6px 0 16px 0;
        }

        /* Judul Surat */
        .judul-surat {
            text-align: center;
            margin-bottom: 16px;
        }

        .judul-surat h1 {
            font-size: 12.5pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin: 0 0 4px 0;
            letter-spacing: 0.5px;
        }

        .judul-surat p {
            font-size: 10pt;
            margin: 0;
        }

        .paragraf {
            text-align: justify;
            text-indent: 32px;
            margin-bottom: 12px;
        }

        .paragraf-no-indent {
            text-align: justify;
            margin-bottom: 10px;
        }

        /* Tabel Penggantian */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0 16px 0;
        }

        .data-table th, .data-table td {
            border: 1px solid #333;
            padding: 5px 6px;
            font-size: 8.5pt;
            vertical-align: top;
        }

        .data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }

        /* Pengesahan 4 Pihak */
        .pengesahan-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .pengesahan-table td {
            vertical-align: top;
            width: 50%;
            font-size: 9pt;
            text-align: center;
            padding-bottom: 12px;
        }

        .ttd-space {
            height: 52px;
        }
    </style>
</head>
<body class="antialiased">

    <!-- Top Navigation & Action Toolbar (Hanya Muncul di Layar Browser) -->
    <header class="no-print sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 px-4 py-2.5 shadow-xs">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
            <!-- Sisi Kiri: Tombol Kembali & Info Dokumen -->
            <div class="flex items-center space-x-3 w-full sm:w-auto">
                <a href="{{ route('social-aid.programs.show', $program) }}" 
                   class="h-9 px-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center space-x-1.5 transition shadow-2xs whitespace-nowrap">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali</span>
                </a>
                
                <span class="text-slate-300">|</span>

                <div class="inline-flex items-center space-x-1.5 px-2.5 py-1.5 rounded-lg bg-slate-100 border border-slate-200/80 text-xs font-bold text-slate-800 whitespace-nowrap">
                    <span class="text-slate-400 font-semibold text-[11px]">Dokumen:</span>
                    <span class="font-semibold text-slate-900 tracking-tight">Berita Acara Penggantian</span>
                </div>

                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 whitespace-nowrap">
                    {{ $rtContext ? 'Wilayah RT ' . $rtContext->nomor_rt : 'Semua RT' }}
                </span>
            </div>

            <!-- Sisi Kanan: Filter RT & Action Buttons -->
            <div class="flex items-center flex-wrap gap-2 w-full sm:w-auto justify-end">
                @if($user->isRwLevel() && $rts->count() > 0)
                <div class="inline-flex items-center space-x-1.5 text-xs text-slate-600 mr-2">
                    <span class="font-medium text-slate-500">Lingkup RT:</span>
                    <select onchange="window.location.href = this.value" class="h-9 px-2.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 focus:ring-1 focus:ring-emerald-500 cursor-pointer">
                        <option value="{{ route('social-aid.programs.replacement-print', ['program' => $program->id]) }}" {{ ! $targetRtId ? 'selected' : '' }}>Semua RT (Rekap RW)</option>
                        @foreach($rts as $r)
                            <option value="{{ route('social-aid.programs.replacement-print', ['program' => $program->id, 'rt_id' => $r->id]) }}" {{ $targetRtId === $r->id ? 'selected' : '' }}>
                                RT {{ $r->nomor_rt }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <a href="{{ route('social-aid.programs.replacement-pdf', ['program' => $program->id, 'rt_id' => $targetRtId]) }}" 
                   class="h-9 px-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center space-x-1.5 transition shadow-2xs whitespace-nowrap"
                   title="Unduh Berkas Resmi PDF">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Unduh PDF</span>
                </a>

                <button onclick="window.print()" 
                        class="h-9 px-3.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold inline-flex items-center space-x-1.5 transition shadow-2xs whitespace-nowrap cursor-pointer"
                        title="Cetak langsung menggunakan printer (Ctrl + P)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Dokumen</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Dokumen Fisik Kertas Ukuran A4 -->
    <main class="py-6 px-4">
        <div class="a4-page">
            <!-- Kop Surat Resmi -->
            <div class="kop-header">
                <div class="line-1">RUKUN WARGA 01</div>
                <div class="line-2">{{ $rtContext ? 'RUKUN TETANGGA ' . str_pad((string)((int)$rtContext->nomor_rt), 2, '0', STR_PAD_LEFT) : 'RUKUN TETANGGA 01 - 04' }}</div>
                <div class="line-3">Kelurahan Slawi Kulon, Kecamatan Slawi, Kab. Tegal</div>
            </div>
            <div class="kop-divider"></div>

            <!-- Judul Dokumen -->
            <div class="judul-surat">
                <h1>BERITA ACARA PENGGANTIAN PENERIMA BANTUAN SOSIAL</h1>
                <p>Nomor: BA-GANTI/{{ $program->id }}/RW.01/{{ \Carbon\Carbon::now()->format('m/Y') }}</p>
            </div>

            <!-- Paragraf Pengantar -->
            <div class="paragraf">
                Pada hari ini, <strong>{{ \Carbon\Carbon::now()->translatedFormat('l') }}</strong> tanggal <strong>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</strong>, bertempat di Sekretariat Lingkungan RW 01 Kelurahan Slawi Kulon, telah disepakati dan diputuskan pengalihan / penggantian data calon penerima manfaat untuk program:
            </div>

            <div style="margin-left: 24px; margin-bottom: 12px; font-size: 9.5pt;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 140px; font-weight: bold;">Program Bantuan</td>
                        <td style="width: 12px;">:</td>
                        <td>{{ $program->nama_program }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Bentuk & Jumlah</td>
                        <td>:</td>
                        <td>{{ $program->detail_bantuan }} ({{ $program->jenis_bantuan_label }})</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Sumber Anggaran</td>
                        <td>:</td>
                        <td>{{ $program->sumber_dana_label }}</td>
                    </tr>
                </table>
            </div>

            <div class="paragraf-no-indent">
                Adapun daftar warga yang mengalami perubahan dan dialihkan kepada calon penerima pengganti adalah sebagai berikut:
            </div>

            <!-- Tabel Penggantian -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 25px;">No</th>
                        <th style="width: 35px;">RT</th>
                        <th style="width: 175px;">Penerima Awal (Diganti)</th>
                        <th style="width: 175px;">Penerima Baru (Pengganti)</th>
                        <th style="width: 110px;">Alasan Pengalihan</th>
                        <th>Keterangan Tambahan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($replacements as $idx => $rep)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="text-center font-bold">RT {{ $rep->rt?->nomor_rt ?? '-' }}</td>
                        <td>
                            <strong>{{ $rep->resident?->nama_lengkap ?? '-' }}</strong>
                            <div style="font-size: 7.5pt; color: #475569;">NIK: {{ $rep->resident?->nik ?? '-' }}</div>
                            <div style="font-size: 7.5pt; color: #475569;">Rumah: {{ $rep->house?->nomor_rumah ?? '-' }}</div>
                            <div style="font-size: 7.5pt; color: #64748b; font-family: monospace;">Kupon: {{ $rep->nomor_urut_kupon ?? '-' }}</div>
                        </td>
                        <td>
                            <strong>{{ $rep->replacementResident?->nama_lengkap ?? 'Belum Ditunjuk' }}</strong>
                            @if($rep->replacementResident)
                                <div style="font-size: 7.5pt; color: #475569;">NIK: {{ $rep->replacementResident->nik ?? '-' }}</div>
                                <div style="font-size: 7.5pt; color: #475569;">Rumah: {{ $rep->replacementResident->house?->nomor_rumah ?? '-' }}</div>
                            @endif
                        </td>
                        <td>
                            <strong style="text-transform: capitalize;">{{ str_replace('_', ' ', $rep->alasan_penggantian ?? '-') }}</strong>
                        </td>
                        <td style="font-size: 8pt; color: #334155;">
                            {{ $rep->catatan_penggantian ?: '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 16px; color: #64748b;">
                            Tidak ada data sanggahan atau penggantian penerima untuk program ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="paragraf">
                Demikian Berita Acara Pergantian Penerima Bantuan Sosial ini dibuat dengan sebenarnya dalam musyawarah mufakat demi ketepatan sasaran bantuan, dan dapat dipertanggungjawabkan sesuai ketentuan administrasi lingkungan yang berlaku.
            </div>

            <!-- Lembar Pengesahan 4 Pihak -->
            <table class="pengesahan-table">
                <tr>
                    <td>
                        Pihak yang Diganti / Ahli Waris,<br>
                        <div class="ttd-space"></div>
                        <strong>( .................................................. )</strong>
                    </td>
                    <td>
                        Penerima Pengganti yang Berhak,<br>
                        <div class="ttd-space"></div>
                        <strong>( .................................................. )</strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        Mengetahui,<br>
                        <strong>Ketua RT {{ $rtContext ? $rtContext->nomor_rt : 'Terkait' }}</strong>
                        <div class="ttd-space"></div>
                        <strong>( {{ $rtContext?->nama_ketua ?: '..................................................' }} )</strong>
                    </td>
                    <td>
                        Mengesahkan,<br>
                        <strong>Ketua RW 01 Kelurahan Slawi Kulon</strong>
                        <div class="ttd-space"></div>
                        <strong>( {{ $ketuaRw?->nama ?: 'AMAT MAJA' }} )</strong>
                    </td>
                </tr>
            </table>
        </div>
    </main>

</body>
</html>
