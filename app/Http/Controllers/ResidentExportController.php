<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResidentExportController extends Controller
{
    /**
     * Ekspor data warga ke file Excel (residents.xlsx) sesuai filter dan hak akses.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = Resident::with(['house.rt'])->forCurrentUser();

        // Filter RT (khusus admin_rw)
        if ($request->filled('rt_id') && auth()->user()?->role === 'admin_rw') {
            $rtId = $request->input('rt_id');
            $query->whereHas('house', function ($q) use ($rtId) {
                $q->where('rt_id', $rtId);
            });
        }

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('no_kk', 'like', "%{$search}%")
                    ->orWhere('pekerjaan', 'like', "%{$search}%");
            });
        }

        // Filter Lansia
        if ($request->boolean('filter_lansia')) {
            $threshold = Carbon::today()->subYears(60);
            $query->whereDate('tanggal_lahir', '<=', $threshold);
        }

        // Filter Balita
        if ($request->boolean('filter_balita')) {
            $threshold = Carbon::today()->subYears(5);
            $query->whereDate('tanggal_lahir', '>', $threshold);
        }

        // Filter Domisili Luar
        if ($request->boolean('filter_domisili_luar')) {
            $query->where('status_kependudukan', 'domisili_luar');
        }

        // Filter Status Warga
        if ($request->filled('status_warga') && $request->input('status_warga') !== 'semua') {
            $query->where('status_warga', $request->input('status_warga'));
        }

        $residents = $query->orderBy('nama_lengkap', 'asc')->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Warga RW');

        // Header Dokumen
        $sheet->setCellValue('A1', 'DATA KEPENDUDUKAN WARGA RW (MULTI-RT)');
        $sheet->setCellValue('A2', 'Diekspor pada: '.now()->translatedFormat('d F Y H:i').' WIB oleh: '.(auth()->user()?->nama ?? 'Pengurus'));
        $sheet->mergeCells('A1:P1');
        $sheet->mergeCells('A2:P2');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(11);

        // Header Kolom Tabel
        $headers = [
            'No',
            'RT',
            'No. Rumah',
            'NIK',
            'No. Kartu Keluarga',
            'Nama Lengkap',
            'L/P',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Usia (Thn)',
            'Agama',
            'Pekerjaan',
            'Status Keluarga',
            'Status Domisili',
            'No. WhatsApp',
            'Status Warga',
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col.'4', $header);
            $col++;
        }

        // Format Header Baris 4
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B'], // Slate 800
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
        ];
        $sheet->getStyle('A4:P4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(28);

        // Isi Data Baris
        $row = 5;
        $no = 1;

        foreach ($residents as $resident) {
            $statusKeluarga = match ($resident->status_keluarga) {
                'kepala_keluarga' => 'Kepala Keluarga',
                'istri' => 'Istri',
                'anak' => 'Anak',
                'famili_lain' => 'Famili Lain',
                default => $resident->status_keluarga,
            };

            $statusDomisili = match ($resident->status_kependudukan) {
                'ktp_setempat' => 'KTP Setempat',
                'domisili_luar' => 'Domisili Luar',
                default => $resident->status_kependudukan,
            };

            $statusWarga = match ($resident->status_warga) {
                'aktif' => 'Aktif',
                'pindah' => 'Pindah Keluar',
                'meninggal' => 'Meninggal',
                default => $resident->status_warga,
            };

            $sheet->setCellValue('A'.$row, $no++);
            $sheet->setCellValue('B'.$row, 'RT '.($resident->house?->rt?->nomor_rt ?? '-'));
            $sheet->setCellValue('C'.$row, $resident->house?->nomor_rumah ?? '-');
            $sheet->setCellValueExplicit('D'.$row, $resident->nik, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('E'.$row, $resident->no_kk, DataType::TYPE_STRING);
            $sheet->setCellValue('F'.$row, $resident->nama_lengkap);
            $sheet->setCellValue('G'.$row, $resident->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan');
            $sheet->setCellValue('H'.$row, $resident->tempat_lahir);
            $sheet->setCellValue('I'.$row, $resident->tanggal_lahir ? $resident->tanggal_lahir->format('d/m/Y') : '-');
            $sheet->setCellValue('J'.$row, $resident->umur);
            $sheet->setCellValue('K'.$row, $resident->agama);
            $sheet->setCellValue('L'.$row, $resident->pekerjaan);
            $sheet->setCellValue('M'.$row, $statusKeluarga);
            $sheet->setCellValue('N'.$row, $statusDomisili);
            $sheet->setCellValueExplicit('O'.$row, $resident->no_wa ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValue('P'.$row, $statusWarga);

            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        }

        $lastRow = max(5, $row - 1);
        $contentRange = 'A5:P'.$lastRow;

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
        $sheet->getStyle('G5:G'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I5:J'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('P5:P'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto width untuk setiap kolom
        foreach (range('A', 'P') as $colId) {
            $sheet->getColumnDimension($colId)->setAutoSize(true);
        }

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="residents.xlsx"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
