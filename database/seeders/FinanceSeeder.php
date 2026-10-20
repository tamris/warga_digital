<?php

namespace Database\Seeders;

use App\Models\CashLedger;
use App\Models\FeeSetting;
use App\Models\House;
use App\Models\HouseDue;
use App\Models\Rt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rts = Rt::orderBy('nomor_rt')->get();

        // 1. Seed Master Tarif Iuran Bulanan untuk setiap RT
        foreach ($rts as $rt) {
            FeeSetting::updateOrCreate(
                ['rt_id' => $rt->id, 'kategori_tarif' => 'standar'],
                ['nominal' => 50000, 'keterangan' => 'Iuran standar hunian (kebersihan, sampah & ronda)']
            );
            FeeSetting::updateOrCreate(
                ['rt_id' => $rt->id, 'kategori_tarif' => 'usaha'],
                ['nominal' => 100000, 'keterangan' => 'Tarif ruko, warung, toko atau tempat usaha']
            );
            FeeSetting::updateOrCreate(
                ['rt_id' => $rt->id, 'kategori_tarif' => 'kosong'],
                ['nominal' => 20000, 'keterangan' => 'Tarif pemeliharaan kavling / rumah kosong']
            );
        }

        // Ambil bendahara & admin
        $adminRw = User::where('username', 'admin_rw')->first();
        $bendaharaRw = User::where('username', 'bendahara_rw')->first() ?? $adminRw;
        $bendaharaRt1 = User::where('username', 'bendahara_rt01')->first();

        // 2. Seed Riwayat Iuran Rumah untuk Tahun 2026 (RT 001)
        $housesRt1 = House::where('rt_id', $rts->first()->id)->with('headOfFamily')->get();
        $currentYear = 2026;

        foreach ($housesRt1 as $index => $house) {
            $feeNominal = match ($house->kategori_tarif) {
                'usaha' => 100000,
                'kosong' => 20000,
                default => 50000,
            };

            $headName = $house->headOfFamily?->nama_lengkap ?? 'Warga RT 01';

            // Simulasikan pelunasan Januari - September 2026
            $monthsPaid = $index === 0 ? 9 : ($index === 1 ? 8 : 6);

            for ($m = 1; $m <= $monthsPaid; $m++) {
                $payDate = Carbon::create($currentYear, $m, rand(5, 15))->toDateString();
                $due = HouseDue::updateOrCreate(
                    [
                        'house_id' => $house->id,
                        'bulan' => $m,
                        'tahun' => $currentYear,
                    ],
                    [
                        'rt_id' => $house->rt_id,
                        'nominal' => $feeNominal,
                        'status' => 'lunas',
                        'tanggal_bayar' => $payDate,
                        'metode_bayar' => $m % 3 === 0 ? 'transfer' : 'tunai',
                        'nama_pembayar' => $headName,
                        'received_by_user_id' => $bendaharaRt1?->id,
                        'catatan' => 'Lunas tepat waktu',
                    ]
                );

                // Buat baris buku kas masuk otomatis
                CashLedger::updateOrCreate(
                    ['house_due_id' => $due->id],
                    [
                        'rt_id' => $house->rt_id,
                        'user_id' => $bendaharaRt1?->id ?? 1,
                        'jenis_transaksi' => 'masuk',
                        'kategori' => 'iuran_warga',
                        'nominal' => $feeNominal,
                        'tanggal' => $payDate,
                        'keterangan' => "Iuran {$due->nama_bulan} {$currentYear} - {$house->nomor_rumah} ({$headName})",
                    ]
                );
            }
        }

        // 3. Seed Pengeluaran Kas Operasional RT 01
        if ($bendaharaRt1) {
            $rt1Id = $rts->first()->id;

            $expenses = [
                ['kategori' => 'gaji_keamanan', 'nominal' => 600000, 'tgl' => '2026-09-01', 'ket' => 'Honorarium 2 petugas ronda malam RT 01 bulan September'],
                ['kategori' => 'angkut_sampah', 'nominal' => 350000, 'tgl' => '2026-09-05', 'ket' => 'Retribusi petugas angkutan sampah lingkungan RT 01'],
                ['kategori' => 'lampu_jalan', 'nominal' => 175000, 'tgl' => '2026-09-12', 'ket' => 'Penggantian 3 bohlam LED jalan gang Blok A'],
                ['kategori' => 'setor_ke_rw', 'nominal' => 300000, 'tgl' => '2026-09-20', 'ket' => 'Setoran kas wajib bulanan RT 01 ke Kas Induk RW 01'],
                ['kategori' => 'gaji_keamanan', 'nominal' => 600000, 'tgl' => '2026-10-01', 'ket' => 'Honorarium 2 petugas ronda malam RT 01 bulan Oktober'],
                ['kategori' => 'angkut_sampah', 'nominal' => 350000, 'tgl' => '2026-10-05', 'ket' => 'Retribusi angkut sampah lingkungan RT 01 bulan Oktober'],
                ['kategori' => 'operasional_rt', 'nominal' => 85000, 'tgl' => '2026-10-10', 'ket' => 'Pembelian kertas HVS & tinta stempel pengurus RT'],
            ];

            foreach ($expenses as $exp) {
                CashLedger::updateOrCreate(
                    [
                        'rt_id' => $rt1Id,
                        'tanggal' => $exp['tgl'],
                        'keterangan' => $exp['ket'],
                    ],
                    [
                        'user_id' => $bendaharaRt1->id,
                        'jenis_transaksi' => 'keluar',
                        'kategori' => $exp['kategori'],
                        'nominal' => $exp['nominal'],
                    ]
                );
            }
        }

        // 4. Seed Kas Induk RW 01 (rt_id = NULL)
        $rwExpenses = [
            ['jenis' => 'masuk', 'kategori' => 'setoran_rt', 'nominal' => 300000, 'tgl' => '2026-09-20', 'ket' => 'Penerimaan setoran wajib kas bulanan dari RT 001'],
            ['jenis' => 'masuk', 'kategori' => 'setoran_rt', 'nominal' => 300000, 'tgl' => '2026-09-22', 'ket' => 'Penerimaan setoran wajib kas bulanan dari RT 002'],
            ['jenis' => 'masuk', 'kategori' => 'bantuan_pemerintah', 'nominal' => 2500000, 'tgl' => '2026-09-25', 'ket' => 'Bantuan dana operasional lingkungan dari Kelurahan Slawi Kulon'],
            ['jenis' => 'keluar', 'kategori' => 'acara_lingkungan', 'nominal' => 850000, 'tgl' => '2026-09-28', 'ket' => 'Konsumsi & perlengkapan rapat koordinasi pengurus RW dan ketua RT se-RW 01'],
            ['jenis' => 'keluar', 'kategori' => 'kas_sosial', 'nominal' => 500000, 'tgl' => '2026-10-08', 'ket' => 'Santunan duka cita warga lingkungan RW 01'],
            ['jenis' => 'keluar', 'kategori' => 'operasional_rw', 'nominal' => 200000, 'tgl' => '2026-10-12', 'ket' => 'Biaya perawatan sound system & inventaris balai warga RW 01'],
        ];

        foreach ($rwExpenses as $rwExp) {
            CashLedger::updateOrCreate(
                [
                    'rt_id' => null,
                    'tanggal' => $rwExp['tgl'],
                    'keterangan' => $rwExp['ket'],
                ],
                [
                    'user_id' => $bendaharaRw->id,
                    'jenis_transaksi' => $rwExp['jenis'],
                    'kategori' => $rwExp['kategori'],
                    'nominal' => $rwExp['nominal'],
                ]
            );
        }
    }
}
