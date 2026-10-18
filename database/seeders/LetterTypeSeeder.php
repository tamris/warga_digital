<?php

namespace Database\Seeders;

use App\Models\LetterType;
use Illuminate\Database\Seeder;

class LetterTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Pengantar Pembuatan / Perpanjangan KTP',
            'Pengantar Perubahan / Pembuatan Kartu Keluarga (KK)',
            'Pengantar Surat Keterangan Catatan Kepolisian (SKCK)',
            'Surat Keterangan Domisili Warga',
            'Surat Keterangan Kematian',
            'Surat Keterangan Usaha (SKU)',
            'Surat Pengantar Nikah (N1-N4)',
            'Surat Keterangan Tidak Mampu (SKTM)',
            'Surat Keterangan Pindah Domisili',
        ];

        foreach ($types as $type) {
            LetterType::firstOrCreate([
                'nama_keperluan' => $type,
            ], [
                'is_active' => true,
            ]);
        }
    }
}
