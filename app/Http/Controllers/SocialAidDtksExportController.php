<?php

namespace App\Http\Controllers;

use App\Models\House;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SocialAidDtksExportController extends Controller
{
    /**
     * Ekspor rekomendasi warga calon penerima DTKS baru yang belum terdaftar ke Excel (.xlsx)
     */
    public function export(Request $request): StreamedResponse
    {
        $user = auth()->user();

        if (! $user || ! $user->canManageSocialAid()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunduh usulan DTKS.');
        }

        $query = House::with(['rt', 'socialProfile', 'residents' => function ($q) {
            $q->where('status_warga', 'aktif');
        }])->whereHas('socialProfile', function ($q) {
            $q->whereIn('kategori_ekonomi', ['miskin_ekstrem', 'rentan_miskin'])
                ->where('status_dtks', '!=', 'terdaftar');
        });

        // Filter Hak Akses Isolasi RT
        if ($user->isPengurusRt() && $user->rt_id) {
            $query->where('rt_id', $user->rt_id);
        } elseif ($request->filled('rt_id')) {
            $query->where('rt_id', $request->input('rt_id'));
        }

        $houses = $query->get()->sortByDesc(function ($house) {
            return $house->socialProfile?->skor_prioritas ?? 0;
        });

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Usulan DTKS Baru');

        // Header Judul Dokumen
        $sheet->setCellValue('A1', 'REKOMENDASI PENGUSULAN WARGA MASUK DATA TERPADU KESEJAHTERAAN SOSIAL (DTKS)');
        $sheet->setCellValue('A2', 'Tingkat RW 01 - Diekspor pada: '.now()->translatedFormat('d F Y H:i').' WIB oleh: '.($user->nama ?? 'Pengurus'));
        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);

        // Header Kolom
        $headers = [
            'No',
            'RT',
            'Blok / No. Rumah',
            'Nama Kepala Keluarga',
            'NIK Kepala Keluarga',
            'No. Kartu Keluarga',
            'No. WhatsApp / HP',
            'Kategori Ekonomi',
            'Rentang Penghasilan',
            'Skor Prioritas',
            'Indikator Kerentanan Sosial',
            'Catatan Kondisi Lapangan',
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col.'4', $header);
            $col++;
        }

        // Styling Header Kolom (Baris 4)
        $sheet->getStyle('A4:L4')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10.5,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '047857'], // Emerald 700
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'A7F3D0'],
                ],
            ],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(30);

        // Isi Data Baris
        $row = 5;
        $no = 1;

        foreach ($houses as $house) {
            $profile = $house->socialProfile;
            $headOfFamily = $house->residents->firstWhere('status_keluarga', 'kepala_keluarga') ?? $house->residents->first();

            // Identifikasi Kerentanan
            $kerentanan = [];
            $hasElderly = $house->residents->contains(function ($r) {
                return $r->tanggal_lahir && Carbon::parse($r->tanggal_lahir)->age >= 60;
            });
            if ($hasElderly) {
                $kerentanan[] = 'Lansia (≥60 Thn)';
            }
            if ($profile?->is_kepala_keluarga_janda_duda) {
                $kerentanan[] = 'KK Janda/Duda';
            }
            if ($profile?->ada_anggota_disabilitas_sakit_kronis) {
                $kerentanan[] = 'Disabilitas/Sakit Kronis';
            }
            if ($profile?->kondisi_rumah_tidak_layak) {
                $kerentanan[] = 'Rumah Tidak Layak';
            }

            $kerentananText = ! empty($kerentanan) ? implode(', ', $kerentanan) : 'Tidak ada indikator khusus';

            $sheet->setCellValue('A'.$row, $no++);
            $sheet->setCellValue('B'.$row, 'RT '.($house->rt?->nomor_rt ?? '-'));
            $sheet->setCellValue('C'.$row, 'Blok '.$house->blok_nomor);
            $sheet->setCellValue('D'.$row, $headOfFamily?->nama_lengkap ?? '(Belum terdata)');
            $sheet->setCellValueExplicit('E'.$row, $headOfFamily?->nik ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('F'.$row, $headOfFamily?->no_kk ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('G'.$row, $headOfFamily?->no_wa ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValue('H'.$row, $profile?->kategori_ekonomi_label ?? '-');
            $sheet->setCellValue('I'.$row, $profile?->rentang_penghasilan_label ?? '-');
            $sheet->setCellValue('J'.$row, ($profile?->skor_prioritas ?? 0).' Pts');
            $sheet->setCellValue('K'.$row, $kerentananText);
            $sheet->setCellValue('L'.$row, $profile?->catatan_kondisi ?? '-');

            $sheet->getRowDimension($row)->setRowHeight(24);
            $row++;
        }

        $lastRow = max(5, $row - 1);
        $contentRange = 'A5:L'.$lastRow;

        $sheet->getStyle($contentRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E2E8F0'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A5:A'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B5:B'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C5:C'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E5:G'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H5:J'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto width untuk setiap kolom
        foreach (range('A', 'L') as $colId) {
            $sheet->getColumnDimension($colId)->setAutoSize(true);
        }

        $safeTimestamp = now()->format('Ymd_His');

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"Usulan_DTKS_Warga_{$safeTimestamp}.xlsx\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
