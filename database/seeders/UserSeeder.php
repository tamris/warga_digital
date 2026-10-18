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
        // 1. Data Master RT (RT 001 - RT 004)
        $rt1 = Rt::updateOrCreate(
            ['nomor_rt' => '001'],
            [
                'nama_ketua' => 'AGUNG JATMIKO',
                'no_wa_ketua' => '081234567801',
            ]
        );

        $rt2 = Rt::updateOrCreate(
            ['nomor_rt' => '002'],
            [
                'nama_ketua' => 'DENY',
                'no_wa_ketua' => '081234567802',
            ]
        );

        $rt3 = Rt::updateOrCreate(
            ['nomor_rt' => '003'],
            [
                'nama_ketua' => 'MUKHID',
                'no_wa_ketua' => '081234567803',
            ]
        );

        $rt4 = Rt::updateOrCreate(
            ['nomor_rt' => '004'],
            [
                'nama_ketua' => 'SUBANDI',
                'no_wa_ketua' => '081234567804',
            ]
        );

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

        // 7. Akun Operator Demo Tambahan (Sekretaris & Bendahara RT 001)
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
                'nama' => 'Sri Wahyuni (Bendahara)',
                'password' => Hash::make('password'),
                'role' => 'bendahara_rt',
                'no_wa' => '081234567806',
            ]
        );
    }
}
