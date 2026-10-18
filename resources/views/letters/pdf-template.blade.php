<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pengantar - {{ $letter->nomor_surat }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 2cm 2.5cm 2cm 2.5cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.45;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
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
        }

        .paragraf-no-indent {
            text-align: justify;
            margin-bottom: 10px;
        }

        /* Tabel Biodata */
        .table-biodata {
            width: 100%;
            margin: 12px 0 16px 20px;
            border-collapse: collapse;
        }

        .table-biodata td {
            padding: 2.5px 0;
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
            padding: 6px 12px;
            background-color: #f7f7f7;
            border-left: 3px solid #000;
            font-size: 11.5pt;
        }

        /* Tanda Tangan */
        .ttd-wrapper {
            margin-top: 35px;
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
            margin: 6px auto;
            text-align: center;
        }

        .tte-qr {
            width: 65px;
            height: 65px;
            display: block;
            margin: 0 auto;
        }

        .tte-caption {
            font-size: 6.5pt;
            color: #475569;
            margin-top: 3px;
            font-weight: bold;
            line-height: 1.1;
        }

        .tte-footer-note {
            margin-top: 20px;
            font-size: 7.5pt;
            color: #64748b;
            border-top: 1px dashed #cbd5e1;
            padding-top: 5px;
            font-style: italic;
            line-height: 1.3;
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
            font-size: 40pt;
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
<body>
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
                        <div style="width: 140px; height: 65px; margin: 6px auto; border: 1px dashed #d97706; background-color: #fffbeb; text-align: center;">
                            <div style="padding-top: 18px; font-size: 7.5pt; color: #b45309; font-weight: bold; line-height: 1.2;">
                                [ Menunggu Pengesahan<br>Ketua RT ]
                            </div>
                        </div>
                    @elseif($letter->status === 'menunggu_rw')
                        <div style="width: 140px; height: 65px; margin: 6px auto; border: 1px dashed #d97706; background-color: #fffbeb; text-align: center;">
                            <div style="padding-top: 18px; font-size: 7.5pt; color: #b45309; font-weight: bold; line-height: 1.2;">
                                [ Pending Pengesahan<br>& TTE Ketua RW ]
                            </div>
                        </div>
                    @elseif($letter->status === 'ditolak_rt')
                        <div style="width: 140px; height: 65px; margin: 6px auto; border: 1px dashed #dc2626; background-color: #fef2f2; text-align: center;">
                            <div style="padding-top: 18px; font-size: 7.5pt; color: #b91c1c; font-weight: bold; line-height: 1.2;">
                                [ Dibatalkan / Ditolak<br>di Tingkat RT ]
                            </div>
                        </div>
                    @elseif($letter->status === 'ditolak_rw' || $letter->status === 'ditolak')
                        <div style="width: 140px; height: 65px; margin: 6px auto; border: 1px dashed #dc2626; background-color: #fef2f2; text-align: center;">
                            <div style="padding-top: 18px; font-size: 7.5pt; color: #b91c1c; font-weight: bold; line-height: 1.2;">
                                [ Pengesahan Ditolak<br>Ketua RW ]
                            </div>
                        </div>
                    @elseif($letter->status === 'dibatalkan')
                        <div style="width: 140px; height: 65px; margin: 6px auto; border: 1px dashed #dc2626; background-color: #fef2f2; text-align: center;">
                            <div style="padding-top: 18px; font-size: 7.5pt; color: #b91c1c; font-weight: bold; line-height: 1.2;">
                                [ Surat Dibatalkan<br>Tidak Berlaku ]
                            </div>
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
                        <div style="width: 140px; height: 65px; margin: 6px auto; border: 1px dashed #d97706; background-color: #fffbeb; text-align: center;">
                            <div style="padding-top: 18px; font-size: 7.5pt; color: #b45309; font-weight: bold; line-height: 1.2;">
                                [ Pending Pengesahan<br>& TTE Ketua RT ]
                            </div>
                        </div>
                    @elseif($letter->status === 'ditolak_rt')
                        <div style="width: 140px; height: 65px; margin: 6px auto; border: 1px dashed #dc2626; background-color: #fef2f2; text-align: center;">
                            <div style="padding-top: 18px; font-size: 7.5pt; color: #b91c1c; font-weight: bold; line-height: 1.2;">
                                [ Pengesahan Ditolak<br>Ketua RT ]
                            </div>
                        </div>
                    @elseif($letter->status === 'dibatalkan')
                        <div style="width: 140px; height: 65px; margin: 6px auto; border: 1px dashed #dc2626; background-color: #fef2f2; text-align: center;">
                            <div style="padding-top: 18px; font-size: 7.5pt; color: #b91c1c; font-weight: bold; line-height: 1.2;">
                                [ Surat Dibatalkan<br>Tidak Berlaku ]
                            </div>
                        </div>
                    @elseif($letter->status === 'ditolak_rw' || $letter->status === 'ditolak')
                        <div style="width: 140px; height: 65px; margin: 6px auto; border: 1px dashed #dc2626; background-color: #fef2f2; text-align: center;">
                            <div style="padding-top: 18px; font-size: 7.5pt; color: #b91c1c; font-weight: bold; line-height: 1.2;">
                                [ Pengesahan Ditolak<br>Ketua RW ]
                            </div>
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
</body>
</html>
