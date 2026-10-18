<?php

namespace Tests\Feature;

use App\Livewire\Rts\RtIndex;
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
            ->assertSee('Kelola Master Data RT')
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

    public function test_non_admin_rw_cannot_access_rt_management(): void
    {
        $ketuaRt = User::where('role', 'ketua_rt')->firstOrFail();

        $this->actingAs($ketuaRt);

        $this->get(route('rts.index'))->assertForbidden();
    }
}
