<?php

namespace Database\Seeders;

use App\Models\Rt;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Data Master RT (Cek keberadaan RT yang sudah ada agar tidak duplikat)
        $findRt = function (string $preferredNumber, array $aliases, string $namaKetua, string $noWaKetua): Rt {
            $rt = Rt::whereIn('nomor_rt', array_merge([$preferredNumber], $aliases))->first();

            if ($rt) {
                $rt->update([
                    'nama_ketua' => $namaKetua,
                    'no_wa_ketua' => $noWaKetua,
                ]);

                return $rt;
            }

            return Rt::create([
                'nomor_rt' => $preferredNumber,
                'nama_ketua' => $namaKetua,
                'no_wa_ketua' => $noWaKetua,
            ]);
        };

        $rt1 = $findRt('001', ['01', '1'], 'AGUNG JATMIKO', '081234567801');
        $rt2 = $findRt('002', ['02', '2'], 'DENY', '081234567802');
        $rt3 = $findRt('003', ['03', '3'], 'MUKHID', '081234567803');
        $rt4 = $findRt('004', ['04', '4'], 'SUBANDI', '081234567804');

        // 2. Akun Admin RW: AMAT MAJA
        User::updateOrCreate(
            ['username' => 'admin_rw'],
            [
                'rt_id' => null,
                'nama' => 'AMAT MAJA',
                'password' => Hash::make('password'),
                'role' => 'admin_rw',
                'no_wa' => '081198765432',
            ]
        );

        // 3. Akun Ketua RT 01: AGUNG JATMIKO
        User::updateOrCreate(
            ['username' => 'ketua_rt01'],
            [
                'rt_id' => $rt1->id,
                'nama' => 'AGUNG JATMIKO',
                'password' => Hash::make('password'),
                'role' => 'ketua_rt',
                'no_wa' => '081234567801',
            ]
        );

        // 4. Akun Ketua RT 02: DENY
        User::updateOrCreate(
            ['username' => 'ketua_rt02'],
            [
                'rt_id' => $rt2->id,
                'nama' => 'DENY',
                'password' => Hash::make('password'),
                'role' => 'ketua_rt',
                'no_wa' => '081234567802',
            ]
        );

        // 5. Akun Ketua RT 03: MUKHID
        User::updateOrCreate(
            ['username' => 'ketua_rt03'],
            [
                'rt_id' => $rt3->id,
                'nama' => 'MUKHID',
                'password' => Hash::make('password'),
                'role' => 'ketua_rt',
                'no_wa' => '081234567803',
            ]
        );

        // 6. Akun Ketua RT 04: SUBANDI
        User::updateOrCreate(
            ['username' => 'ketua_rt04'],
            [
                'rt_id' => $rt4->id,
                'nama' => 'SUBANDI',
                'password' => Hash::make('password'),
                'role' => 'ketua_rt',
                'no_wa' => '081234567804',
            ]
        );

        // 7. Akun Pengurus RW Tambahan (Sekretaris & Bendahara RW)
        User::updateOrCreate(
            ['username' => 'sekretaris_rw'],
            [
                'rt_id' => null,
                'nama' => 'BAMBANG SUTRISNO',
                'password' => Hash::make('password'),
                'role' => 'sekretaris_rw',
                'no_wa' => '081198765433',
            ]
        );

        User::updateOrCreate(
            ['username' => 'bendahara_rw'],
            [
                'rt_id' => null,
                'nama' => 'HJ. ENDANG SUPRAPTI',
                'password' => Hash::make('password'),
                'role' => 'bendahara_rw',
                'no_wa' => '081198765434',
            ]
        );

        // 8. Akun Operator Demo Tambahan (Sekretaris & Bendahara RT 001 - 004)
        User::updateOrCreate(
            ['username' => 'sekretaris_rt01'],
            [
                'rt_id' => $rt1->id,
                'nama' => 'Indra Gunawan (Sekretaris)',
                'password' => Hash::make('password'),
                'role' => 'sekretaris_rt',
                'no_wa' => '081234567805',
            ]
        );

        User::updateOrCreate(
            ['username' => 'bendahara_rt01'],
            [
                'rt_id' => $rt1->id,
                'nama' => 'Sri Wahyuni (Bendahara RT 01)',
                'password' => Hash::make('password'),
                'role' => 'bendahara_rt',
                'no_wa' => '081234567806',
            ]
        );

        User::updateOrCreate(
            ['username' => 'bendahara_rt02'],
            [
                'rt_id' => $rt2->id,
                'nama' => 'Rina Marlina (Bendahara RT 02)',
                'password' => Hash::make('password'),
                'role' => 'bendahara_rt',
                'no_wa' => '081234567807',
            ]
        );

        User::updateOrCreate(
            ['username' => 'bendahara_rt03'],
            [
                'rt_id' => $rt3->id,
                'nama' => 'Siti Aminah (Bendahara RT 03)',
                'password' => Hash::make('password'),
                'role' => 'bendahara_rt',
                'no_wa' => '081234567808',
            ]
        );

        User::updateOrCreate(
            ['username' => 'bendahara_rt04'],
            [
                'rt_id' => $rt4->id,
                'nama' => 'Dewi Lestari (Bendahara RT 04)',
                'password' => Hash::make('password'),
                'role' => 'bendahara_rt',
                'no_wa' => '081234567809',
            ]
        );
    }
}
