<?php

namespace Database\Seeders;

use App\Models\House;
use App\Models\HouseSocialProfile;
use App\Models\Rt;
use App\Models\SocialAidProgram;
use App\Models\SocialAidQuota;
use App\Models\SocialAidRecipient;
use App\Models\User;
use Illuminate\Database\Seeder;

class SocialAidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin_rw')->first() ?? User::first();
        $houses = House::with(['residents', 'rt'])->get();

        if ($houses->isEmpty()) {
            return;
        }

        // 1. Buat Profil Sosial Ekonomi untuk Setiap Unit Rumah
        foreach ($houses as $index => $house) {
            $head = $house->headOfFamily;
            $hasLansia = $house->residents->contains(fn ($r) => $r->status_warga === 'aktif' && $r->tanggal_lahir && $r->tanggal_lahir->diffInYears(now()) >= 60);

            // Variasikan status ekonomi secara realistis
            $modulo = $index % 5;

            if ($modulo === 0) {
                // Miskin Ekstrem (Prioritas Tertinggi: Skor >= 80)
                $penghasilan = 'di_bawah_1_5_jt';
                $dtks = ($index % 2 === 0) ? 'belum_terdaftar' : 'terdaftar';
                $bantuan = ($dtks === 'terdaftar') ? ['PKH', 'BPNT'] : null;
                $janda = true;
                $disabilitas = true;
                $rumahTidakLayak = true;
                $catatan = 'Keluarga buruh lepas harian, lantai rumah masih semen kasar, kepala keluarga tunggal mengurus anggota disabilitas.';
            } elseif ($modulo === 1) {
                // Rentan Miskin (Skor 55 - 79)
                $penghasilan = 'di_bawah_1_5_jt';
                $dtks = ($index % 3 === 0) ? 'belum_terdaftar' : 'terdaftar';
                $bantuan = ($dtks === 'terdaftar') ? ['BPNT'] : null;
                $janda = false;
                $disabilitas = ($index % 2 === 0);
                $rumahTidakLayak = false;
                $catatan = 'Pedagang kecil keliling, penghasilan harian tidak menentu.';
            } elseif ($modulo === 2) {
                // Rentan Miskin / Lansia Tunggal (Skor 55 - 79)
                $penghasilan = '1_5_sampai_3_jt';
                $dtks = 'proses_usulan';
                $bantuan = null;
                $janda = true;
                $disabilitas = true;
                $rumahTidakLayak = true;
                $catatan = 'Kepala keluarga janda lansia, rumah semi permanen, hidup dari bantuan anak dan tetangga sekitar.';
            } elseif ($modulo === 3) {
                // Menengah (Skor 30 - 54)
                $penghasilan = '1_5_sampai_3_jt';
                $dtks = 'belum_terdaftar';
                $bantuan = null;
                $janda = false;
                $disabilitas = false;
                $rumahTidakLayak = false;
                $catatan = 'Karyawan swasta industri lokal, ekonomi stabil pas-pasan.';
            } else {
                // Mampu (Skor < 30)
                $penghasilan = 'di_atas_3_jt';
                $dtks = 'belum_terdaftar';
                $bantuan = null;
                $janda = false;
                $disabilitas = false;
                $rumahTidakLayak = false;
                $catatan = 'Keluarga berkecukupan, rumah permanen bertingkat.';
            }

            $profile = HouseSocialProfile::updateOrCreate(
                ['house_id' => $house->id],
                [
                    'rentang_penghasilan' => $penghasilan,
                    'status_dtks' => $dtks,
                    'bantuan_rutin_pemerintah' => $bantuan,
                    'is_kepala_keluarga_janda_duda' => $janda,
                    'ada_anggota_disabilitas_sakit_kronis' => $disabilitas,
                    'kondisi_rumah_tidak_layak' => $rumahTidakLayak,
                    'catatan_kondisi' => $catatan,
                    'updated_by' => $admin?->id,
                ]
            );

            // Hitung & simpan skor serta status ekonomi otomatis
            $profile->skor_prioritas = $profile->calculatePriorityScore();
            $profile->kategori_ekonomi = $profile->determineKategoriEkonomi($profile->skor_prioritas);
            $profile->saveQuietly();
        }

        // 2. Buat Program Bantuan Sosial Contoh
        $program = SocialAidProgram::firstOrCreate(
            ['nama_program' => 'Bantuan Pangan Beras Cadangan Pemerintah 10 Kg - Oktober 2026'],
            [
                'sumber_dana' => 'pemerintah_pusat',
                'jenis_bantuan' => 'sembako_barang',
                'detail_bantuan' => 'Beras CBP 10 Kg + Minyak Goreng 2 Liter',
                'total_kuota' => 12,
                'tanggal_penyaluran' => now()->addDays(3)->toDateString(),
                'lokasi_pengambilan' => 'Balai Pertemuan RW 01 / Pos Ronda RT Setempat',
                'status_program' => 'pendataan_rt',
                'created_by' => $admin->id,
            ]
        );

        // 3. Alokasikan Kuota ke Masing-Masing RT (Contoh: 3 KK per RT, Total 12 KK)
        $rts = Rt::orderBy('nomor_rt')->get();
        foreach ($rts as $rt) {
            SocialAidQuota::updateOrCreate(
                [
                    'program_id' => $program->id,
                    'rt_id' => $rt->id,
                ],
                [
                    'jumlah_kuota' => 3,
                ]
            );

            // Cari 2 rumah dengan skor prioritas tertinggi di RT ini untuk dijadikan usulan contoh
            $topHouses = House::where('rt_id', $rt->id)
                ->whereHas('socialProfile', fn ($q) => $q->where('skor_prioritas', '>', 0))
                ->with(['socialProfile', 'headOfFamily'])
                ->get()
                ->sortByDesc(fn ($h) => $h->socialProfile?->skor_prioritas ?? 0)
                ->take(2);

            $kuponIdx = 1;
            foreach ($topHouses as $candidateHouse) {
                $headResident = $candidateHouse->headOfFamily ?? $candidateHouse->residents->first();
                if (! $headResident) {
                    continue;
                }

                $rtStr = str_pad((string) ((int) $rt->nomor_rt), 2, '0', STR_PAD_LEFT);
                $kuponStr = "RT{$rtStr}-".str_pad((string) $kuponIdx, 3, '0', STR_PAD_LEFT);

                SocialAidRecipient::updateOrCreate(
                    [
                        'program_id' => $program->id,
                        'house_id' => $candidateHouse->id,
                    ],
                    [
                        'rt_id' => $rt->id,
                        'resident_id' => $headResident->id,
                        'nomor_urut_kupon' => $kuponStr,
                        'status_usulan' => 'diusulkan_rt',
                        'status_penyerahan' => 'belum_diambil',
                    ]
                );

                $kuponIdx++;
            }
        }
    }
}
