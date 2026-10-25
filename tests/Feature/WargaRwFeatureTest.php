<?php

namespace Tests\Feature;

use App\Livewire\Houses\HouseDetail;
use App\Livewire\Houses\HouseIndex;
use App\Livewire\Residents\ResidentIndex;
use App\Models\House;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WargaRwFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('login'));
    }

    public function test_admin_rw_sees_all_houses_and_can_filter(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();

        $this->actingAs($admin);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('Admin RW');

        Livewire::actingAs($admin)
            ->test(HouseIndex::class)
            ->assertOk()
            ->assertSee('Blok 003-A No. 01')
            ->assertSee('Blok 002-A No. 01')
            ->set('selectedRtId', 1)
            ->assertSee('Blok 001-A No. 01')
            ->assertDontSee('Blok 002-A No. 01');
    }

    public function test_pengurus_rt_scoping_isolation(): void
    {
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $rt2 = Rt::where('nomor_rt', '002')->firstOrFail();

        $ketuaRt1 = User::where('username', 'ketua_rt01')->firstOrFail();
        $houseRt2 = House::where('rt_id', $rt2->id)->firstOrFail();

        $this->actingAs($ketuaRt1);

        // Hanya boleh melihat rumah RT 001 di tabel rumah
        Livewire::actingAs($ketuaRt1)
            ->test(HouseIndex::class)
            ->assertOk()
            ->assertSee('Blok 001-A No. 01')
            ->assertDontSee('Blok 002-A No. 01');

        // Mencoba membuka detail rumah RT 002 harus gagal 404 (di luar scope)
        $response = $this->get(route('houses.show', $houseRt2->id));
        $response->assertNotFound();
    }

    public function test_house_creation_by_pengurus_rt_is_locked_to_their_rt(): void
    {
        $ketuaRt1 = User::where('username', 'ketua_rt01')->firstOrFail();

        Livewire::actingAs($ketuaRt1)
            ->test(HouseIndex::class)
            ->call('openCreateModal')
            ->set('form_nomor_rumah', 'Blok X-99')
            ->set('form_status_huni', 'dihuni_pemilik')
            ->set('form_kategori_tarif', 'standar')
            ->set('form_rt_id', 999) // Mencoba inject ID RT lain
            ->call('saveHouse')
            ->assertHasNoErrors();

        // Harus tetap tersimpan ke RT 1 milik ketua_rt01
        $house = House::where('nomor_rumah', 'Blok X-99')->first();
        $this->assertNotNull($house);
        $this->assertEquals($ketuaRt1->rt_id, $house->rt_id);
    }

    public function test_resident_creation_under_house_with_validation(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $house = House::firstOrFail();

        Livewire::actingAs($admin)
            ->test(HouseDetail::class, ['id' => $house->id])
            ->call('openCreateResidentModal')
            ->set('form_no_kk', '3271012999999999')
            ->set('form_nik', '3271011999999999')
            ->set('form_nama_lengkap', 'Budi Santoso Baru')
            ->set('form_jenis_kelamin', 'L')
            ->set('form_tempat_lahir', 'Surabaya')
            ->set('form_tanggal_lahir', '1985-05-15')
            ->set('form_agama', 'Islam')
            ->set('form_pekerjaan', 'Arsitek')
            ->set('form_status_keluarga', 'kepala_keluarga')
            ->set('form_status_kependudukan', 'ktp_setempat')
            ->set('form_no_wa', '081299998888')
            ->set('form_status_warga', 'aktif')
            ->call('saveResident')
            ->assertHasNoErrors();

        $resident = Resident::where('nik', '3271011999999999')->first();
        $this->assertNotNull($resident);
        $this->assertEquals('BUDI SANTOSO BARU', $resident->nama_lengkap);
        $this->assertEquals($house->id, $resident->house_id);
    }

    public function test_mutasi_resident_to_pindah(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $resident = Resident::where('status_warga', 'aktif')->firstOrFail();
        $house = $resident->house;

        Livewire::actingAs($admin)
            ->test(HouseDetail::class, ['id' => $house->id])
            ->call('confirmMutasi', $resident->id)
            ->call('executeMutasi');

        $resident->refresh();
        $this->assertEquals('pindah', $resident->status_warga);
    }

    public function test_resident_excel_export_returns_streamed_xlsx(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('residents.export'));
        $response->assertOk();
        $response->assertHeader('content-disposition', 'attachment; filename="residents.xlsx"');
    }

    public function test_sekretaris_rt_can_manage_but_cannot_delete(): void
    {
        $sekretaris = User::where('role', 'sekretaris_rt')->firstOrFail();
        $house = House::where('rt_id', $sekretaris->rt_id)->firstOrFail();

        // Bisa buka create modal
        Livewire::actingAs($sekretaris)
            ->test(HouseIndex::class)
            ->call('openCreateModal')
            ->assertSet('showModal', true);

        // Tidak bisa hapus rumah (403)
        $this->actingAs($sekretaris);
        Livewire::test(HouseIndex::class)
            ->call('confirmDelete', $house->id)
            ->assertForbidden();
    }

    public function test_bendahara_rt_is_read_only_for_houses_and_residents(): void
    {
        $bendahara = User::where('role', 'bendahara_rt')->firstOrFail();
        $house = House::where('rt_id', $bendahara->rt_id)->firstOrFail();

        // Tidak bisa buka create modal (403)
        Livewire::actingAs($bendahara)
            ->test(HouseIndex::class)
            ->call('openCreateModal')
            ->assertForbidden();

        // Tidak bisa hapus rumah (403)
        Livewire::actingAs($bendahara)
            ->test(HouseIndex::class)
            ->call('confirmDelete', $house->id)
            ->assertForbidden();

        // Tidak bisa tambah warga (403)
        Livewire::actingAs($bendahara)
            ->test(HouseDetail::class, ['id' => $house->id])
            ->call('openCreateResidentModal')
            ->assertForbidden();
    }

    public function test_house_index_supports_sorting_and_status_filter(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();

        Livewire::actingAs($admin)
            ->test(HouseIndex::class)
            ->assertSet('sortBy', 'terbaru')
            ->set('sortBy', 'nomor_asc')
            ->assertSet('sortBy', 'nomor_asc')
            ->set('statusHuni', 'dihuni_pemilik')
            ->assertSet('statusHuni', 'dihuni_pemilik');
    }

    public function test_resident_index_supports_sorting(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();

        Livewire::actingAs($admin)
            ->test(ResidentIndex::class)
            ->assertSet('sortBy', 'terbaru')
            ->set('sortBy', 'nama_asc')
            ->assertSet('sortBy', 'nama_asc');
    }
}
