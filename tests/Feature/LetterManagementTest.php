<?php

namespace Tests\Feature;

use App\Livewire\Letters\LetterCreate;
use App\Livewire\Letters\LetterList;
use App\Models\Letter;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LetterManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_user_can_view_letters_register(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        Livewire::test(LetterList::class)
            ->assertOk()
            ->assertSee('Register Surat Pengantar RT/RW');
    }

    public function test_ketua_rt_can_create_letter_and_generates_correct_letter_number(): void
    {
        $ketuaRt1 = User::where('username', 'ketua_rt01')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $resident = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $rt1->id))
            ->where('status_warga', 'aktif')
            ->firstOrFail();

        $this->actingAs($ketuaRt1);

        $currentYear = (int) date('Y');
        $currentMonthRoman = Letter::getRomanMonth((int) date('n'));
        $expectedNumber = "001/SP/RT.01/RW.01/{$currentMonthRoman}/{$currentYear}";

        Livewire::test(LetterCreate::class)
            ->assertOk()
            ->assertSet('selectedRtId', $rt1->id)
            ->call('selectResident', $resident->id)
            ->set('selectedJenisKeperluan', 'Pengantar Pembuatan / Perpanjangan KTP')
            ->set('keteranganKeperluan', 'KTP lama hilang')
            ->set('namaKetuaRt', 'AGUNG JATMIKO')
            ->call('saveLetter')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('letters', [
            'rt_id' => $rt1->id,
            'resident_id' => $resident->id,
            'nomor_urut' => 1,
            'nomor_surat' => $expectedNumber,
            'jenis_keperluan' => 'Pengantar Pembuatan / Perpanjangan KTP',
            'keterangan_keperluan' => 'KTP lama hilang',
            'snapshot_nama' => strtoupper($resident->nama_lengkap),
            'snapshot_nik' => $resident->nik,
            'status' => 'menunggu_rw',
        ]);
    }

    public function test_sequential_letter_number_per_rt(): void
    {
        $ketuaRt1 = User::where('username', 'ketua_rt01')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $resident = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $rt1->id))->firstOrFail();

        $this->actingAs($ketuaRt1);

        // Surat 1
        Livewire::test(LetterCreate::class)
            ->call('selectResident', $resident->id)
            ->set('selectedJenisKeperluan', 'Pengantar SKCK')
            ->set('namaKetuaRt', 'AGUNG JATMIKO')
            ->call('saveLetter')
            ->assertHasNoErrors();

        // Surat 2
        Livewire::test(LetterCreate::class)
            ->call('selectResident', $resident->id)
            ->set('selectedJenisKeperluan', 'Surat Keterangan Domisili Warga')
            ->set('namaKetuaRt', 'AGUNG JATMIKO')
            ->call('saveLetter')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('letters', [
            'rt_id' => $rt1->id,
            'nomor_urut' => 2,
        ]);
    }

    public function test_ketua_rt_can_cancel_letter_with_reason(): void
    {
        $ketuaRt1 = User::where('username', 'ketua_rt01')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $resident = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $rt1->id))->firstOrFail();

        $letter = Letter::create([
            'rt_id' => $rt1->id,
            'resident_id' => $resident->id,
            'created_by_user_id' => $ketuaRt1->id,
            'nomor_urut' => 1,
            'nomor_surat' => '001/SP/RT.01/RW.01/X/2026',
            'tahun' => 2026,
            'jenis_keperluan' => 'Pengantar SKCK',
            'snapshot_nama' => $resident->nama_lengkap,
            'snapshot_nik' => $resident->nik,
            'snapshot_no_kk' => $resident->no_kk,
            'snapshot_ttl' => 'Surabaya, 12 April 1975',
            'snapshot_jenis_kelamin' => 'L',
            'snapshot_agama' => 'Islam',
            'snapshot_pekerjaan' => 'PNS',
            'snapshot_alamat' => 'Blok 01-A No. 01',
            'nama_ketua_rt' => 'AGUNG JATMIKO',
            'tanggal_surat' => '2026-10-18',
            'status' => 'terbit',
        ]);

        $this->actingAs($ketuaRt1);

        Livewire::test(LetterList::class)
            ->call('openCancelModal', $letter->id)
            ->set('cancelReason', 'Warga salah memberikan tujuan permohonan')
            ->call('confirmCancelLetter')
            ->assertHasNoErrors()
            ->assertSet('showCancelModal', false);

        $this->assertDatabaseHas('letters', [
            'id' => $letter->id,
            'status' => 'dibatalkan',
            'alasan_batal' => 'Warga salah memberikan tujuan permohonan',
        ]);
    }

    public function test_bendahara_cannot_access_create_letter(): void
    {
        $bendahara = User::where('username', 'bendahara_rt01')->firstOrFail();

        $this->actingAs($bendahara);

        Livewire::test(LetterCreate::class)
            ->assertForbidden();
    }

    public function test_letter_print_and_download_endpoints(): void
    {
        $ketuaRt1 = User::where('username', 'ketua_rt01')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $resident = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $rt1->id))->firstOrFail();

        $letter = Letter::create([
            'rt_id' => $rt1->id,
            'resident_id' => $resident->id,
            'created_by_user_id' => $ketuaRt1->id,
            'nomor_urut' => 1,
            'nomor_surat' => '001/SP/RT.01/RW.01/X/2026',
            'tahun' => 2026,
            'jenis_keperluan' => 'Pengantar SKCK',
            'snapshot_nama' => $resident->nama_lengkap,
            'snapshot_nik' => $resident->nik,
            'snapshot_no_kk' => $resident->no_kk,
            'snapshot_ttl' => 'Surabaya, 12 April 1975',
            'snapshot_jenis_kelamin' => 'L',
            'snapshot_agama' => 'Islam',
            'snapshot_pekerjaan' => 'PNS',
            'snapshot_alamat' => 'Blok 01-A No. 01',
            'nama_ketua_rt' => 'AGUNG JATMIKO',
            'tanggal_surat' => '2026-10-18',
            'status' => 'terbit',
        ]);

        $this->actingAs($ketuaRt1);

        $this->get(route('letters.print', $letter->id))
            ->assertOk()
            ->assertSee('SURAT PENGANTAR')
            ->assertSee($letter->nomor_surat)
            ->assertSee('18 Oktober 2026')
            ->assertDontSee('October');

        $this->get(route('letters.download', $letter->id))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_public_verification_page_for_valid_and_cancelled_letters(): void
    {
        $ketuaRt1 = User::where('username', 'ketua_rt01')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $resident = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $rt1->id))->firstOrFail();

        $letter = Letter::create([
            'rt_id' => $rt1->id,
            'resident_id' => $resident->id,
            'created_by_user_id' => $ketuaRt1->id,
            'nomor_urut' => 1,
            'nomor_surat' => '001/SP/RT.01/RW.01/X/2026',
            'tahun' => 2026,
            'jenis_keperluan' => 'Pengantar SKCK',
            'snapshot_nama' => $resident->nama_lengkap,
            'snapshot_nik' => $resident->nik,
            'snapshot_no_kk' => $resident->no_kk,
            'snapshot_ttl' => 'Surabaya, 12 April 1975',
            'snapshot_jenis_kelamin' => 'L',
            'snapshot_agama' => 'Islam',
            'snapshot_pekerjaan' => 'PNS',
            'snapshot_alamat' => 'Blok 01-A No. 01',
            'nama_ketua_rt' => 'AGUNG JATMIKO',
            'tanggal_surat' => '2026-10-18',
            'status' => 'terbit',
        ]);

        $this->assertNotNull($letter->verification_code);

        // Akses publik tanpa autentikasi
        $this->get(route('letters.verify', $letter->verification_code))
            ->assertOk()
            ->assertSee('Validasi Keaslian Dokumen')
            ->assertSee('Terverifikasi Sah')
            ->assertSee($letter->nomor_surat)
            ->assertSee(strtoupper($letter->snapshot_nama));

        // Uji saat surat berstatus menunggu_rw
        $letter->update([
            'status' => 'menunggu_rw',
        ]);

        $this->get(route('letters.verify', $letter->verification_code))
            ->assertOk()
            ->assertSee('Pending')
            ->assertSee('Pending - Menunggu Persetujuan & TTE Ketua RW 01', false);

        // Uji saat surat ditolak oleh Ketua RW
        $letter->update([
            'status' => 'ditolak',
            'alasan_tolak' => 'Lampiran Kartu Keluarga belum diperbarui',
        ]);

        $this->get(route('letters.verify', $letter->verification_code))
            ->assertOk()
            ->assertSee('Pengesahan Ditolak')
            ->assertSee('Surat Ditolak oleh Ketua RW 01')
            ->assertSee('Lampiran Kartu Keluarga belum diperbarui');

        // Uji saat surat dibatalkan
        $letter->update([
            'status' => 'dibatalkan',
            'alasan_batal' => 'Kekeliruan data pemohon',
        ]);

        $this->get(route('letters.verify', $letter->verification_code))
            ->assertOk()
            ->assertSee('Tidak Berlaku')
            ->assertSee('Surat Telah Dibatalkan')
            ->assertSee('Kekeliruan data pemohon')
            ->assertDontSee('Telah ditandatangani secara elektronik');

        // Uji dengan token tidak valid
        $this->get(route('letters.verify', 'invalid-token-code-12345'))
            ->assertOk()
            ->assertSee('Dokumen Tidak Ditemukan');
    }

    public function test_admin_rw_can_approve_pending_letter_with_tte(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $resident = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $rt1->id))->firstOrFail();

        $letter = Letter::create([
            'rt_id' => $rt1->id,
            'resident_id' => $resident->id,
            'created_by_user_id' => $admin->id,
            'nomor_urut' => 1,
            'nomor_surat' => '001/SP/RT.01/RW.01/X/2026',
            'tahun' => 2026,
            'jenis_keperluan' => 'Pengantar SKCK',
            'snapshot_nama' => $resident->nama_lengkap,
            'snapshot_nik' => $resident->nik,
            'snapshot_no_kk' => $resident->no_kk,
            'snapshot_ttl' => 'Tegal, 1 Januari 1990',
            'snapshot_jenis_kelamin' => 'L',
            'snapshot_agama' => 'Islam',
            'snapshot_pekerjaan' => 'Wiraswasta',
            'snapshot_alamat' => 'Slawi Kulon RT 01 RW 01',
            'nama_ketua_rt' => 'AGUNG JATMIKO',
            'tanggal_surat' => '2026-10-18',
            'status' => 'menunggu_rw',
            'signed_by_rt_at' => now(),
        ]);

        $this->actingAs($admin);

        Livewire::test(LetterList::class)
            ->call('approveByRw', $letter->id)
            ->assertHasNoErrors();

        $letter->refresh();
        $this->assertEquals('terbit', $letter->status);
        $this->assertNotNull($letter->signed_by_rw_at);
    }

    public function test_admin_rw_can_open_and_confirm_approve_modal(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $resident = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $rt1->id))->firstOrFail();

        $letter = Letter::create([
            'rt_id' => $rt1->id,
            'resident_id' => $resident->id,
            'created_by_user_id' => $admin->id,
            'nomor_urut' => 1,
            'nomor_surat' => '001/SP/RT.01/RW.01/X/2026',
            'tahun' => 2026,
            'jenis_keperluan' => 'Pengantar SKCK',
            'snapshot_nama' => $resident->nama_lengkap,
            'snapshot_nik' => $resident->nik,
            'snapshot_no_kk' => $resident->no_kk,
            'snapshot_ttl' => 'Tegal, 1 Januari 1990',
            'snapshot_jenis_kelamin' => 'L',
            'snapshot_agama' => 'Islam',
            'snapshot_pekerjaan' => 'Wiraswasta',
            'snapshot_alamat' => 'Slawi Kulon RT 01 RW 01',
            'nama_ketua_rt' => 'AGUNG JATMIKO',
            'tanggal_surat' => '2026-10-18',
            'status' => 'menunggu_rw',
            'signed_by_rt_at' => now(),
        ]);

        $this->actingAs($admin);

        Livewire::test(LetterList::class)
            ->call('openApproveModal', $letter->id)
            ->assertSet('showApproveModal', true)
            ->assertSet('letterIdToApprove', $letter->id)
            ->assertSee($letter->nomor_surat)
            ->call('confirmApproveLetter')
            ->assertHasNoErrors()
            ->assertSet('showApproveModal', false);

        $letter->refresh();
        $this->assertEquals('terbit', $letter->status);
        $this->assertNotNull($letter->signed_by_rw_at);
    }

    public function test_admin_rw_can_bulk_approve_all_pending_letters(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $resident = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $rt1->id))->firstOrFail();

        for ($i = 1; $i <= 3; $i++) {
            Letter::create([
                'rt_id' => $rt1->id,
                'resident_id' => $resident->id,
                'created_by_user_id' => $admin->id,
                'nomor_urut' => $i,
                'nomor_surat' => sprintf('%03d/SP/RT.01/RW.01/X/2026', $i),
                'tahun' => 2026,
                'jenis_keperluan' => 'Pengantar SKCK',
                'snapshot_nama' => $resident->nama_lengkap,
                'snapshot_nik' => $resident->nik,
                'snapshot_no_kk' => $resident->no_kk,
                'snapshot_ttl' => 'Tegal, 1 Januari 1990',
                'snapshot_jenis_kelamin' => 'L',
                'snapshot_agama' => 'Islam',
                'snapshot_pekerjaan' => 'Wiraswasta',
                'snapshot_alamat' => 'Slawi Kulon RT 01 RW 01',
                'nama_ketua_rt' => 'AGUNG JATMIKO',
                'tanggal_surat' => '2026-10-18',
                'status' => 'menunggu_rw',
                'signed_by_rt_at' => now(),
            ]);
        }

        $this->actingAs($admin);

        Livewire::test(LetterList::class)
            ->call('openBulkApproveModal')
            ->assertSet('showBulkApproveModal', true)
            ->call('confirmBulkApprove')
            ->assertHasNoErrors()
            ->assertSet('showBulkApproveModal', false);

        $this->assertEquals(0, Letter::where('status', 'menunggu_rw')->count());
        $this->assertEquals(3, Letter::where('status', 'terbit')->count());
    }

    public function test_admin_rw_can_reject_letter_with_reason(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $resident = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $rt1->id))->firstOrFail();

        $letter = Letter::create([
            'rt_id' => $rt1->id,
            'resident_id' => $resident->id,
            'created_by_user_id' => $admin->id,
            'nomor_urut' => 1,
            'nomor_surat' => '001/SP/RT.01/RW.01/X/2026',
            'tahun' => 2026,
            'jenis_keperluan' => 'Pengantar SKCK',
            'snapshot_nama' => $resident->nama_lengkap,
            'snapshot_nik' => $resident->nik,
            'snapshot_no_kk' => $resident->no_kk,
            'snapshot_ttl' => 'Tegal, 1 Januari 1990',
            'snapshot_jenis_kelamin' => 'L',
            'snapshot_agama' => 'Islam',
            'snapshot_pekerjaan' => 'Wiraswasta',
            'snapshot_alamat' => 'Slawi Kulon RT 01 RW 01',
            'nama_ketua_rt' => 'AGUNG JATMIKO',
            'tanggal_surat' => '2026-10-18',
            'status' => 'menunggu_rw',
            'signed_by_rt_at' => now(),
        ]);

        $this->actingAs($admin);

        Livewire::test(LetterList::class)
            ->call('openRejectModal', $letter->id)
            ->assertSet('showRejectModal', true)
            ->set('rejectReason', 'Berkas KK belum diperbarui ke yang terbaru')
            ->call('confirmRejectLetter')
            ->assertHasNoErrors()
            ->assertSet('showRejectModal', false);

        $letter->refresh();
        $this->assertEquals('ditolak_rw', $letter->status);
        $this->assertEquals('Berkas KK belum diperbarui ke yang terbaru', $letter->alasan_tolak);
    }

    public function test_ketua_rt_cannot_approve_or_reject_letter(): void
    {
        $ketuaRt1 = User::where('username', 'ketua_rt01')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $resident = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $rt1->id))->firstOrFail();

        $letter = Letter::create([
            'rt_id' => $rt1->id,
            'resident_id' => $resident->id,
            'created_by_user_id' => $ketuaRt1->id,
            'nomor_urut' => 1,
            'nomor_surat' => '001/SP/RT.01/RW.01/X/2026',
            'tahun' => 2026,
            'jenis_keperluan' => 'Pengantar SKCK',
            'snapshot_nama' => $resident->nama_lengkap,
            'snapshot_nik' => $resident->nik,
            'snapshot_no_kk' => $resident->no_kk,
            'snapshot_ttl' => 'Tegal, 1 Januari 1990',
            'snapshot_jenis_kelamin' => 'L',
            'snapshot_agama' => 'Islam',
            'snapshot_pekerjaan' => 'Wiraswasta',
            'snapshot_alamat' => 'Slawi Kulon RT 01 RW 01',
            'nama_ketua_rt' => 'AGUNG JATMIKO',
            'tanggal_surat' => '2026-10-18',
            'status' => 'menunggu_rw',
            'signed_by_rt_at' => now(),
        ]);

        $this->actingAs($ketuaRt1);

        Livewire::test(LetterList::class)
            ->call('approveByRw', $letter->id)
            ->assertForbidden();

        Livewire::test(LetterList::class)
            ->call('openRejectModal', $letter->id)
            ->assertForbidden();
    }

    public function test_sekretaris_rt_creates_letter_with_status_menunggu_rt(): void
    {
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $sekretaris = User::firstOrCreate(
            ['username' => 'sekretaris_rt01'],
            [
                'name' => 'Sekretaris RT 01',
                'email' => 'sekretaris01@example.com',
                'password' => bcrypt('password'),
                'role' => 'sekretaris_rt',
                'rt_id' => $rt1->id,
            ]
        );
        $resident = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $rt1->id))->firstOrFail();

        $this->actingAs($sekretaris);

        Livewire::test(LetterCreate::class)
            ->call('selectResident', $resident->id)
            ->set('selectedJenisKeperluan', 'Pengantar SKCK')
            ->set('namaKetuaRt', 'AGUNG JATMIKO')
            ->call('saveLetter')
            ->assertHasNoErrors()
            ->assertRedirect(route('letters.index'));

        $letter = Letter::where('snapshot_nik', $resident->nik)->latest('id')->firstOrFail();
        $this->assertEquals('menunggu_rt', $letter->status);
        $this->assertNull($letter->signed_by_rt_at);
        $this->assertNull($letter->signed_by_rw_at);
    }

    public function test_ketua_rt_can_approve_menunggu_rt_letter_to_menunggu_rw(): void
    {
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $ketuaRt1 = User::where('username', 'ketua_rt01')->firstOrFail();
        $resident = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $rt1->id))->firstOrFail();

        $letter = Letter::create([
            'rt_id' => $rt1->id,
            'resident_id' => $resident->id,
            'created_by_user_id' => $ketuaRt1->id,
            'nomor_urut' => 10,
            'nomor_surat' => '010/SP/RT.01/RW.01/X/2026',
            'tahun' => 2026,
            'jenis_keperluan' => 'Pengantar SKCK',
            'snapshot_nama' => $resident->nama_lengkap,
            'snapshot_nik' => $resident->nik,
            'snapshot_no_kk' => $resident->no_kk,
            'snapshot_ttl' => 'Tegal, 1 Januari 1990',
            'snapshot_jenis_kelamin' => 'L',
            'snapshot_agama' => 'Islam',
            'snapshot_pekerjaan' => 'Wiraswasta',
            'snapshot_alamat' => 'Slawi Kulon RT 01 RW 01',
            'nama_ketua_rt' => 'AGUNG JATMIKO',
            'tanggal_surat' => '2026-10-18',
            'status' => 'menunggu_rt',
            'signed_by_rt_at' => null,
            'signed_by_rw_at' => null,
        ]);

        $this->actingAs($ketuaRt1);

        Livewire::test(LetterList::class)
            ->call('openApproveRtModal', $letter->id)
            ->assertSet('showApproveRtModal', true)
            ->call('confirmApproveRtLetter')
            ->assertHasNoErrors()
            ->assertSet('showApproveRtModal', false);

        $letter->refresh();
        $this->assertEquals('menunggu_rw', $letter->status);
        $this->assertNotNull($letter->signed_by_rt_at);
        $this->assertNull($letter->signed_by_rw_at);
    }

    public function test_ketua_rt_can_reject_menunggu_rt_letter_with_reason(): void
    {
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $ketuaRt1 = User::where('username', 'ketua_rt01')->firstOrFail();
        $resident = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $rt1->id))->firstOrFail();

        $letter = Letter::create([
            'rt_id' => $rt1->id,
            'resident_id' => $resident->id,
            'created_by_user_id' => $ketuaRt1->id,
            'nomor_urut' => 11,
            'nomor_surat' => '011/SP/RT.01/RW.01/X/2026',
            'tahun' => 2026,
            'jenis_keperluan' => 'Pengantar SKCK',
            'snapshot_nama' => $resident->nama_lengkap,
            'snapshot_nik' => $resident->nik,
            'snapshot_no_kk' => $resident->no_kk,
            'snapshot_ttl' => 'Tegal, 1 Januari 1990',
            'snapshot_jenis_kelamin' => 'L',
            'snapshot_agama' => 'Islam',
            'snapshot_pekerjaan' => 'Wiraswasta',
            'snapshot_alamat' => 'Slawi Kulon RT 01 RW 01',
            'nama_ketua_rt' => 'AGUNG JATMIKO',
            'tanggal_surat' => '2026-10-18',
            'status' => 'menunggu_rt',
            'signed_by_rt_at' => null,
            'signed_by_rw_at' => null,
        ]);

        $this->actingAs($ketuaRt1);

        Livewire::test(LetterList::class)
            ->call('openRejectModal', $letter->id)
            ->assertSet('showRejectModal', true)
            ->set('rejectReason', 'Alamat domisili fisik belum sesuai di lapangan')
            ->call('confirmRejectLetter')
            ->assertHasNoErrors()
            ->assertSet('showRejectModal', false);

        $letter->refresh();
        $this->assertEquals('ditolak_rt', $letter->status);
        $this->assertEquals('Alamat domisili fisik belum sesuai di lapangan', $letter->alasan_tolak);
    }

    public function test_ketua_rt_cannot_approve_or_reject_other_rt_letter(): void
    {
        $rt2 = Rt::where('nomor_rt', '002')->firstOrFail();
        $ketuaRt2 = User::where('username', 'ketua_rt02')->firstOrFail();

        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $resident1 = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $rt1->id))->firstOrFail();

        $letterRt1 = Letter::create([
            'rt_id' => $rt1->id,
            'resident_id' => $resident1->id,
            'created_by_user_id' => $ketuaRt2->id,
            'nomor_urut' => 12,
            'nomor_surat' => '012/SP/RT.01/RW.01/X/2026',
            'tahun' => 2026,
            'jenis_keperluan' => 'Pengantar SKCK',
            'snapshot_nama' => $resident1->nama_lengkap,
            'snapshot_nik' => $resident1->nik,
            'snapshot_no_kk' => $resident1->no_kk,
            'snapshot_ttl' => 'Tegal, 1 Januari 1990',
            'snapshot_jenis_kelamin' => 'L',
            'snapshot_agama' => 'Islam',
            'snapshot_pekerjaan' => 'Wiraswasta',
            'snapshot_alamat' => 'Slawi Kulon RT 01 RW 01',
            'nama_ketua_rt' => 'AGUNG JATMIKO',
            'tanggal_surat' => '2026-10-18',
            'status' => 'menunggu_rt',
            'signed_by_rt_at' => null,
            'signed_by_rw_at' => null,
        ]);

        $this->actingAs($ketuaRt2);

        // Ketua RT 02 cannot approve RT 01 letter
        Livewire::test(LetterList::class)
            ->call('openApproveRtModal', $letterRt1->id)
            ->assertForbidden();

        // Ketua RT 02 cannot reject RT 01 letter
        Livewire::test(LetterList::class)
            ->call('openRejectModal', $letterRt1->id)
            ->assertForbidden();
    }

    public function test_sekretaris_rt_cannot_approve_or_reject_letter(): void
    {
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $sekretaris = User::where('username', 'sekretaris_rt01')->firstOrFail();
        $resident = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $rt1->id))->firstOrFail();

        $letter = Letter::create([
            'rt_id' => $rt1->id,
            'resident_id' => $resident->id,
            'created_by_user_id' => $sekretaris->id,
            'nomor_urut' => 13,
            'nomor_surat' => '013/SP/RT.01/RW.01/X/2026',
            'tahun' => 2026,
            'jenis_keperluan' => 'Pengantar SKCK',
            'snapshot_nama' => $resident->nama_lengkap,
            'snapshot_nik' => $resident->nik,
            'snapshot_no_kk' => $resident->no_kk,
            'snapshot_ttl' => 'Tegal, 1 Januari 1990',
            'snapshot_jenis_kelamin' => 'L',
            'snapshot_agama' => 'Islam',
            'snapshot_pekerjaan' => 'Wiraswasta',
            'snapshot_alamat' => 'Slawi Kulon RT 01 RW 01',
            'nama_ketua_rt' => 'AGUNG JATMIKO',
            'tanggal_surat' => '2026-10-18',
            'status' => 'menunggu_rt',
            'signed_by_rt_at' => null,
            'signed_by_rw_at' => null,
        ]);

        $this->actingAs($sekretaris);

        Livewire::test(LetterList::class)
            ->call('approveByRt', $letter->id)
            ->assertForbidden();

        Livewire::test(LetterList::class)
            ->call('openApproveRtModal', $letter->id)
            ->assertForbidden();

        Livewire::test(LetterList::class)
            ->call('approveByRw', $letter->id)
            ->assertForbidden();

        Livewire::test(LetterList::class)
            ->call('openRejectModal', $letter->id)
            ->assertForbidden();
    }

    public function test_admin_rw_can_approve_menunggu_rt_letter_directly(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $resident = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $rt1->id))->firstOrFail();

        $letter = Letter::create([
            'rt_id' => $rt1->id,
            'resident_id' => $resident->id,
            'created_by_user_id' => $admin->id,
            'nomor_urut' => 14,
            'nomor_surat' => '014/SP/RT.01/RW.01/X/2026',
            'tahun' => 2026,
            'jenis_keperluan' => 'Pengantar SKCK',
            'snapshot_nama' => $resident->nama_lengkap,
            'snapshot_nik' => $resident->nik,
            'snapshot_no_kk' => $resident->no_kk,
            'snapshot_ttl' => 'Tegal, 1 Januari 1990',
            'snapshot_jenis_kelamin' => 'L',
            'snapshot_agama' => 'Islam',
            'snapshot_pekerjaan' => 'Wiraswasta',
            'snapshot_alamat' => 'Slawi Kulon RT 01 RW 01',
            'nama_ketua_rt' => 'AGUNG JATMIKO',
            'tanggal_surat' => '2026-10-18',
            'status' => 'menunggu_rt',
            'signed_by_rt_at' => null,
            'signed_by_rw_at' => null,
        ]);

        $this->actingAs($admin);

        Livewire::test(LetterList::class)
            ->call('approveByRt', $letter->id)
            ->assertHasNoErrors();

        $letter->refresh();
        $this->assertEquals('menunggu_rw', $letter->status);
        $this->assertNotNull($letter->signed_by_rt_at);
    }

    public function test_bendahara_cannot_access_letters_module(): void
    {
        $bendahara = User::where('role', 'bendahara_rt')->firstOrFail();
        $this->actingAs($bendahara);

        // Akses langsung Livewire component ditolak
        Livewire::test(LetterList::class)->assertForbidden();
        Livewire::test(LetterCreate::class)->assertForbidden();

        // Akses via HTTP route ditolak (403)
        $this->get(route('letters.index'))->assertForbidden();
        $this->get(route('letters.create'))->assertForbidden();
    }

    public function test_bendahara_does_not_see_surat_pengantar_in_navigation_menu(): void
    {
        $bendahara = User::where('role', 'bendahara_rt')->firstOrFail();
        $this->actingAs($bendahara);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
        $response->assertDontSee('Surat Pengantar');
    }

    public function test_ketua_rt_and_admin_rw_can_see_surat_pengantar_in_navigation_menu(): void
    {
        $ketuaRt = User::where('role', 'ketua_rt')->firstOrFail();
        $this->actingAs($ketuaRt);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('Surat Pengantar');

        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('Surat Pengantar');
    }
}
