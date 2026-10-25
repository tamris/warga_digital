<?php

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Livewire\Rts\RtIndex;
use App\Models\CashLedger;
use App\Models\Letter;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RtManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_rw_can_view_rt_and_accounts(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();

        $this->actingAs($admin);

        Livewire::test(RtIndex::class)
            ->assertOk()
            ->assertSee('Kelola Wilayah')
            ->assertSee('AGUNG JATMIKO')
            ->assertSee('DENY')
            ->assertSee('MUKHID')
            ->assertSee('SUBANDI');
    }

    public function test_admin_rw_can_edit_pengurus_account(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $targetUser = User::where('username', 'ketua_rt02')->firstOrFail();

        $this->actingAs($admin);

        Livewire::test(RtIndex::class)
            ->call('openEditUserModal', $targetUser->id)
            ->assertSet('editingUserId', $targetUser->id)
            ->assertSet('user_nama', 'DENY')
            ->set('user_nama', 'DENY KURNIAWAN')
            ->set('user_no_wa', '081299998888')
            ->call('saveUser')
            ->assertHasNoErrors()
            ->assertSet('showUserModal', false);

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'nama' => 'DENY KURNIAWAN',
            'no_wa' => '081299998888',
        ]);
    }

    public function test_admin_rw_can_create_and_delete_pengurus_account(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();

        $this->actingAs($admin);

        // Tambah Akun
        Livewire::test(RtIndex::class)
            ->call('openCreateUserModal', $rt1->id)
            ->set('selectedRtIdForUser', $rt1->id)
            ->set('user_nama', 'Hendra Pengurus Baru')
            ->set('user_username', 'hendra_rt01')
            ->set('user_password', 'password123')
            ->set('user_role', 'sekretaris_rt')
            ->set('user_no_wa', '081200001111')
            ->call('saveUser')
            ->assertHasNoErrors();

        $createdUser = User::where('username', 'hendra_rt01')->firstOrFail();
        $this->assertEquals('Hendra Pengurus Baru', $createdUser->nama);

        // Hapus Akun
        Livewire::test(RtIndex::class)
            ->call('confirmDeleteUser', $createdUser->id)
            ->assertSet('showDeleteUserModal', true)
            ->call('deleteUser')
            ->assertSet('showDeleteUserModal', false);

        $this->assertDatabaseMissing('users', [
            'id' => $createdUser->id,
        ]);
    }

    public function test_non_credential_manager_cannot_access_rt_management(): void
    {
        $sekretaris = User::where('role', 'sekretaris_rt')->first();
        if (! $sekretaris) {
            $sekretaris = User::create([
                'rt_id' => 1,
                'nama' => 'Sekre Test',
                'username' => 'sekre_test_forbidden',
                'password' => bcrypt('password'),
                'role' => 'sekretaris_rt',
                'no_wa' => '081234567899',
                'is_active' => true,
            ]);
        }

        $this->actingAs($sekretaris);

        $this->get(route('rts.index'))->assertForbidden();
    }

    public function test_admin_rw_can_create_pengurus_account_linked_to_resident(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $resident = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $rt1->id))->firstOrFail();

        $this->actingAs($admin);

        Livewire::test(RtIndex::class)
            ->call('openCreateUserModal', $rt1->id)
            ->set('selectedResidentId', $resident->id)
            ->assertSet('user_nama', $resident->nama_lengkap)
            ->set('user_username', 'budi_rt01')
            ->set('user_password', 'password123')
            ->set('user_role', 'sekretaris_rt')
            ->call('saveUser')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'username' => 'budi_rt01',
            'resident_id' => $resident->id,
            'nama' => $resident->nama_lengkap,
        ]);
    }

    public function test_admin_rw_can_link_existing_account_to_resident(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $rt2 = Rt::where('nomor_rt', '002')->firstOrFail();
        $targetUser = User::where('username', 'ketua_rt02')->firstOrFail();
        $resident = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $rt2->id))->firstOrFail();

        $this->actingAs($admin);

        Livewire::test(RtIndex::class)
            ->call('openEditUserModal', $targetUser->id)
            ->set('selectedResidentId', $resident->id)
            ->call('saveUser')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'resident_id' => $resident->id,
        ]);

        Livewire::test(RtIndex::class)
            ->assertSee($resident->nik);
    }

    public function test_admin_rw_can_search_and_select_resident_autocomplete(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $resident = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $rt1->id))->firstOrFail();

        $this->actingAs($admin);

        Livewire::test(RtIndex::class)
            ->call('openCreateUserModal', $rt1->id)
            ->set('searchResident', substr($resident->nama_lengkap, 0, 4))
            ->assertSee($resident->nama_lengkap)
            ->call('selectResident', $resident->id)
            ->assertSet('selectedResidentId', $resident->id)
            ->assertSet('user_nama', $resident->nama_lengkap)
            ->call('resetSelectedResident')
            ->assertSet('selectedResidentId', null)
            ->assertSet('user_nama', '')
            ->assertSet('user_username', '')
            ->assertSet('user_no_wa', '')
            ->assertSet('searchResident', '');
    }

    public function test_admin_rw_can_switch_tabs_and_view_rw_officers(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();

        $this->actingAs($admin);

        Livewire::test(RtIndex::class)
            ->assertSet('activeTab', 'rt')
            ->call('switchTab', 'rw')
            ->assertSet('activeTab', 'rw')
            ->assertSee('Daftar Pengurus Pusat Rukun Warga')
            ->assertSee('BAMBANG SUTRISNO')
            ->assertSee('HJ. ENDANG SUPRAPTI')
            ->assertSee('AMAT MAJA');
    }

    public function test_admin_rw_can_create_rw_officer_linked_to_resident(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $resident = Resident::where('status_warga', 'aktif')->firstOrFail();

        $this->actingAs($admin);

        Livewire::test(RtIndex::class)
            ->call('openCreateUserModal', null, true)
            ->assertSet('isRwOfficerModal', true)
            ->assertSet('selectedRtIdForUser', null)
            ->call('selectResident', $resident->id)
            ->assertSet('selectedResidentId', $resident->id)
            ->assertSet('user_nama', $resident->nama_lengkap)
            ->set('user_username', 'sekretaris_rw_baru')
            ->set('user_password', 'password123')
            ->set('user_role', 'sekretaris_rw')
            ->set('user_no_wa', '081399887766')
            ->call('saveUser')
            ->assertHasNoErrors()
            ->assertSet('showUserModal', false);

        $this->assertDatabaseHas('users', [
            'username' => 'sekretaris_rw_baru',
            'role' => 'sekretaris_rw',
            'resident_id' => $resident->id,
            'rt_id' => null,
            'nama' => $resident->nama_lengkap,
        ]);
    }

    public function test_admin_rw_can_edit_and_delete_rw_officer(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $sekretarisRw = User::where('username', 'sekretaris_rw')->firstOrFail();

        $this->actingAs($admin);

        // Edit
        Livewire::test(RtIndex::class)
            ->call('openEditUserModal', $sekretarisRw->id)
            ->assertSet('isRwOfficerModal', true)
            ->assertSet('editingUserId', $sekretarisRw->id)
            ->set('user_nama', 'BAMBANG SUTRISNO EDIT')
            ->set('user_no_wa', '081911223344')
            ->call('saveUser')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $sekretarisRw->id,
            'nama' => 'BAMBANG SUTRISNO EDIT',
            'no_wa' => '081911223344',
            'rt_id' => null,
        ]);

        // Hapus
        Livewire::test(RtIndex::class)
            ->call('confirmDeleteUser', $sekretarisRw->id)
            ->assertSet('showDeleteUserModal', true)
            ->call('deleteUser')
            ->assertSet('showDeleteUserModal', false);

        $this->assertDatabaseMissing('users', [
            'id' => $sekretarisRw->id,
        ]);
    }

    public function test_deleting_officer_who_created_letters_sets_foreign_key_to_null(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $targetUser = User::where('role', 'ketua_rt')->firstOrFail();
        $rt = Rt::findOrFail($targetUser->rt_id);

        $letter = Letter::create([
            'rt_id' => $rt->id,
            'resident_id' => null,
            'created_by_user_id' => $targetUser->id,
            'nomor_urut' => 999,
            'nomor_surat' => '999/RT.001/2026',
            'tahun' => 2026,
            'jenis_keperluan' => 'Surat Pengantar KTP',
            'keterangan_keperluan' => 'Untuk perpanjangan KTP',
            'snapshot_nama' => 'Warga Percobaan',
            'snapshot_nik' => '3171010101019999',
            'snapshot_no_kk' => '3171010101019999',
            'snapshot_ttl' => 'Jakarta, 01-01-1990',
            'snapshot_jenis_kelamin' => 'L',
            'snapshot_agama' => 'Islam',
            'snapshot_pekerjaan' => 'Karyawan',
            'snapshot_alamat' => 'Jl. Mawar No. 1',
            'nama_ketua_rt' => $targetUser->nama,
            'tanggal_surat' => now()->toDateString(),
            'status' => 'terbit',
        ]);

        $this->actingAs($admin);

        Livewire::test(RtIndex::class)
            ->call('confirmDeleteUser', $targetUser->id)
            ->assertSet('showDeleteUserModal', true)
            ->call('deleteUser')
            ->assertSet('showDeleteUserModal', false);

        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id,
        ]);

        $letter->refresh();
        $this->assertNull($letter->created_by_user_id);
    }

    public function test_audit_snapshots_survive_officer_renaming_and_deletion(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $officer = User::create([
            'nama' => 'AHMAD SHOLEH',
            'username' => 'ahmad_rt01',
            'password' => bcrypt('password'),
            'role' => 'ketua_rt',
            'no_wa' => '081234567890',
            'is_active' => true,
        ]);

        $this->actingAs($officer);

        $letter = Letter::create([
            'rt_id' => 1,
            'resident_id' => null,
            'created_by_user_id' => $officer->id,
            'nomor_urut' => 101,
            'nomor_surat' => '101/RT.001/2026',
            'tahun' => 2026,
            'jenis_keperluan' => 'Surat Keterangan Usaha',
            'snapshot_nama' => 'Warga Percobaan',
            'snapshot_nik' => '3171010101019999',
            'snapshot_no_kk' => '3171010101019999',
            'snapshot_ttl' => 'Tegal, 01-01-1990',
            'snapshot_jenis_kelamin' => 'L',
            'snapshot_agama' => 'Islam',
            'snapshot_pekerjaan' => 'Wiraswasta',
            'snapshot_alamat' => 'RT 001',
            'nama_ketua_rt' => 'AHMAD SHOLEH',
            'tanggal_surat' => now()->toDateString(),
            'status' => 'terbit',
        ]);

        $cash = CashLedger::create([
            'rt_id' => null,
            'user_id' => $officer->id,
            'jenis_transaksi' => 'masuk',
            'kategori' => 'Iuran Warga',
            'nominal' => 500000,
            'keterangan' => 'Iuran Warga Bulan Oktober',
            'tanggal' => now()->toDateString(),
        ]);

        $this->assertEquals('AHMAD SHOLEH', $letter->creator_name);
        $this->assertEquals('AHMAD SHOLEH', $cash->recorder_name);

        // Jika akun diubah namanya menjadi DADANG
        $officer->update(['nama' => 'DADANG SUKANDAR']);

        $letter->refresh();
        $cash->refresh();

        // Nama arsip lampau TIDAK ikut berubah jadi Dadang
        $this->assertEquals('AHMAD SHOLEH', $letter->creator_name);
        $this->assertEquals('AHMAD SHOLEH', $cash->recorder_name);

        // Jika akun Ahmad/Dadang dihapus permanen dari sistem
        $officer->delete();

        $letter->refresh();
        $cash->refresh();

        $this->assertNull($letter->created_by_user_id);
        $this->assertNull($cash->user_id);

        // Nama di arsip tetap 'AHMAD SHOLEH'
        $this->assertEquals('AHMAD SHOLEH', $letter->creator_name);
        $this->assertEquals('AHMAD SHOLEH', $cash->recorder_name);
    }

    public function test_admin_rw_can_toggle_officer_status_to_purna_tugas_and_block_login(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $officer = User::create([
            'nama' => 'PAK RT LAMA',
            'username' => 'rt_lama',
            'password' => bcrypt('password123'),
            'role' => 'ketua_rt',
            'no_wa' => '081234567890',
            'is_active' => true,
        ]);

        $this->assertTrue($officer->isActive());

        $this->actingAs($admin);

        // Toggle menjadi Purna Tugas (nonaktif)
        Livewire::test(RtIndex::class)
            ->call('toggleUserStatus', $officer->id)
            ->assertHasNoErrors();

        $officer->refresh();
        $this->assertFalse($officer->isActive());

        // Coba login sebagai pengurus yang sudah purna tugas -> harus ditolak
        auth()->logout();

        Livewire::test(Login::class)
            ->set('username', 'rt_lama')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors(['username']);

        $this->assertGuest();

        // Aktifkan kembali
        $this->actingAs($admin);

        Livewire::test(RtIndex::class)
            ->call('toggleUserStatus', $officer->id)
            ->assertHasNoErrors();

        $officer->refresh();
        $this->assertTrue($officer->isActive());

        // Login kembali berhasil
        auth()->logout();

        Livewire::test(Login::class)
            ->set('username', 'rt_lama')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($officer);
    }

    public function test_delete_modal_detects_history_and_allows_deactivate_instead(): void
    {
        $admin = User::where('role', 'admin_rw')->firstOrFail();
        $officer = User::create([
            'nama' => 'AHMAD PENGURUS',
            'username' => 'ahmad_pengurus',
            'password' => bcrypt('password'),
            'role' => 'ketua_rt',
            'no_wa' => '081234567890',
            'is_active' => true,
        ]);

        // Buat riwayat surat
        Letter::create([
            'rt_id' => 1,
            'resident_id' => null,
            'created_by_user_id' => $officer->id,
            'created_by_name' => 'AHMAD PENGURUS',
            'nomor_urut' => 202,
            'nomor_surat' => '202/RT.001/2026',
            'tahun' => 2026,
            'jenis_keperluan' => 'Surat Pengantar',
            'snapshot_nama' => 'Warga Percobaan',
            'snapshot_nik' => '3171010101019999',
            'snapshot_no_kk' => '3171010101019999',
            'snapshot_ttl' => 'Tegal, 01-01-1990',
            'snapshot_jenis_kelamin' => 'L',
            'snapshot_agama' => 'Islam',
            'snapshot_pekerjaan' => 'Wiraswasta',
            'snapshot_alamat' => 'RT 001',
            'nama_ketua_rt' => 'AHMAD PENGURUS',
            'tanggal_surat' => now()->toDateString(),
            'status' => 'terbit',
        ]);

        $this->actingAs($admin);

        // Buka modal delete -> terdeteksi punya riwayat
        Livewire::test(RtIndex::class)
            ->call('confirmDeleteUser', $officer->id)
            ->assertSet('showDeleteUserModal', true)
            ->assertSet('deletingUserHasHistory', true)
            ->call('deactivateFromDeleteModal')
            ->assertSet('showDeleteUserModal', false);

        // User TIDAK terhapus, melainkan dinonaktifkan (Purna Tugas)
        $this->assertDatabaseHas('users', [
            'id' => $officer->id,
            'is_active' => false,
        ]);
    }

    public function test_ketua_rt_can_access_rt_management_and_only_sees_own_rt(): void
    {
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $rt2 = Rt::where('nomor_rt', '002')->firstOrFail();
        $ketuaRt1 = User::where('role', 'ketua_rt')->where('rt_id', $rt1->id)->firstOrFail();
        $userRt2 = User::where('rt_id', $rt2->id)->firstOrFail();

        $this->actingAs($ketuaRt1);

        $this->get(route('rts.index'))->assertOk();

        Livewire::test(RtIndex::class)
            ->assertSet('activeTab', 'rt')
            ->assertSee('RT '.$rt1->nomor_rt)
            ->assertDontSee($userRt2->username);
    }

    public function test_ketua_rt_can_create_and_manage_staff_in_own_rt(): void
    {
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $ketuaRt1 = User::where('role', 'ketua_rt')->where('rt_id', $rt1->id)->firstOrFail();

        $this->actingAs($ketuaRt1);

        // Ketua RT creates sekretaris_rt
        Livewire::test(RtIndex::class)
            ->call('openCreateUserModal')
            ->assertSet('selectedRtIdForUser', $rt1->id)
            ->assertSet('user_role', 'sekretaris_rt')
            ->set('user_nama', 'Sekretaris Baru RT 01')
            ->set('user_username', 'sekre_baru_rt01')
            ->set('user_password', 'password123')
            ->set('user_no_wa', '081233445566')
            ->set('user_role', 'sekretaris_rt')
            ->call('saveUser')
            ->assertHasNoErrors();

        $createdUser = User::where('username', 'sekre_baru_rt01')->firstOrFail();
        $this->assertEquals($rt1->id, $createdUser->rt_id);
        $this->assertEquals('sekretaris_rt', $createdUser->role);
        $this->assertTrue($createdUser->is_active);

        // Ketua RT can toggle status
        Livewire::test(RtIndex::class)
            ->call('toggleUserStatus', $createdUser->id);

        $this->assertFalse($createdUser->fresh()->is_active);

        // Ketua RT can delete staff
        Livewire::test(RtIndex::class)
            ->call('confirmDeleteUser', $createdUser->id)
            ->call('deleteUser');

        $this->assertDatabaseMissing('users', ['id' => $createdUser->id]);
    }

    public function test_ketua_rt_cannot_manage_users_from_other_rts(): void
    {
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $rt2 = Rt::where('nomor_rt', '002')->firstOrFail();
        $ketuaRt1 = User::where('role', 'ketua_rt')->where('rt_id', $rt1->id)->firstOrFail();
        $userRt2 = User::where('rt_id', $rt2->id)->firstOrFail();

        $this->actingAs($ketuaRt1);

        // Cannot edit user of other RT
        Livewire::test(RtIndex::class)
            ->call('openEditUserModal', $userRt2->id)
            ->assertStatus(403);

        // Cannot delete user of other RT
        Livewire::test(RtIndex::class)
            ->call('confirmDeleteUser', $userRt2->id);

        $this->assertDatabaseHas('users', ['id' => $userRt2->id]);
    }

    public function test_ketua_rt_cannot_delete_or_deactivate_self(): void
    {
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $ketuaRt1 = User::where('role', 'ketua_rt')->where('rt_id', $rt1->id)->firstOrFail();

        $this->actingAs($ketuaRt1);

        Livewire::test(RtIndex::class)
            ->call('toggleUserStatus', $ketuaRt1->id);

        $this->assertTrue($ketuaRt1->fresh()->is_active);

        Livewire::test(RtIndex::class)
            ->call('confirmDeleteUser', $ketuaRt1->id);

        $this->assertDatabaseHas('users', ['id' => $ketuaRt1->id]);
    }
}
