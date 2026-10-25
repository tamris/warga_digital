<?php

namespace Tests\Feature;

use App\Livewire\Houses\HouseDetail;
use App\Livewire\SocialAid\ProgramDetail;
use App\Livewire\SocialAid\ProgramIndex;
use App\Livewire\SocialAid\WelfareMappingIndex;
use App\Models\House;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\SocialAidProgram;
use App\Models\SocialAidQuota;
use App\Models\SocialAidRecipient;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SocialAidManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('social-aid.welfare-mapping'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('social-aid.programs.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_welfare_mapping_page_renders_for_authorized_users(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $component = Livewire::test(WelfareMappingIndex::class)
            ->assertOk()
            ->assertSee('Pemetaan Kesejahteraan')
            ->assertSee('Total KK Rentan')
            ->assertSee('Belum Masuk DTKS');

        $this->assertEquals(House::count(), $component->viewData('houses')->total());
    }

    public function test_updating_house_social_profile_calculates_priority_score(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $house = House::firstOrFail();

        Livewire::test(WelfareMappingIndex::class)
            ->call('openEditModal', $house->id)
            ->assertSet('editingHouseId', $house->id)
            ->set('form_kategori_ekonomi', 'miskin_ekstrem')
            ->set('form_rentang_penghasilan', 'di_bawah_1_5_jt')
            ->set('form_status_dtks', 'belum_terdaftar')
            ->set('form_is_janda_duda', true)
            ->set('form_ada_disabilitas_kronis', true)
            ->set('form_rumah_tidak_layak', true)
            ->set('form_catatan_kondisi', 'Kondisi sangat membutuhkan bantuan mendesak')
            ->call('saveSocialProfile')
            ->assertHasNoErrors();

        $profile = $house->fresh()->socialProfile;
        $this->assertNotNull($profile);
        $this->assertEquals('miskin_ekstrem', $profile->kategori_ekonomi);
        $this->assertEquals('di_bawah_1_5_jt', $profile->rentang_penghasilan);
        $this->assertTrue((bool) $profile->is_kepala_keluarga_janda_duda);
        $this->assertTrue((bool) $profile->ada_anggota_disabilitas_sakit_kronis);
        $this->assertTrue((bool) $profile->kondisi_rumah_tidak_layak);
        // miskin_ekstrem (35) + di_bawah_1_5_jt (20) + janda (10) + disabilitas (10) + rutilahu (5) = at least 80
        $this->assertGreaterThanOrEqual(80, $profile->skor_prioritas);
    }

    public function test_dtks_export_excel_returns_streamed_response(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $response = $this->get(route('social-aid.dtks-export'));
        $response->assertOk();
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
    }

    public function test_programs_index_page_displays_active_programs(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $program = SocialAidProgram::firstOrFail();

        Livewire::test(ProgramIndex::class)
            ->assertOk()
            ->assertSee($program->nama_program)
            ->assertSee('Program Bantuan Sosial');
    }

    public function test_admin_rw_can_create_new_social_aid_program(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $rts = Rt::orderBy('nomor_rt')->get();
        $quotaInput = [];
        foreach ($rts as $rt) {
            $quotaInput[$rt->id] = 5;
        }

        // Test that live typing string inputs like "5", "10", or "" into rtQuotas does not cause array_sum error
        Livewire::test(ProgramIndex::class)
            ->call('openCreateModal')
            ->set('rtQuotas.1', '10')
            ->set('rtQuotas.2', '')
            ->assertSet('totalAllocatedQuota', 10);

        Livewire::test(ProgramIndex::class)
            ->call('openCreateModal')
            ->set('nama_program', 'Santunan Ramadhan Peduli Sesama 2026')
            ->set('sumber_dana', 'kas_rw')
            ->set('jenis_bantuan', 'uang_tunai')
            ->set('detail_bantuan', 'Uang Tunai Rp 250.000 / KK')
            ->set('tanggal_penyaluran', '2026-11-15')
            ->set('lokasi_pengambilan', 'Sekretariat RW 05')
            ->set('rtQuotas', $quotaInput)
            ->call('saveProgram')
            ->assertHasNoErrors();

        $newProgram = SocialAidProgram::where('nama_program', 'Santunan Ramadhan Peduli Sesama 2026')->first();
        $this->assertNotNull($newProgram);
        $this->assertEquals(count($rts) * 5, $newProgram->total_kuota);
        $this->assertCount(count($rts), $newProgram->quotas);
    }

    public function test_ketua_rt_can_propose_candidate_recipient(): void
    {
        $ketuaRt1 = User::where('username', 'ketua_rt01')->firstOrFail();
        $this->actingAs($ketuaRt1);

        $program = SocialAidProgram::firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();

        // Ensure quota exists
        SocialAidQuota::updateOrCreate(
            ['program_id' => $program->id, 'rt_id' => $rt1->id],
            ['jumlah_kuota' => 10]
        );

        $house = House::where('rt_id', $rt1->id)
            ->whereHas('residents', fn ($q) => $q->where('status_warga', 'aktif'))
            ->whereDoesntHave('socialAidRecipients', fn ($q) => $q->where('program_id', $program->id))
            ->firstOrFail();

        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->assertOk()
            ->call('openProposeModal')
            ->set("selectedHouseIds.{$house->id}", true)
            ->call('saveProposedRecipients')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('social_aid_recipients', [
            'program_id' => $program->id,
            'house_id' => $house->id,
            'rt_id' => $rt1->id,
            'status_usulan' => 'diusulkan_rt',
        ]);
    }

    public function test_pengurus_can_replace_recipient(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $program = SocialAidProgram::firstOrFail();
        $recipient = SocialAidRecipient::where('program_id', $program->id)->firstOrFail();

        // Find another active resident in another house in the same RT
        $replacement = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $recipient->rt_id)->where('id', '!=', $recipient->house_id))
            ->where('status_warga', 'aktif')
            ->firstOrFail();

        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('openReplaceModal', $recipient->id)
            ->set('alasan_penggantian', 'meninggal')
            ->set('digantikan_oleh_resident_id', $replacement->id)
            ->set('catatan_penggantian', 'Warga awal telah wafat bulan lalu, dialihkan ke tetangga janda')
            ->call('confirmReplacement')
            ->assertHasNoErrors();

        $recipient->refresh();
        $this->assertEquals('diganti', $recipient->status_usulan);
        $this->assertEquals('meninggal', $recipient->alasan_penggantian);
        $this->assertEquals($replacement->id, $recipient->digantikan_oleh_resident_id);
    }

    public function test_marking_recipient_as_received(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $program = SocialAidProgram::firstOrFail();
        $recipient = SocialAidRecipient::where('program_id', $program->id)
            ->where('status_usulan', '!=', 'diganti')
            ->firstOrFail();

        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('openDeliveryModal', $recipient->id)
            ->set('status_penyerahan', 'sudah_diterima')
            ->set('penerima_aktual', 'Budi Santoso')
            ->call('saveDelivery')
            ->assertHasNoErrors();

        $recipient->refresh();
        $this->assertEquals('sudah_diterima', $recipient->status_penyerahan);
        $this->assertNotNull($recipient->waktu_diterima);
        $this->assertEquals($admin->id, $recipient->diserahkan_oleh);
    }

    public function test_attendance_and_replacement_pdf_downloads(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $program = SocialAidProgram::firstOrFail();

        $attResponse = $this->get(route('social-aid.programs.attendance-pdf', $program));
        $attResponse->assertOk();
        $this->assertEquals('application/pdf', $attResponse->headers->get('Content-Type'));

        $repResponse = $this->get(route('social-aid.programs.replacement-pdf', $program));
        $repResponse->assertOk();
        $this->assertEquals('application/pdf', $repResponse->headers->get('Content-Type'));
    }

    public function test_attendance_and_replacement_browser_print_previews(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $program = SocialAidProgram::firstOrFail();

        $attPrint = $this->get(route('social-aid.programs.attendance-print', $program));
        $attPrint->assertOk();
        $attPrint->assertSee('DAFTAR HADIR');
        $attPrint->assertSee('BUKTI PENERIMAAN BANTUAN SOSIAL');
        $attPrint->assertSee('Cetak Dokumen');
        $attPrint->assertSee('Unduh PDF');

        $repPrint = $this->get(route('social-aid.programs.replacement-print', $program));
        $repPrint->assertOk();
        $repPrint->assertSee('BERITA ACARA PENGGANTIAN');
        $repPrint->assertSee('Cetak Dokumen');
        $repPrint->assertSee('Unduh PDF');
    }

    public function test_rt_isolation_for_pengurus_rt_on_program_detail(): void
    {
        $ketuaRt1 = User::where('username', 'ketua_rt01')->firstOrFail();
        $this->actingAs($ketuaRt1);

        $program = SocialAidProgram::firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();

        $component = Livewire::test(ProgramDetail::class, ['program' => $program]);
        $component->assertOk();
        $this->assertEquals($rt1->id, $component->get('selectedRtId'));
    }

    public function test_admin_rw_can_approve_and_advance_to_penyaluran_when_distribution_date_due(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $program = SocialAidProgram::create([
            'nama_program' => 'Program Uji Verifikasi RW',
            'sumber_dana' => 'pemda_kelurahan',
            'jenis_bantuan' => 'sembako_barang',
            'detail_bantuan' => 'Beras 10kg',
            'total_kuota' => 1,
            'tanggal_penyaluran' => now()->toDateString(),
            'status_program' => 'verifikasi_rw',
            'created_by' => $admin->id,
        ]);

        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        SocialAidQuota::create([
            'program_id' => $program->id,
            'rt_id' => $rt1->id,
            'jumlah_kuota' => 1,
        ]);
        $house = House::where('rt_id', $rt1->id)->firstOrFail();
        $resident = Resident::where('house_id', $house->id)->firstOrFail();

        $recipient = SocialAidRecipient::create([
            'program_id' => $program->id,
            'rt_id' => $rt1->id,
            'house_id' => $house->id,
            'resident_id' => $resident->id,
            'nomor_urut_kupon' => 'KP-TEST-001',
            'status_usulan' => 'diusulkan_rt',
        ]);

        // RW approves single recipient
        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('approveRecipient', $recipient->id)
            ->assertHasNoErrors();

        $recipient->refresh();
        $this->assertEquals('disetujui_rw', $recipient->status_usulan);

        // RW approves all & since date is due, advances automatically to penyaluran
        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('approveAllAndAdvanceToPenyaluran')
            ->assertHasNoErrors();

        $program->refresh();
        $this->assertEquals('penyaluran', $program->status_program);
    }

    public function test_program_stays_in_verifikasi_rw_when_date_in_future_until_due_or_manual_advance(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $futureDate = now()->addDays(7)->toDateString();
        $program = SocialAidProgram::create([
            'nama_program' => 'Program Bantuan Jadwal Masa Depan',
            'sumber_dana' => 'pemda_kelurahan',
            'jenis_bantuan' => 'sembako_barang',
            'detail_bantuan' => 'Beras 10kg',
            'total_kuota' => 1,
            'tanggal_penyaluran' => $futureDate,
            'status_program' => 'verifikasi_rw',
            'created_by' => $admin->id,
        ]);

        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        SocialAidQuota::create([
            'program_id' => $program->id,
            'rt_id' => $rt1->id,
            'jumlah_kuota' => 1,
        ]);
        $house = House::where('rt_id', $rt1->id)->firstOrFail();
        $resident = Resident::where('house_id', $house->id)->firstOrFail();

        SocialAidRecipient::create([
            'program_id' => $program->id,
            'rt_id' => $rt1->id,
            'house_id' => $house->id,
            'resident_id' => $resident->id,
            'nomor_urut_kupon' => 'KP-FUT-001',
            'status_usulan' => 'disetujui_rw',
            'status_penyerahan' => 'belum_diambil',
        ]);

        // When visiting, because all proposals are approved but date is in the future:
        // 1. Program stays in verifikasi_rw
        // 2. Status label is 'Siap Disalurkan (Menunggu Jadwal)'
        // 3. Tab Penyaluran Hari-H is not visible
        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->assertOk()
            ->assertDontSee('Penyaluran Hari-H')
            ->assertSee('Siap Disalurkan (Menunggu Jadwal)')
            ->assertSee('Buka Penyaluran Sekarang')
            ->call('advanceToPenyaluran')
            ->assertHasNoErrors();

        $program->refresh();
        $this->assertEquals('penyaluran', $program->status_program);

        // After manual advance by RW, Tab Penyaluran Hari-H becomes visible
        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->assertOk()
            ->assertSee('Penyaluran Hari-H');
    }

    public function test_rt_cannot_delete_approved_recipient(): void
    {
        $ketuaRt1 = User::where('username', 'ketua_rt01')->firstOrFail();
        $this->actingAs($ketuaRt1);

        $program = SocialAidProgram::firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $house = House::where('rt_id', $rt1->id)->firstOrFail();
        $resident = Resident::where('house_id', $house->id)->firstOrFail();

        $recipient = SocialAidRecipient::create([
            'program_id' => $program->id,
            'rt_id' => $rt1->id,
            'house_id' => $house->id,
            'resident_id' => $resident->id,
            'nomor_urut_kupon' => 'KP-LOCKED-001',
            'status_usulan' => 'disetujui_rw',
        ]);

        // RT tries to delete approved recipient - blocked because already approved by RW
        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('removeRecipient', $recipient->id);

        $this->assertDatabaseHas('social_aid_recipients', [
            'id' => $recipient->id,
            'status_usulan' => 'disetujui_rw',
        ]);
    }

    public function test_program_auto_completion_and_reversion(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $program = SocialAidProgram::create([
            'nama_program' => 'Program Tes Penyaluran Selesai',
            'sumber_dana' => 'kas_rw',
            'jenis_bantuan' => 'uang_tunai',
            'detail_bantuan' => 'Rp. 300.000',
            'tanggal_penyaluran' => now()->toDateString(),
            'lokasi_pengambilan' => 'Pos RW',
            'total_kuota' => 2,
            'status_program' => 'penyaluran',
            'created_by' => $admin->id,
        ]);

        $rt1 = Rt::firstOrFail();
        $residents = Resident::take(2)->get();

        $rec1 = SocialAidRecipient::create([
            'program_id' => $program->id,
            'rt_id' => $rt1->id,
            'house_id' => $residents[0]->house_id,
            'resident_id' => $residents[0]->id,
            'status_usulan' => 'disetujui_rw',
            'status_penyerahan' => 'sudah_diterima',
            'penerima_aktual' => $residents[0]->nama_lengkap,
            'waktu_diterima' => now(),
        ]);

        $rec2 = SocialAidRecipient::create([
            'program_id' => $program->id,
            'rt_id' => $rt1->id,
            'house_id' => $residents[1]->house_id,
            'resident_id' => $residents[1]->id,
            'status_usulan' => 'disetujui_rw',
            'status_penyerahan' => 'belum_diambil',
        ]);

        // Deliver the 2nd recipient -> should auto transition to 'selesai'
        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('openDeliveryModal', $rec2->id)
            ->set('status_penyerahan', 'sudah_diterima')
            ->set('penerima_aktual', $residents[1]->nama_lengkap)
            ->call('saveDelivery');

        $program->refresh();
        $this->assertEquals('selesai', $program->status_program);

        // Reset delivery of rec2 -> should auto revert back to 'penyaluran'
        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('resetDelivery', $rec2->id);

        $program->refresh();
        $this->assertEquals('penyaluran', $program->status_program);

        // Advance to selesai manually
        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('advanceToSelesai');

        $program->refresh();
        $this->assertEquals('selesai', $program->status_program);
    }

    public function test_complete_lifecycle_pipeline_from_pendataan_to_arsip(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $rt1 = Rt::firstOrFail();
        $residents = Resident::take(2)->get();

        $program = SocialAidProgram::create([
            'nama_program' => 'Program Siklus Penuh Bantuan',
            'sumber_dana' => 'kas_rw',
            'jenis_bantuan' => 'uang_tunai',
            'detail_bantuan' => 'Rp. 500.000',
            'tanggal_penyaluran' => now()->toDateString(),
            'lokasi_pengambilan' => 'Pos RW 01',
            'total_kuota' => 1,
            'status_program' => 'pendataan_rt',
            'created_by' => $admin->id,
        ]);

        SocialAidQuota::create([
            'program_id' => $program->id,
            'rt_id' => $rt1->id,
            'jumlah_kuota' => 1,
        ]);

        $this->assertEquals('pendataan_rt', $program->status_program);

        // 1. RT proposes 1 citizen -> quota is now full (1/1) -> should transition to verifikasi_rw
        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->set('selectedRtId', $rt1->id)
            ->call('openProposeModal')
            ->set("selectedHouseIds.{$residents[0]->house_id}", true)
            ->call('saveProposedRecipients');

        $program->refresh();
        $this->assertEquals('verifikasi_rw', $program->status_program);

        $recipient = $program->recipients()->where('status_usulan', '!=', 'diganti')->firstOrFail();

        // 2. RW approves (ACC) the proposal -> all proposals approved -> should transition to penyaluran
        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('approveRecipient', $recipient->id);

        $program->refresh();
        $this->assertEquals('penyaluran', $program->status_program);

        // 3. Mark recipient as delivered -> all deliveries completed -> should transition to selesai
        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('openDeliveryModal', $recipient->id)
            ->set('status_penyerahan', 'sudah_diterima')
            ->set('penerima_aktual', $residents[0]->nama_lengkap)
            ->call('saveDelivery');

        $program->refresh();
        $this->assertEquals('selesai', $program->status_program);

        // 4. Reversal: Reset delivery -> should revert to penyaluran
        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('resetDelivery', $recipient->id);

        $program->refresh();
        $this->assertEquals('penyaluran', $program->status_program);

        // 5. In penyaluran stage, unapproving and deleting proposals are strictly locked
        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('unapproveRecipient', $recipient->id);

        $this->assertEquals('disetujui_rw', $recipient->fresh()->status_usulan);

        $program->refresh();
        $this->assertEquals('penyaluran', $program->status_program);

        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('removeRecipient', $recipient->id);

        $this->assertDatabaseHas('social_aid_recipients', [
            'id' => $recipient->id,
        ]);

        $program->refresh();
        $this->assertEquals('penyaluran', $program->status_program);
    }

    public function test_delete_program_allowed_only_in_planning_stages(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        // Program 1: pendataan_rt -> can be deleted
        $programPlanning = SocialAidProgram::create([
            'nama_program' => 'Program Draft Perencanaan',
            'sumber_dana' => 'kas_rw',
            'jenis_bantuan' => 'uang_tunai',
            'detail_bantuan' => 'Rp. 100.000',
            'tanggal_penyaluran' => now()->toDateString(),
            'total_kuota' => 5,
            'status_program' => 'pendataan_rt',
            'created_by' => $admin->id,
        ]);

        Livewire::test(ProgramIndex::class)
            ->call('confirmDeleteProgram', $programPlanning->id)
            ->assertSet('showDeleteModal', true)
            ->call('executeDeleteProgram')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('social_aid_programs', ['id' => $programPlanning->id]);

        // Program 2: penyaluran -> blocked from deletion
        $programPenyaluran = SocialAidProgram::create([
            'nama_program' => 'Program Sedang Penyaluran',
            'sumber_dana' => 'kas_rw',
            'jenis_bantuan' => 'uang_tunai',
            'detail_bantuan' => 'Rp. 100.000',
            'tanggal_penyaluran' => now()->toDateString(),
            'total_kuota' => 5,
            'status_program' => 'penyaluran',
            'created_by' => $admin->id,
        ]);

        Livewire::test(ProgramIndex::class)
            ->call('confirmDeleteProgram', $programPenyaluran->id)
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseHas('social_aid_programs', ['id' => $programPenyaluran->id]);

        // Program 3: selesai -> blocked from deletion
        $programSelesai = SocialAidProgram::create([
            'nama_program' => 'Program Sudah Selesai & Arsip',
            'sumber_dana' => 'kas_rw',
            'jenis_bantuan' => 'uang_tunai',
            'detail_bantuan' => 'Rp. 100.000',
            'tanggal_penyaluran' => now()->toDateString(),
            'total_kuota' => 5,
            'status_program' => 'selesai',
            'created_by' => $admin->id,
        ]);

        Livewire::test(ProgramIndex::class)
            ->call('confirmDeleteProgram', $programSelesai->id)
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseHas('social_aid_programs', ['id' => $programSelesai->id]);
    }

    public function test_approach_a_recipient_replacement_restrictions(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $program = SocialAidProgram::firstOrFail();
        $recipient = SocialAidRecipient::where('program_id', $program->id)->firstOrFail();
        $replacement = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $recipient->rt_id)->where('id', '!=', $recipient->house_id))
            ->where('status_warga', 'aktif')
            ->firstOrFail();

        // 1. When status_usulan === 'disetujui_rw', direct replacement is BLOCKED
        $recipient->update(['status_usulan' => 'disetujui_rw', 'status_penyerahan' => 'belum_diambil']);

        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('openReplaceModal', $recipient->id)
            ->assertSet('showReplaceModal', false);

        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->set('targetRecipientId', $recipient->id)
            ->set('alasan_penggantian', 'meninggal')
            ->set('digantikan_oleh_resident_id', $replacement->id)
            ->call('confirmReplacement');

        $recipient->refresh();
        $this->assertEquals('disetujui_rw', $recipient->status_usulan);

        // 2. When RW unapproves (kembalikan ke usulan RT), replacement becomes ALLOWED
        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('unapproveRecipient', $recipient->id);

        $recipient->refresh();
        $this->assertEquals('diusulkan_rt', $recipient->status_usulan);

        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('openReplaceModal', $recipient->id)
            ->assertSet('showReplaceModal', true)
            ->set('alasan_penggantian', 'meninggal')
            ->set('digantikan_oleh_resident_id', $replacement->id)
            ->call('confirmReplacement')
            ->assertHasNoErrors();

        $recipient->refresh();
        $this->assertEquals('diganti', $recipient->status_usulan);

        // 3. Recipient who has already received aid (status_penyerahan != 'belum_diambil') CANNOT be replaced
        $deliveredRecipient = SocialAidRecipient::where('program_id', $program->id)
            ->where('status_usulan', '!=', 'diganti')
            ->where('id', '!=', $recipient->id)
            ->firstOrFail();
        $deliveredRecipient->update(['status_penyerahan' => 'sudah_diterima']);

        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('openReplaceModal', $deliveredRecipient->id)
            ->assertSet('showReplaceModal', false);

        // 4. In completed/archived program, replacement is BLOCKED
        $program->recipients()->update(['status_usulan' => 'disetujui_rw', 'status_penyerahan' => 'sudah_diterima']);
        $program->update(['status_program' => 'selesai']);
        $anotherRecipient = SocialAidRecipient::where('program_id', $program->id)
            ->where('status_usulan', '!=', 'diganti')
            ->firstOrFail();

        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('openReplaceModal', $anotherRecipient->id)
            ->assertSet('showReplaceModal', false);
    }

    public function test_approving_proposals_when_quota_incomplete_keeps_program_in_pendataan_rt(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $program = SocialAidProgram::create([
            'nama_program' => 'Program Kuota Belum Lengkap',
            'sumber_dana' => 'kas_rw',
            'jenis_bantuan' => 'sembako_barang',
            'detail_bantuan' => 'Beras 10kg',
            'total_kuota' => 12,
            'tanggal_penyaluran' => '2026-11-01',
            'status_program' => 'pendataan_rt',
            'created_by' => $admin->id,
        ]);

        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        SocialAidQuota::create([
            'program_id' => $program->id,
            'rt_id' => $rt1->id,
            'jumlah_kuota' => 12,
        ]);

        $residents = Resident::take(6)->get();
        foreach ($residents as $idx => $r) {
            SocialAidRecipient::create([
                'program_id' => $program->id,
                'rt_id' => $rt1->id,
                'house_id' => $r->house_id,
                'resident_id' => $r->id,
                'nomor_urut_kupon' => 'KP-INC-'.($idx + 1),
                'status_usulan' => 'diusulkan_rt',
                'status_penyerahan' => 'belum_diambil',
            ]);
        }

        // Only 6 of 12 are entered
        $this->assertFalse($program->areAllRtQuotasFilled());

        // RW approves all 6 proposals via bulk action
        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('approveAllAndAdvanceToPenyaluran')
            ->assertHasNoErrors();

        // All 6 should now be disetujui_rw
        $this->assertEquals(6, $program->recipients()->where('status_usulan', 'disetujui_rw')->count());

        // BUT program status MUST STAY pendataan_rt because quota is not yet full!
        $program->refresh();
        $this->assertEquals('pendataan_rt', $program->status_program);
    }

    public function test_penyaluran_stage_locks_proposal_actions_and_blocks_unapprove_or_delete(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $program = SocialAidProgram::create([
            'nama_program' => 'Bansos Tahap Penyaluran Terkunci',
            'sumber_dana' => 'kas_rw',
            'jenis_bantuan' => 'uang_tunai',
            'detail_bantuan' => 'Rp. 500.000',
            'tanggal_penyaluran' => now()->toDateString(),
            'lokasi_pengambilan' => 'Balai RW',
            'total_kuota' => 1,
            'status_program' => 'penyaluran',
            'created_by' => $admin->id,
        ]);

        $rt1 = Rt::firstOrFail();
        SocialAidQuota::create([
            'program_id' => $program->id,
            'rt_id' => $rt1->id,
            'jumlah_kuota' => 1,
        ]);

        $resident = Resident::firstOrFail();
        $recipient = SocialAidRecipient::create([
            'program_id' => $program->id,
            'rt_id' => $rt1->id,
            'house_id' => $resident->house_id,
            'resident_id' => $resident->id,
            'nomor_urut_kupon' => 'RT01-001',
            'status_usulan' => 'disetujui_rw',
            'status_penyerahan' => 'belum_diambil',
        ]);

        // In blade, the action buttons (unapprove & delete) should NOT exist, but "Terkunci" badge should be visible
        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->assertSee('Terkunci')
            ->assertDontSee('unapproveRecipient')
            ->assertDontSee('confirmRemoveRecipient')
            ->call('unapproveRecipient', $recipient->id);

        // Recipient should remain disetujui_rw
        $this->assertEquals('disetujui_rw', $recipient->fresh()->status_usulan);

        // Attempting to remove recipient should also be blocked
        Livewire::test(ProgramDetail::class, ['program' => $program])
            ->call('removeRecipient', $recipient->id);

        $this->assertDatabaseHas('social_aid_recipients', [
            'id' => $recipient->id,
        ]);
    }

    public function test_automatic_poverty_categorization_from_factual_indicators(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $house = House::firstOrFail();

        // 1. High income (> 3jt) with no vulnerability -> score 0 -> kategori 'mampu'
        Livewire::test(WelfareMappingIndex::class)
            ->call('openEditModal', $house->id)
            ->set('form_rentang_penghasilan', 'di_atas_3_jt')
            ->set('form_rumah_tidak_layak', false)
            ->set('form_is_janda_duda', false)
            ->set('form_ada_disabilitas_kronis', false)
            ->call('saveSocialProfile')
            ->assertHasNoErrors();

        $profile = $house->fresh()->socialProfile;
        $this->assertEquals('mampu', $profile->kategori_ekonomi);
        $this->assertLessThan(30, $profile->skor_prioritas);

        // 2. Income 1.5 - 3jt (20) + janda (10) -> score >= 30 -> kategori 'menengah'
        Livewire::test(WelfareMappingIndex::class)
            ->call('openEditModal', $house->id)
            ->set('form_rentang_penghasilan', '1_5_sampai_3_jt')
            ->set('form_rumah_tidak_layak', false)
            ->set('form_is_janda_duda', true)
            ->set('form_ada_disabilitas_kronis', false)
            ->call('saveSocialProfile')
            ->assertHasNoErrors();

        $profile = $house->fresh()->socialProfile;
        $this->assertEquals('menengah', $profile->kategori_ekonomi);
        $this->assertGreaterThanOrEqual(30, $profile->skor_prioritas);
        $this->assertLessThan(55, $profile->skor_prioritas);

        // 3. Low income (40) + janda (10) + disabilitas (10) -> score 60 -> kategori 'rentan_miskin'
        Livewire::test(WelfareMappingIndex::class)
            ->call('openEditModal', $house->id)
            ->set('form_rentang_penghasilan', 'di_bawah_1_5_jt')
            ->set('form_rumah_tidak_layak', false)
            ->set('form_is_janda_duda', true)
            ->set('form_ada_disabilitas_kronis', true)
            ->call('saveSocialProfile')
            ->assertHasNoErrors();

        $profile = $house->fresh()->socialProfile;
        $this->assertEquals('rentan_miskin', $profile->kategori_ekonomi);
        $this->assertGreaterThanOrEqual(55, $profile->skor_prioritas);
        $this->assertLessThan(80, $profile->skor_prioritas);

        // 4. Low income (40) + RTLH (20) + janda (10) + disabilitas (10) -> score 80 -> kategori 'miskin_ekstrem'
        Livewire::test(WelfareMappingIndex::class)
            ->call('openEditModal', $house->id)
            ->set('form_rentang_penghasilan', 'di_bawah_1_5_jt')
            ->set('form_rumah_tidak_layak', true)
            ->set('form_is_janda_duda', true)
            ->set('form_ada_disabilitas_kronis', true)
            ->call('saveSocialProfile')
            ->assertHasNoErrors();

        $profile = $house->fresh()->socialProfile;
        $this->assertEquals('miskin_ekstrem', $profile->kategori_ekonomi);
        $this->assertGreaterThanOrEqual(80, $profile->skor_prioritas);
    }

    public function test_modal_does_not_activate_lansia_when_house_has_no_elderly_residents(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        // Find a house where all residents are < 60 years old
        $houseWithoutLansia = House::whereDoesntHave('residents', function ($q) {
            $q->where('status_warga', 'aktif')
                ->whereDate('tanggal_lahir', '<=', now()->subYears(60));
        })->has('residents')->firstOrFail();

        Livewire::test(WelfareMappingIndex::class)
            ->call('openEditModal', $houseWithoutLansia->id)
            ->assertSet('hasLansia', false)
            ->assertSeeHtml('Terdapat Anggota Lansia (&ge; 60 Tahun)')
            ->assertSeeHtml('0 Poin');
    }

    public function test_house_detail_back_button_returns_to_welfare_mapping_when_navigated_from_welfare(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $house = House::firstOrFail();

        // 1. When navigated with ?from=welfare, back button goes to social-aid.welfare-mapping
        Livewire::withQueryParams(['from' => 'welfare'])
            ->test(HouseDetail::class, ['id' => $house->id])
            ->assertSee('Kembali ke Pemetaan Kesejahteraan &amp; Bansos', false)
            ->assertSee(route('social-aid.welfare-mapping'));

        // 2. Default navigation goes to houses.index
        Livewire::withQueryParams([])
            ->test(HouseDetail::class, ['id' => $house->id, 'from' => null])
            ->assertSee('Kembali ke Daftar Rumah')
            ->assertSee(route('houses.index'));
    }

    public function test_house_aid_rotation_metrics_calculates_fairness_and_recency_adjustments(): void
    {
        $rt = Rt::firstOrFail();
        $houseNever = House::where('rt_id', $rt->id)->whereDoesntHave('socialAidRecipients')->first()
            ?? House::factory()->create(['rt_id' => $rt->id]);
        $profile = $houseNever->getOrCreateSocialProfile();
        $profile->update([
            'rentang_penghasilan' => 'di_bawah_1_5_jt', // 40
            'kondisi_rumah_tidak_layak' => true, // 20 -> total 60
        ]);
        $baseScore = $profile->fresh()->skor_prioritas;
        $this->assertEquals(60, $baseScore);

        // 1. House never received aid: +25 bonus
        $houseNever->unsetRelation('socialProfile');
        $metricsNever = $houseNever->getAidRotationMetrics();
        $this->assertEquals('never', $metricsNever['status']);
        $this->assertEquals(0, $metricsNever['total_received']);
        $this->assertEquals(25, $metricsNever['adjustment']);
        $this->assertEquals(85, $metricsNever['fairness_score']); // 60 + 25

        // Create a past program
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $pastProgram = SocialAidProgram::create([
            'nama_program' => 'Bansos Sembako Bulan Lalu',
            'sumber_dana' => 'kas_rw',
            'jenis_bantuan' => 'sembako_barang',
            'detail_bantuan' => 'Beras 5kg, Minyak 2L',
            'status_program' => 'selesai',
            'total_kuota' => 10,
            'tanggal_penyaluran' => now()->subDays(15),
            'created_by' => $admin->id,
        ]);

        $recipientResident = $houseNever->residents()->first() ?? Resident::factory()->create(['house_id' => $houseNever->id, 'rt_id' => $rt->id]);

        $rec = SocialAidRecipient::create([
            'program_id' => $pastProgram->id,
            'rt_id' => $rt->id,
            'house_id' => $houseNever->id,
            'resident_id' => $recipientResident->id,
            'nomor_urut_kupon' => 'RT01-001',
            'status_usulan' => 'disetujui_rw',
            'status_penyerahan' => 'sudah_diterima',
            'waktu_diterima' => now()->subDays(15),
        ]);

        // 2. House received aid 15 days ago: Cooldown penalty (-35)
        $houseNever->unsetRelation('socialAidRecipients');
        $metricsCooldown = $houseNever->getAidRotationMetrics();
        $this->assertEquals('cooldown', $metricsCooldown['status']);
        $this->assertEquals(1, $metricsCooldown['total_received']);
        $this->assertEquals(-35, $metricsCooldown['adjustment']);
        $this->assertEquals(25, $metricsCooldown['fairness_score']); // 60 - 35

        // 3. Safety floor: If extreme poverty base score >= 80, fairness floor is at least 50
        $profile->update([
            'is_kepala_keluarga_janda_duda' => true, // +10
            'ada_anggota_disabilitas_sakit_kronis' => true, // +10 -> total 80
        ]);
        $houseNever->unsetRelation('socialProfile');
        $metricsFloor = $houseNever->getAidRotationMetrics();
        $this->assertGreaterThanOrEqual(80, $metricsFloor['base_score']);
        $this->assertEquals(50, $metricsFloor['fairness_score']); // max(50, 80 - 35 = 45) -> 50

        // 4. House received aid 45 days ago: Light penalty (-15)
        $rec->update(['waktu_diterima' => now()->subDays(45)]);
        $houseNever->unsetRelation('socialAidRecipients');
        $metricsRecent = $houseNever->getAidRotationMetrics();
        $this->assertEquals('recent', $metricsRecent['status']);
        $this->assertEquals(-15, $metricsRecent['adjustment']);

        // 5. House received aid 75 days ago: Ready (0 adjustment)
        $rec->update(['waktu_diterima' => now()->subDays(75)]);
        $houseNever->unsetRelation('socialAidRecipients');
        $metricsReady = $houseNever->getAidRotationMetrics();
        $this->assertEquals('ready', $metricsReady['status']);
        $this->assertEquals(0, $metricsReady['adjustment']);
    }

    public function test_program_detail_propose_modal_supports_fairness_filters_and_auto_selection(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $this->actingAs($admin);

        $program = SocialAidProgram::where('status_program', 'pendataan_rt')->first();
        if (! $program) {
            $program = SocialAidProgram::create([
                'nama_program' => 'Program Rotasi Adil Uji Coba',
                'sumber_dana' => 'kas_rw',
                'jenis_bantuan' => 'sembako_barang',
                'status_program' => 'pendataan_rt',
                'total_kuota' => 6,
                'tanggal_penyaluran' => now()->addDays(7),
                'created_by' => $admin->id,
            ]);
            $rt = Rt::firstOrFail();
            SocialAidQuota::create([
                'program_id' => $program->id,
                'rt_id' => $rt->id,
                'jumlah_kuota' => 3,
            ]);
        }

        $rtId = $program->quotas->first()->rt_id;
        $quota = $program->getQuotaForRt($rtId);

        // Open propose modal
        $component = Livewire::test(ProgramDetail::class, ['program' => $program])
            ->set('selectedRtId', $rtId)
            ->call('openProposeModal')
            ->assertSee('Rekomendasi Calon Penerima Bantuan')
            ->assertSee('Pilih Otomatis');

        // Test auto select by fairness
        $component->call('autoSelectByFairness')
            ->assertHasNoErrors();

        $selected = array_filter($component->get('selectedHouseIds'));
        $this->assertLessThanOrEqual($quota, count($selected));
        $this->assertGreaterThan(0, count($selected));

        // Test clearing selection
        $component->call('clearCandidateSelection');
        $this->assertEmpty(array_filter($component->get('selectedHouseIds')));

        // Test filtering candidates
        $component->set('candidateFilter', 'never')
            ->assertHasNoErrors();
    }
}
