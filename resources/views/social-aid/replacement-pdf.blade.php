<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Penggantian Penerima Bansos - {{ $program->nama_program }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.8cm 2cm 2cm 2cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat Resmi */
        .kop-header {
            text-align: center;
            margin-bottom: 6px;
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
            margin: 6px 0 16px 0;
        }

        /* Judul Surat */
        .judul-surat {
            text-align: center;
            margin-bottom: 18px;
        }

        .judul-surat h1 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin: 0 0 4px 0;
            letter-spacing: 0.5px;
        }

        .judul-surat p {
            font-size: 10.5pt;
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

        /* Blok Tanda Tangan */
        .ttd-wrapper {
            margin-top: 24px;
            width: 100%;
            page-break-inside: avoid;
        }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ttd-table td {
            vertical-align: top;
            width: 33.33%;
            font-size: 9.5pt;
            text-align: center;
        }

        .ttd-space {
            height: 55px;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT RESMI -->
    <div class="kop-header">
        <div class="line-1">RUKUN WARGA 01</div>
        <div class="line-2">{{ $rtContext ? 'RUKUN TETANGGA ' . str_pad((string)((int)$rtContext->nomor_rt), 2, '0', STR_PAD_LEFT) : 'RUKUN TETANGGA 01 - 04' }}</div>
        <div class="line-3">Kelurahan Slawi Kulon, Kecamatan Slawi, Kab. Tegal</div>
    </div>
    <div class="kop-divider"></div>

    <!-- JUDUL -->
    <div class="judul-surat">
        <h1>BERITA ACARA PENGGANTIAN PENERIMA BANTUAN SOSIAL</h1>
        <p>Nomor: BA-BANSOS/{{ date('m') }}/RW05/{{ date('Y') }}</p>
    </div>

    <!-- NARASI AWAL -->
    <div class="paragraf">
        Pada hari ini, <strong>{{ now()->translatedFormat('l') }}</strong> tanggal <strong>{{ now()->translatedFormat('d F Y') }}</strong>, bertempat di Balai Sekretariat RW 05, telah diselenggarakan musyawarah verifikasi faktual lapangan terkait alokasi penerima bantuan program <strong>"{{ $program->nama_program }}"</strong> (Sumber: {{ $program->sumber_dana_label }}).
    </div>

    <div class="paragraf-no-indent">
        Berdasarkan pemeriksaan kondisi riil di lingkungan, Pengurus RT dan RW menetapkan perubahan dan pengalihan hak Keluarga Penerima Manfaat (KPM) awal kepada warga pengganti yang memenuhi kriteria prioritas kemiskinan dan kerentanan sosial, dengan rincian sebagai berikut:
    </div>

    <!-- TABEL DATA PENGGANTIAN -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 40px;">RT</th>
                <th>Penerima Awal (Kelurahan)</th>
                <th style="width: 100px;">Alasan Penggantian</th>
                <th>Warga Pengganti yang Sah</th>
                <th style="width: 120px;">Keterangan / Alamat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($replacements as $idx => $item)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td class="text-center">RT {{ $item->rt?->nomor_rt ?? '-' }}</td>
                <td>
                    <strong>{{ $item->resident?->nama_lengkap ?? '-' }}</strong><br>
                    <span class="font-mono" style="font-size: 7.5pt; color: #475569;">NIK: {{ $item->resident?->nik ?? '-' }}</span><br>
                    <span style="font-size: 7.5pt; color: #64748b;">Rumah: Blok {{ $item->house?->blok_nomor ?? '-' }}</span>
                </td>
                <td class="text-center">
                    <strong style="text-transform: capitalize;">{{ $item->alasan_penggantian_label ?? 'Penyesuaian' }}</strong>
                </td>
                <td>
                    @if($item->replacementResident)
                        <strong>{{ $item->replacementResident->nama_lengkap }}</strong><br>
                        <span class="font-mono" style="font-size: 7.5pt; color: #475569;">NIK: {{ $item->replacementResident->nik }}</span><br>
                        <span style="font-size: 7.5pt; color: #166534;">Rumah: Blok {{ $item->replacementResident->house?->blok_nomor ?? '-' }}</span>
                    @else
                        <span style="color: #94a3b8; font-style: italic;">(Belum dipilih)</span>
                    @endif
                </td>
                <td style="font-size: 8pt;">
                    {{ $item->catatan_penggantian ?? '-' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 16px; color: #64748b;">
                    Tidak ada data penggantian / sanggahan penerima dalam program ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="paragraf-no-indent" style="font-size: 9.5pt;">
        Demikian Berita Acara ini dibuat dengan sebenar-benarnya secara transparan, bertanggung jawab, dan berdasarkan hasil verifikasi lapangan bersama demi terwujudnya penyaluran bantuan yang tepat sasaran, berkeadilan, dan bebas dari konflik kepentingan.
    </div>

    <!-- PENGESAHAN 3 PIHAK -->
    <div class="ttd-wrapper">
        <table class="ttd-table">
            <tr>
                <td>
                    Pengurus Lingkungan,<br>
                    <strong>Ketua RT {{ $rtContext ? $rtContext->nomor_rt : 'Terkait' }}</strong>
                    <div class="ttd-space"></div>
                    <strong>( {{ $rtContext?->nama_ketua ?: '..................................................' }} )</strong>
                </td>
                <td>
                    Pengurus Wilayah,<br>
                    <strong>Ketua RW 01</strong>
                    <div class="ttd-space"></div>
                    <strong>( {{ $ketuaRw?->nama ?: 'AMAT MAJA' }} )</strong>
                </td>
                <td>
                    Mengetahui,<br>
                    <strong>Kasi Kesra / Kelurahan</strong>
                    <div class="ttd-space"></div>
                    <strong>( .................................................. )</strong>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
