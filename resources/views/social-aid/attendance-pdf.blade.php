<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir Penyaluran Bansos - {{ $program->nama_program }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm 1.5cm 2cm 1.5cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat Resmi */
        .kop-header {
            text-align: center;
            margin-bottom: 8px;
        }

        .kop-header .line-1 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .kop-header .line-2 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .kop-header .line-3 {
            font-size: 9pt;
            font-style: italic;
            color: #333;
        }

        .kop-divider {
            border: 0;
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            height: 3px;
            margin: 6px 0 14px 0;
        }

        /* Judul Dokumen */
        .judul-dokumen {
            text-align: center;
            margin-bottom: 14px;
        }

        .judul-dokumen h1 {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin: 0 0 4px 0;
            letter-spacing: 0.5px;
        }

        .judul-dokumen p {
            font-size: 9.5pt;
            margin: 0;
        }

        /* Metadata Program */
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9.5pt;
        }

        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .meta-table td.lbl {
            width: 130px;
            font-weight: bold;
        }

        .meta-table td.sep {
            width: 12px;
            text-align: center;
        }

        /* Tabel Utama */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .data-table th, .data-table td {
            border: 1px solid #444;
            padding: 5px 6px;
            font-size: 8.5pt;
            vertical-align: middle;
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

        .ttd-box {
            height: 28px;
            position: relative;
        }

        .ttd-num {
            position: absolute;
            top: 2px;
            font-size: 7.5pt;
            color: #777;
        }

        .ttd-num.odd { left: 4px; }
        .ttd-num.even { right: 8px; }

        /* Blok Tanda Tangan Pengesahan */
        .pengesahan-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            page-break-inside: avoid;
        }

        .pengesahan-table td {
            vertical-align: top;
            width: 50%;
            font-size: 9.5pt;
            text-align: center;
        }

        .ttd-space {
            height: 60px;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="kop-header">
        <div class="line-1">RUKUN WARGA 01</div>
        <div class="line-2">{{ $rtContext ? 'RUKUN TETANGGA ' . str_pad((string)((int)$rtContext->nomor_rt), 2, '0', STR_PAD_LEFT) : 'RUKUN TETANGGA 01 - 04' }}</div>
        <div class="line-3">Kelurahan Slawi Kulon, Kecamatan Slawi, Kab. Tegal</div>
    </div>
    <div class="kop-divider"></div>

    <!-- JUDUL -->
    <div class="judul-dokumen">
        <h1>DAFTAR HADIR & BUKTI PENERIMAAN BANTUAN SOSIAL</h1>
        <p>Tahun Anggaran {{ \Carbon\Carbon::parse($program->tanggal_penyaluran)->format('Y') }}</p>
    </div>

    <!-- METADATA PROGRAM -->
    <table class="meta-table">
        <tr>
            <td class="lbl">Nama Program</td>
            <td class="sep">:</td>
            <td><strong>{{ $program->nama_program }}</strong></td>
            <td class="lbl">Sumber Dana</td>
            <td class="sep">:</td>
            <td>{{ $program->sumber_dana_label }}</td>
        </tr>
        <tr>
            <td class="lbl">Bentuk Bantuan</td>
            <td class="sep">:</td>
            <td>{{ $program->detail_bantuan }} ({{ $program->jenis_bantuan_label }})</td>
            <td class="lbl">Tanggal Penyaluran</td>
            <td class="sep">:</td>
            <td>{{ \Carbon\Carbon::parse($program->tanggal_penyaluran)->translatedFormat('l, d F Y') }}</td>
        </tr>
        <tr>
            <td class="lbl">Lokasi Pengambilan</td>
            <td class="sep">:</td>
            <td>{{ $program->lokasi_pengambilan }}</td>
            <td class="lbl">Lingkup Wilayah</td>
            <td class="sep">:</td>
            <td>{{ $rtContext ? 'RT ' . $rtContext->nomor_rt : 'Seluruh RT di Lingkungan RW' }}</td>
        </tr>
    </table>

    <!-- TABEL PENERIMA -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 75px; white-space: nowrap;">No. Kupon</th>
                <th style="width: 38px; white-space: nowrap;">RT</th>
                <th>Nama Kepala Keluarga</th>
                <th style="width: 110px; white-space: nowrap;">NIK</th>
                <th style="width: 110px;">Alamat / Rumah</th>
                <th style="width: 115px;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recipients as $idx => $r)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td class="text-center font-mono font-bold" style="white-space: nowrap;">{{ $r->nomor_urut_kupon ?? '-' }}</td>
                <td class="text-center" style="white-space: nowrap;">RT {{ $r->rt?->nomor_rt ?? '-' }}</td>
                <td>
                    <strong>{{ $r->resident?->nama_lengkap ?? '-' }}</strong>
                    @if($r->status_penyerahan === 'diwakilkan' && $r->penerima_aktual)
                        <div style="font-size: 7.5pt; color: #475569; font-style: italic;">
                            (Diwakilkan: {{ $r->penerima_aktual }})
                        </div>
                    @endif
                </td>
                <td class="text-center font-mono">{{ $r->resident?->nik ?? '-' }}</td>
                <td>{{ $r->house?->nomor_rumah ?? '-' }}</td>
                <td class="ttd-box">
                    @if($idx % 2 === 0)
                        <span class="ttd-num odd">{{ $idx + 1 }}. .............</span>
                    @else
                        <span class="ttd-num even">{{ $idx + 1 }}. .............</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 16px; color: #64748b;">
                    Belum ada data penerima yang diusulkan untuk cetak daftar hadir.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="font-size: 8pt; color: #64748b; margin-bottom: 12px; font-style: italic;">
        * Catatan: Warga penerima wajib membawa KTP asli/fotokopi dan Kartu Keluarga saat pengambilan bantuan. Bila diwakilkan, penerima kuasa harus berada dalam 1 Kartu Keluarga.
    </div>

    <!-- PENGESAHAN -->
    <table class="pengesahan-table">
        <tr>
            @if($rtContext)
                <td style="width: 50%;">
                    Mengetahui,<br>
                    <strong>Ketua RT {{ $rtContext->nomor_rt }}</strong>
                    <div class="ttd-space"></div>
                    <strong>( {{ $rtContext->nama_ketua ?: '..................................................' }} )</strong>
                </td>
                <td style="width: 50%;">
                    Slawi, {{ \Carbon\Carbon::parse($program->tanggal_penyaluran)->translatedFormat('d F Y') }}<br>
                    <strong>Ketua RW 01</strong>
                    <div class="ttd-space"></div>
                    <strong>( {{ $ketuaRw?->nama ?: 'AMAT MAJA' }} )</strong>
                </td>
            @else
                <td style="width: 55%;"></td>
                <td style="width: 45%;">
                    Slawi, {{ \Carbon\Carbon::parse($program->tanggal_penyaluran)->translatedFormat('d F Y') }}<br>
                    <strong>Ketua RW 01</strong>
                    <div class="ttd-space"></div>
                    <strong>( {{ $ketuaRw?->nama ?: 'AMAT MAJA' }} )</strong>
                </td>
            @endif
        </tr>
    </table>

</body>
</html>
