<?php

namespace Tests\Feature;

use App\Livewire\Houses\HouseIndex;
use App\Livewire\Public\PublicSensus;
use App\Models\Rt;
use App\Models\SensusSubmission;
use App\Models\User;
use App\Services\ResidentImportService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ResidentHybridOnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_can_download_resident_excel_template(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();

        $response = $this->actingAs($admin)
            ->get(route('residents.import.template', ['rt_id' => 1]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_resident_import_service_parses_and_commits_excel_data(): void
    {
        $rt = Rt::firstOrFail();

        // Buat file spreadsheet sementara
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Baris 1-3: Judul
        $sheet->setCellValue('A1', 'JUDUL');
        // Baris 4: Header
        $headers = ['Nomor Rumah', 'Status Huni', 'Kategori Tarif', 'No KK', 'NIK', 'Nama Lengkap', 'L/P', 'Tempat Lahir', 'Tanggal Lahir', 'Agama', 'Pekerjaan', 'Status Keluarga', 'Status Domisili', 'No WhatsApp'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.'4', $h);
            $col++;
        }

        // Baris 5: Data Keluarga 1 (Kepala Keluarga)
        $sheet->setCellValue('A5', 'Blok Import No. 99');
        $sheet->setCellValue('B5', 'dihuni_pemilik');
        $sheet->setCellValue('C5', 'standar');
        $sheet->setCellValueExplicit('D5', '3201019999990001', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('E5', '3201011111110001', DataType::TYPE_STRING);
        $sheet->setCellValue('F5', 'Bambang Import');
        $sheet->setCellValue('G5', 'L');
        $sheet->setCellValue('H5', 'Bogor');
        $sheet->setCellValue('I5', '1985-06-20');
        $sheet->setCellValue('J5', 'Islam');
        $sheet->setCellValue('K5', 'PNS');
        $sheet->setCellValue('L5', 'kepala_keluarga');
        $sheet->setCellValue('M5', 'ktp_setempat');
        $sheet->setCellValueExplicit('N5', '081299998888', DataType::TYPE_STRING);

        // Baris 6: Data Keluarga 1 (Istri)
        $sheet->setCellValue('A6', 'Blok Import No. 99');
        $sheet->setCellValue('B6', 'dihuni_pemilik');
        $sheet->setCellValue('C6', 'standar');
        $sheet->setCellValueExplicit('D6', '3201019999990001', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('E6', '3201012222220002', DataType::TYPE_STRING);
        $sheet->setCellValue('F6', 'Sri Import');
        $sheet->setCellValue('G6', 'P');
        $sheet->setCellValue('H6', 'Depok');
        $sheet->setCellValue('I6', '1989-08-15');
        $sheet->setCellValue('J6', 'Islam');
        $sheet->setCellValue('K6', 'Guru');
        $sheet->setCellValue('L6', 'istri');
        $sheet->setCellValue('M6', 'ktp_setempat');
        $sheet->setCellValueExplicit('N6', '081299998889', DataType::TYPE_STRING);

        $tempPath = tempnam(sys_get_temp_dir(), 'test_import_').'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $service = new ResidentImportService;
        $validation = $service->parseAndValidate($tempPath, $rt->id);

        $this->assertTrue($validation['success']);
        $this->assertCount(2, $validation['valid_rows']);
        $this->assertEquals(1, $validation['new_houses_count']);

        $importResult = $service->executeImport($validation['valid_rows'], $rt->id);
        $this->assertEquals(1, $importResult['houses_count']);
        $this->assertEquals(2, $importResult['residents_count']);

        $this->assertDatabaseHas('houses', [
            'rt_id' => $rt->id,
            'nomor_rumah' => 'Blok Import No. 99',
        ]);

        $this->assertDatabaseHas('residents', [
            'nik' => '3201011111110001',
            'nama_lengkap' => 'BAMBANG IMPORT',
            'status_keluarga' => 'kepala_keluarga',
        ]);

        $this->assertDatabaseHas('residents', [
            'nik' => '3201012222220002',
            'nama_lengkap' => 'SRI IMPORT',
            'status_keluarga' => 'istri',
        ]);

        @unlink($tempPath);
    }

    public function test_public_sensus_mandiri_can_be_submitted_and_tracked(): void
    {
        $rt = Rt::firstOrFail();

        // 1. Submit Sensus Mandiri Tanpa Login
        $testComponent = Livewire::test(PublicSensus::class)
            ->set('rt_id', $rt->id)
            ->set('nomor_rumah', 'Blok Sensus Z9 No. 10')
            ->set('status_huni', 'dihuni_pemilik')
            ->set('kategori_tarif', 'standar')
            ->set('no_kk', '3201018888880001')
            ->set('nama_pengirim', 'Ahmad Sensus')
            ->set('no_wa_pengirim', '081233334444')
            ->set('catatan_rumah', 'Pagar putih')
            ->set('keluarga', [
                [
                    'nama_lengkap' => 'Ahmad Sensus',
                    'nik' => '3201013333330001',
                    'jenis_kelamin' => 'L',
                    'tempat_lahir' => 'Bandung',
                    'tanggal_lahir' => '1984-05-10',
                    'agama' => 'Islam',
                    'pekerjaan' => 'Wiraswasta',
                    'status_keluarga' => 'kepala_keluarga',
                    'status_domisili' => 'ktp_setempat',
                    'no_hp' => '081233334444',
                    'golongan_darah' => 'O',
                ],
                [
                    'nama_lengkap' => 'Rina Sensus',
                    'nik' => '3201014444440002',
                    'jenis_kelamin' => 'P',
                    'tempat_lahir' => 'Jakarta',
                    'tanggal_lahir' => '1988-11-20',
                    'agama' => 'Islam',
                    'pekerjaan' => 'Dokter',
                    'status_keluarga' => 'istri',
                    'status_domisili' => 'ktp_setempat',
                    'no_hp' => '081233334445',
                    'golongan_darah' => 'A',
                ],
            ])
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('isSubmitted', true);

        $submission = SensusSubmission::where('nama_pengirim', 'AHMAD SENSUS')->firstOrFail();
        $this->assertEquals('menunggu_verifikasi', $submission->status);
        $this->assertStringStartsWith('SNS-', $submission->kode_tiket);
        $this->assertEquals(2, $submission->total_anggota);

        // 2. Track Tiket
        Livewire::test(PublicSensus::class)
            ->set('activeTab', 'tracking')
            ->set('trackingTicketInput', $submission->kode_tiket)
            ->call('trackTicket')
            ->assertSet('trackingError', null)
            ->assertSee($submission->kode_tiket)
            ->assertSee('Menunggu Verifikasi RT');
    }

    public function test_pengurus_can_approve_sensus_submission_into_houses_and_residents(): void
    {
        $rt = Rt::firstOrFail();
        $ketuaRt = User::where('role', 'ketua_rt')->where('rt_id', $rt->id)->firstOrFail();

        $submission = SensusSubmission::create([
            'rt_id' => $rt->id,
            'kode_tiket' => 'SNS-TEST-0001',
            'nomor_rumah' => 'Blok Approve No. 77',
            'status_huni' => 'dihuni_pemilik',
            'kategori_tarif' => 'standar',
            'catatan_rumah' => 'Sensus mandiri disetujui',
            'no_kk' => '3201017777770001',
            'nama_pengirim' => 'Agus Approve',
            'no_wa_pengirim' => '081255556666',
            'data_keluarga' => [
                [
                    'nama_lengkap' => 'Agus Approve',
                    'nik' => '3201015555550001',
                    'jenis_kelamin' => 'L',
                    'tempat_lahir' => 'Semarang',
                    'tanggal_lahir' => '1980-01-01',
                    'agama' => 'Islam',
                    'pekerjaan' => 'Karyawan',
                    'status_keluarga' => 'kepala_keluarga',
                    'status_domisili' => 'ktp_setempat',
                    'no_hp' => '081255556666',
                    'golongan_darah' => 'B',
                ],
            ],
            'status' => 'menunggu_verifikasi',
        ]);

        Livewire::actingAs($ketuaRt)
            ->test(HouseIndex::class)
            ->set('activeTab', 'sensus')
            ->assertSee('SNS-TEST-0001')
            ->call('approveSensus', $submission->id);

        $submission->refresh();
        $this->assertEquals('disetujui', $submission->status);
        $this->assertEquals($ketuaRt->id, $submission->verified_by_user_id);
        $this->assertNotNull($submission->verified_at);

        $this->assertDatabaseHas('houses', [
            'rt_id' => $rt->id,
            'nomor_rumah' => 'Blok Approve No. 77',
        ]);

        $this->assertDatabaseHas('residents', [
            'nik' => '3201015555550001',
            'nama_lengkap' => 'AGUS APPROVE',
            'status_keluarga' => 'kepala_keluarga',
        ]);
    }

    public function test_pengurus_can_reject_sensus_submission_with_reason(): void
    {
        $rt = Rt::firstOrFail();
        $ketuaRt = User::where('role', 'ketua_rt')->where('rt_id', $rt->id)->firstOrFail();

        $submission = SensusSubmission::create([
            'rt_id' => $rt->id,
            'kode_tiket' => 'SNS-TEST-0002',
            'nomor_rumah' => 'Blok Reject No. 88',
            'status_huni' => 'kontrak',
            'kategori_tarif' => 'standar',
            'no_kk' => '3201016666660001',
            'nama_pengirim' => 'Doni Reject',
            'no_wa_pengirim' => '081277778888',
            'data_keluarga' => [
                [
                    'nama_lengkap' => 'Doni Reject',
                    'nik' => '3201016666660001',
                    'jenis_kelamin' => 'L',
                    'tanggal_lahir' => '1992-03-04',
                    'status_keluarga' => 'kepala_keluarga',
                    'status_domisili' => 'domisili_luar',
                ],
            ],
            'status' => 'menunggu_verifikasi',
        ]);

        Livewire::actingAs($ketuaRt)
            ->test(HouseIndex::class)
            ->set('activeTab', 'sensus')
            ->set('rejectReasonInput', 'NIK tidak valid, mohon upload foto KTP asli')
            ->call('rejectSensus', $submission->id);

        $submission->refresh();
        $this->assertEquals('ditolak', $submission->status);
        $this->assertEquals('NIK tidak valid, mohon upload foto KTP asli', $submission->catatan_verifikasi);
    }

    public function test_house_index_component_can_preview_and_execute_excel_import(): void
    {
        $rt = Rt::firstOrFail();
        $ketuaRt = User::where('role', 'ketua_rt')->where('rt_id', $rt->id)->firstOrFail();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A5', 'Blok Livewire No. 10');
        $sheet->setCellValue('B5', 'dihuni_pemilik');
        $sheet->setCellValue('C5', 'standar');
        $sheet->setCellValue('D5', '3201019999990001');
        $sheet->setCellValue('E5', '3201019999990001');
        $sheet->setCellValue('F5', 'Rudi Livewire');
        $sheet->setCellValue('G5', 'L');
        $sheet->setCellValue('H5', 'Tegal');
        $sheet->setCellValue('I5', '1989-05-15');
        $sheet->setCellValue('J5', 'Islam');
        $sheet->setCellValue('K5', 'Wiraswasta');
        $sheet->setCellValue('L5', 'kepala_keluarga');
        $sheet->setCellValue('M5', 'ktp_setempat');
        $sheet->setCellValue('N5', '081299991111');

        $tempPath = tempnam(sys_get_temp_dir(), 'test_lw_import_').'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $uploadedFile = UploadedFile::fake()->createWithContent(
            'data_warga.xlsx',
            (string) file_get_contents($tempPath)
        );

        Livewire::actingAs($ketuaRt)
            ->test(HouseIndex::class)
            ->call('openImportModal')
            ->assertSet('showImportModal', true)
            ->assertSet('importStep', 'upload')
            ->set('importFile', $uploadedFile)
            ->call('processImportPreview')
            ->assertHasNoErrors()
            ->assertSet('importStep', 'preview')
            ->assertCount('importValidRows', 1)
            ->call('executeImport')
            ->assertHasNoErrors()
            ->assertSet('showImportModal', false);

        $this->assertDatabaseHas('houses', [
            'rt_id' => $rt->id,
            'nomor_rumah' => 'Blok Livewire No. 10',
        ]);

        $this->assertDatabaseHas('residents', [
            'nik' => '3201019999990001',
            'nama_lengkap' => 'RUDI LIVEWIRE',
        ]);

        @unlink($tempPath);
    }
}
