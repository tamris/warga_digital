<?php

namespace App\Services;

use App\Models\House;
use App\Models\Resident;
use App\Models\Rt;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResidentImportService
{
    /**
     * Download format template resmi Excel untuk import data warga & rumah
     */
    public function downloadTemplate(?int $rtId = null): StreamedResponse
    {
        $rt = $rtId ? Rt::find($rtId) : null;
        $rtLabel = $rt ? 'RT '.str_pad((string) ((int) $rt->nomor_rt), 2, '0', STR_PAD_LEFT) : 'RT';

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Warga');

        // Judul & Panduan
        $sheet->setCellValue('A1', "FORMAT IMPORT DATA WARGA & RUMAH ({$rtLabel})");
        $sheet->setCellValue('A2', 'PENTING: Jangan ubah susunan kolom baris 4. Isikan tanggal lahir dengan format YYYY-MM-DD (Contoh: 1985-04-12) atau DD/MM/YYYY. NIK dan No KK wajib 16 digit.');
        $sheet->mergeCells('A1:N1');
        $sheet->mergeCells('A2:N2');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('0F172A');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB('64748B');

        // Header Kolom (Baris 4)
        $headers = [
            'Nomor Rumah *',
            'Status Huni *',
            'Kategori Tarif *',
            'No KK *',
            'NIK *',
            'Nama Lengkap *',
            'L/P *',
            'Tempat Lahir',
            'Tanggal Lahir *',
            'Agama',
            'Pekerjaan',
            'Status Keluarga *',
            'Status Domisili *',
            'No WhatsApp',
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col.'4', $header);
            $col++;
        }

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'], // Emerald 600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'A7F3D0'],
                ],
            ],
        ];
        $sheet->getStyle('A4:N4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(26);

        // Contoh Data 1 Keluarga (Baris 5 & 6)
        $samples = [
            [
                'Blok A No. 01', 'dihuni_pemilik', 'standar', '3201012345670001', '3201011204850001',
                'Budi Santoso', 'L', 'Bandung', '1985-04-12', 'Islam', 'Karyawan Swasta',
                'kepala_keluarga', 'ktp_setempat', '081234567890',
            ],
            [
                'Blok A No. 01', 'dihuni_pemilik', 'standar', '3201012345670001', '3201015508880002',
                'Siti Aminah', 'P', 'Jakarta', '1988-08-15', 'Islam', 'Ibu Rumah Tangga',
                'istri', 'ktp_setempat', '081234567891',
            ],
            [
                'Blok A No. 02', 'kontrak', 'usaha', '3201019988770002', '3201010101900003',
                'Hendra Pratama', 'L', 'Surabaya', '1990-01-01', 'Islam', 'Wiraswasta',
                'kepala_keluarga', 'domisili_luar', '085712345678',
            ],
        ];

        $rowIdx = 5;
        foreach ($samples as $sample) {
            $sheet->setCellValue('A'.$rowIdx, $sample[0]);
            $sheet->setCellValue('B'.$rowIdx, $sample[1]);
            $sheet->setCellValue('C'.$rowIdx, $sample[2]);
            $sheet->setCellValueExplicit('D'.$rowIdx, $sample[3], DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('E'.$rowIdx, $sample[4], DataType::TYPE_STRING);
            $sheet->setCellValue('F'.$rowIdx, $sample[5]);
            $sheet->setCellValue('G'.$rowIdx, $sample[6]);
            $sheet->setCellValue('H'.$rowIdx, $sample[7]);
            $sheet->setCellValue('I'.$rowIdx, $sample[8]);
            $sheet->setCellValue('J'.$rowIdx, $sample[9]);
            $sheet->setCellValue('K'.$rowIdx, $sample[10]);
            $sheet->setCellValue('L'.$rowIdx, $sample[11]);
            $sheet->setCellValue('M'.$rowIdx, $sample[12]);
            $sheet->setCellValueExplicit('N'.$rowIdx, $sample[13], DataType::TYPE_STRING);

            $sheet->getRowDimension($rowIdx)->setRowHeight(20);
            $rowIdx++;
        }

        // Auto width
        foreach (range('A', 'N') as $colId) {
            $sheet->getColumnDimension($colId)->setAutoSize(true);
        }

        $filename = 'template_import_warga_'.($rt ? 'rt_'.str_pad((string) ((int) $rt->nomor_rt), 2, '0', STR_PAD_LEFT) : 'all').'.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Baca dan validasi berkas Excel sebelum eksekusi commit
     *
     * @return array{
     *     success: bool,
     *     total_rows: int,
     *     new_houses_count: int,
     *     new_residents_count: int,
     *     errors: list<string>,
     *     preview_data: list<array<string, mixed>>,
     *     valid_rows: list<array<string, mixed>>
     * }
     */
    public function parseAndValidate(string $filePath, int $rtId): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        $errors = [];
        $validRows = [];
        $seenNiksInFile = [];
        $uniqueHouses = [];

        for ($row = 5; $row <= $highestRow; $row++) {
            $nomorRumah = trim((string) $sheet->getCell('A'.$row)->getValue());
            if ($nomorRumah === '') {
                continue; // Lewati baris kosong
            }

            $statusHuni = strtolower(trim((string) $sheet->getCell('B'.$row)->getValue()));
            if (! in_array($statusHuni, ['dihuni_pemilik', 'kontrak', 'kosong'], true)) {
                $statusHuni = 'dihuni_pemilik';
            }

            $kategoriTarif = strtolower(trim((string) $sheet->getCell('C'.$row)->getValue()));
            if (! in_array($kategoriTarif, ['standar', 'usaha', 'kosong'], true)) {
                $kategoriTarif = 'standar';
            }

            $noKk = preg_replace('/[^0-9]/', '', (string) $sheet->getCell('D'.$row)->getValue());
            $nik = preg_replace('/[^0-9]/', '', (string) $sheet->getCell('E'.$row)->getValue());
            $namaLengkap = strtoupper(trim((string) $sheet->getCell('F'.$row)->getValue()));
            $jenisKelamin = strtoupper(trim((string) $sheet->getCell('G'.$row)->getValue()));
            $tempatLahir = strtoupper(trim((string) $sheet->getCell('H'.$row)->getValue()));

            $cellDate = $sheet->getCell('I'.$row);
            $rawDateVal = $cellDate->getValue();
            $tglLahir = '1990-01-01';

            if (Date::isDateTime($cellDate)) {
                try {
                    $tglLahir = Carbon::instance(Date::excelToDateTimeObject($rawDateVal))->toDateString();
                } catch (\Throwable) {
                    $tglLahir = '1990-01-01';
                }
            } elseif (is_numeric($rawDateVal) && (float) $rawDateVal > 1000) {
                try {
                    $tglLahir = Carbon::instance(Date::excelToDateTimeObject((float) $rawDateVal))->toDateString();
                } catch (\Throwable) {
                    $tglLahir = '1990-01-01';
                }
            } else {
                $tglLahirStr = trim((string) $rawDateVal);
                if ($tglLahirStr !== '') {
                    try {
                        $tglLahir = Carbon::parse($tglLahirStr)->toDateString();
                    } catch (\Throwable) {
                        $tglLahir = '1990-01-01';
                    }
                }
            }

            $agama = ucwords(strtolower(trim((string) $sheet->getCell('J'.$row)->getValue())));
            $pekerjaan = strtoupper(trim((string) $sheet->getCell('K'.$row)->getValue()));
            $statusKeluarga = strtolower(trim((string) $sheet->getCell('L'.$row)->getValue()));
            $statusDomisili = strtolower(trim((string) $sheet->getCell('M'.$row)->getValue()));

            $noWaRaw = (string) $sheet->getCell('N'.$row)->getValue();
            $noWaDigits = preg_replace('/[^0-9]/', '', $noWaRaw);
            $noWa = $noWaDigits !== '' ? $noWaDigits : null;

            // Validasi NIK
            if (strlen($nik) !== 16) {
                $errors[] = "Baris {$row}: NIK '{$nik}' tidak valid (wajib 16 digit angka).";

                continue;
            }

            if (isset($seenNiksInFile[$nik])) {
                $errors[] = "Baris {$row}: NIK '{$nik}' duplikat di dalam berkas Excel ini (sama dengan baris {$seenNiksInFile[$nik]}).";

                continue;
            }
            $seenNiksInFile[$nik] = $row;

            if ($namaLengkap === '') {
                $errors[] = "Baris {$row}: Nama lengkap tidak boleh kosong.";

                continue;
            }

            if (strlen($noKk) !== 16) {
                $noKk = str_pad($noKk, 16, '0', STR_PAD_RIGHT);
            }

            if (! in_array($jenisKelamin, ['L', 'P'], true)) {
                $jenisKelamin = 'L';
            }

            if (! in_array($agama, ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'], true)) {
                $agama = 'Islam';
            }

            if (! in_array($statusKeluarga, ['kepala_keluarga', 'istri', 'anak', 'famili_lain'], true)) {
                $statusKeluarga = 'kepala_keluarga';
            }

            if (! in_array($statusDomisili, ['ktp_setempat', 'domisili_luar'], true)) {
                $statusDomisili = 'ktp_setempat';
            }

            $uniqueHouses[$nomorRumah] = true;

            $validRows[] = [
                'nomor_rumah' => $nomorRumah,
                'status_huni' => $statusHuni,
                'kategori_tarif' => $kategoriTarif,
                'no_kk' => $noKk,
                'nik' => $nik,
                'nama_lengkap' => $namaLengkap,
                'jenis_kelamin' => $jenisKelamin,
                'tempat_lahir' => $tempatLahir ?: '-',
                'tanggal_lahir' => $tglLahir,
                'agama' => $agama,
                'pekerjaan' => $pekerjaan ?: 'Belum Bekerja',
                'status_keluarga' => $statusKeluarga,
                'status_kependudukan' => $statusDomisili,
                'no_wa' => $noWa ?: null,
            ];
        }

        return [
            'success' => count($errors) === 0,
            'total_rows' => count($validRows),
            'new_houses_count' => count($uniqueHouses),
            'new_residents_count' => count($validRows),
            'errors' => $errors,
            'preview_data' => array_slice($validRows, 0, 10),
            'valid_rows' => $validRows,
        ];
    }

    /**
     * Eksekusi Commit Data ke Database dalam 1 Transaksi Bersih
     *
     * @param  list<array<string, mixed>>  $rows
     * @return array{houses_count: int, residents_count: int}
     */
    public function executeImport(array $rows, int $rtId): array
    {
        return DB::transaction(function () use ($rows, $rtId) {
            $housesMap = [];
            $housesCount = 0;
            $residentsCount = 0;

            foreach ($rows as $row) {
                $noRumah = $row['nomor_rumah'];

                if (! isset($housesMap[$noRumah])) {
                    $house = House::firstOrCreate(
                        [
                            'rt_id' => $rtId,
                            'nomor_rumah' => $noRumah,
                        ],
                        [
                            'status_huni' => $row['status_huni'],
                            'kategori_tarif' => $row['kategori_tarif'],
                            'catatan' => 'Diimpor via Batch Excel pada '.now()->format('d/m/Y H:i'),
                        ]
                    );

                    $housesMap[$noRumah] = $house->id;
                    $housesCount++;
                }

                $houseId = $housesMap[$noRumah];

                Resident::updateOrCreate(
                    [
                        'nik' => $row['nik'],
                    ],
                    [
                        'house_id' => $houseId,
                        'no_kk' => $row['no_kk'],
                        'nama_lengkap' => $row['nama_lengkap'],
                        'jenis_kelamin' => $row['jenis_kelamin'],
                        'tempat_lahir' => $row['tempat_lahir'],
                        'tanggal_lahir' => $row['tanggal_lahir'],
                        'agama' => $row['agama'],
                        'pekerjaan' => $row['pekerjaan'],
                        'status_keluarga' => $row['status_keluarga'],
                        'status_kependudukan' => $row['status_kependudukan'],
                        'no_wa' => $row['no_wa'],
                        'status_warga' => 'aktif',
                    ]
                );

                $residentsCount++;
            }

            return [
                'houses_count' => $housesCount,
                'residents_count' => $residentsCount,
            ];
        });
    }
}
