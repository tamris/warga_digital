<?php

namespace Database\Seeders;

use App\Models\House;
use App\Models\Resident;
use App\Models\Rt;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1 & 2. Seed Master Data RT & Akun Pengurus Sistem (AMAT MAJA & Ketua RT 01-04)
        $this->call(UserSeeder::class);
        $this->call(LetterTypeSeeder::class);

        // Data 5 Rumah per RT beserta Penghuninya
        $rts = Rt::orderBy('nomor_rt')->get();
        $nikCounter = 3271011000000000;
        $kkCounter = 3271012000000000;

        foreach ($rts as $rtIndex => $rt) {
            $rtCode = $rt->nomor_rt;

            // Rumah 1: Dihuni Pemilik - Standar (Keluarga lengkap dengan Lansia)
            $kk1 = (string) (++$kkCounter);
            $h1 = House::create([
                'rt_id' => $rt->id,
                'nomor_rumah' => "Blok {$rtCode}-A No. 01",
                'status_huni' => 'dihuni_pemilik',
                'kategori_tarif' => 'standar',
                'catatan' => 'Pagar hijau, ada pohon mangga di depan',
            ]);
            Resident::create([
                'house_id' => $h1->id,
                'nik' => (string) (++$nikCounter),
                'no_kk' => $kk1,
                'nama_lengkap' => "Suryadi Wibowo (RT {$rtCode})",
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1975-04-12',
                'agama' => 'Islam',
                'pekerjaan' => 'PNS',
                'status_keluarga' => 'kepala_keluarga',
                'status_kependudukan' => 'ktp_setempat',
                'no_wa' => '0812111100'.$rtIndex.'1',
                'status_warga' => 'aktif',
            ]);
            Resident::create([
                'house_id' => $h1->id,
                'nik' => (string) (++$nikCounter),
                'no_kk' => $kk1,
                'nama_lengkap' => "Endang Lestari (RT {$rtCode})",
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1978-08-20',
                'agama' => 'Islam',
                'pekerjaan' => 'Guru',
                'status_keluarga' => 'istri',
                'status_kependudukan' => 'ktp_setempat',
                'no_wa' => '0812111100'.$rtIndex.'2',
                'status_warga' => 'aktif',
            ]);
            Resident::create([
                'house_id' => $h1->id,
                'nik' => (string) (++$nikCounter),
                'no_kk' => $kk1,
                'nama_lengkap' => "Dimas Prasetyo (RT {$rtCode})",
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '2005-01-15',
                'agama' => 'Islam',
                'pekerjaan' => 'Pelajar/Mahasiswa',
                'status_keluarga' => 'anak',
                'status_kependudukan' => 'ktp_setempat',
                'no_wa' => null,
                'status_warga' => 'aktif',
            ]);
            // Lansia (usia > 60 tahun untuk uji filter lansia)
            Resident::create([
                'house_id' => $h1->id,
                'nik' => (string) (++$nikCounter),
                'no_kk' => $kk1,
                'nama_lengkap' => "Mbah Kartorejo (RT {$rtCode})",
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Solo',
                'tanggal_lahir' => '1952-03-10', // Usia ~74 thn (Lansia)
                'agama' => 'Islam',
                'pekerjaan' => 'Pensiunan',
                'status_keluarga' => 'famili_lain',
                'status_kependudukan' => 'ktp_setempat',
                'no_wa' => null,
                'status_warga' => 'aktif',
            ]);

            // Rumah 2: Tempat Usaha (Warung/Kelontong)
            $kk2 = (string) (++$kkCounter);
            $h2 = House::create([
                'rt_id' => $rt->id,
                'nomor_rumah' => "Blok {$rtCode}-A No. 02",
                'status_huni' => 'dihuni_pemilik',
                'kategori_tarif' => 'usaha',
                'catatan' => 'Toko Kelontong Berkah & Fotocopy',
            ]);
            Resident::create([
                'house_id' => $h2->id,
                'nik' => (string) (++$nikCounter),
                'no_kk' => $kk2,
                'nama_lengkap' => "Hendra Gunawan (RT {$rtCode})",
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1980-06-18',
                'agama' => 'Kristen',
                'pekerjaan' => 'Wiraswasta',
                'status_keluarga' => 'kepala_keluarga',
                'status_kependudukan' => 'ktp_setempat',
                'no_wa' => '0813222200'.$rtIndex.'1',
                'status_warga' => 'aktif',
            ]);
            Resident::create([
                'house_id' => $h2->id,
                'nik' => (string) (++$nikCounter),
                'no_kk' => $kk2,
                'nama_lengkap' => "Maria Setyowati (RT {$rtCode})",
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1983-11-22',
                'agama' => 'Katolik',
                'pekerjaan' => 'Wiraswasta',
                'status_keluarga' => 'istri',
                'status_kependudukan' => 'ktp_setempat',
                'no_wa' => '0813222200'.$rtIndex.'2',
                'status_warga' => 'aktif',
            ]);

            // Rumah 3: Kontrak / Domisili Luar (Untuk uji filter Domisili Luar)
            $kk3 = (string) (++$kkCounter);
            $h3 = House::create([
                'rt_id' => $rt->id,
                'nomor_rumah' => "Blok {$rtCode}-B No. 08",
                'status_huni' => 'kontrak',
                'kategori_tarif' => 'standar',
                'catatan' => 'Penyewa kontrak 2 tahun, mobil Avanza silver',
            ]);
            Resident::create([
                'house_id' => $h3->id,
                'nik' => (string) (++$nikCounter),
                'no_kk' => $kk3,
                'nama_lengkap' => "Rahmat Hidayat (RT {$rtCode})",
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '1990-09-05',
                'agama' => 'Islam',
                'pekerjaan' => 'Karyawan Swasta',
                'status_keluarga' => 'kepala_keluarga',
                'status_kependudukan' => 'domisili_luar', // KTP Luar
                'no_wa' => '0815333300'.$rtIndex.'1',
                'status_warga' => 'aktif',
            ]);
            Resident::create([
                'house_id' => $h3->id,
                'nik' => (string) (++$nikCounter),
                'no_kk' => $kk3,
                'nama_lengkap' => "Siti Nurhaliza (RT {$rtCode})",
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Padang',
                'tanggal_lahir' => '1993-12-14',
                'agama' => 'Islam',
                'pekerjaan' => 'Ibu Rumah Tangga',
                'status_keluarga' => 'istri',
                'status_kependudukan' => 'domisili_luar',
                'no_wa' => null,
                'status_warga' => 'aktif',
            ]);

            // Rumah 4: Rumah dengan riwayat Warga Pindah (Untuk uji filter Riwayat Pindah)
            $kk4 = (string) (++$kkCounter);
            $h4 = House::create([
                'rt_id' => $rt->id,
                'nomor_rumah' => "Blok {$rtCode}-B No. 09",
                'status_huni' => 'dihuni_pemilik',
                'kategori_tarif' => 'standar',
                'catatan' => 'Pernah ada anggota keluarga mutasi pindah luar kota',
            ]);
            Resident::create([
                'house_id' => $h4->id,
                'nik' => (string) (++$nikCounter),
                'no_kk' => $kk4,
                'nama_lengkap' => "Drs. Iwan Kusuma (RT {$rtCode})",
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Cirebon',
                'tanggal_lahir' => '1968-02-14',
                'agama' => 'Islam',
                'pekerjaan' => 'Dosen',
                'status_keluarga' => 'kepala_keluarga',
                'status_kependudukan' => 'ktp_setempat',
                'no_wa' => '0816444400'.$rtIndex.'1',
                'status_warga' => 'aktif',
            ]);
            // Anggota yang berstatus 'pindah'
            Resident::create([
                'house_id' => $h4->id,
                'nik' => (string) (++$nikCounter),
                'no_kk' => $kk4,
                'nama_lengkap' => "Bagus Pratama (Pindah) (RT {$rtCode})",
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1998-05-19',
                'agama' => 'Islam',
                'pekerjaan' => 'Programmer',
                'status_keluarga' => 'anak',
                'status_kependudukan' => 'ktp_setempat',
                'no_wa' => null,
                'status_warga' => 'pindah', // Status PINDAH
            ]);

            // Rumah 5: Rumah Kosong
            House::create([
                'rt_id' => $rt->id,
                'nomor_rumah' => "Blok {$rtCode}-C No. 12",
                'status_huni' => 'kosong',
                'kategori_tarif' => 'kosong',
                'catatan' => 'Rumah kosong belum berpenghuni, kunci dititipkan ke Pak RT',
            ]);
        }
    }
}
