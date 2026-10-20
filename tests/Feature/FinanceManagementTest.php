<?php

namespace Tests\Feature;

use App\Livewire\Finance\CashLedgerIndex;
use App\Livewire\Finance\DuesMatrix;
use App\Models\CashLedger;
use App\Models\House;
use App\Models\HouseDue;
use App\Models\Rt;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinanceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_cannot_access_finance_pages(): void
    {
        $this->get(route('finance.dues'))->assertRedirect(route('login'));
        $this->get(route('finance.cash-ledger'))->assertRedirect(route('login'));
    }

    public function test_user_without_finance_role_is_forbidden(): void
    {
        $sekretarisRt = User::where('username', 'sekretaris_rt01')->firstOrFail();

        $this->actingAs($sekretarisRt);

        $this->get(route('finance.dues'))->assertForbidden();
        $this->get(route('finance.cash-ledger'))->assertForbidden();
    }

    public function test_bendahara_rt_is_locked_to_their_own_rt(): void
    {
        $bendahara = User::where('username', 'bendahara_rt01')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();

        $this->actingAs($bendahara);

        Livewire::test(DuesMatrix::class)
            ->assertOk()
            ->assertSet('selectedRtId', $rt1->id)
            ->set('selectedRtId', 999) // Attempt unauthorized switch
            ->assertSet('selectedRtId', $rt1->id); // Locked back to RT 01
    }

    public function test_quick_dues_payment_creates_due_and_auto_cash_ledger(): void
    {
        $bendahara = User::where('username', 'bendahara_rt01')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $house = House::where('rt_id', $rt1->id)->firstOrFail();

        $this->actingAs($bendahara);

        // Ensure month 11 is not paid yet
        HouseDue::where('house_id', $house->id)
            ->where('tahun', 2026)
            ->where('bulan', 11)
            ->delete();

        Livewire::test(DuesMatrix::class)
            ->call('openPayModal', $house->id, 11)
            ->assertSet('showPayModal', true)
            ->assertSet('payHouseId', $house->id)
            ->set('payMethod', 'tunai')
            ->set('payNotes', 'Uji pembayaran bulan 11')
            ->call('savePayment')
            ->assertHasNoErrors()
            ->assertSet('showPayModal', false);

        // Verify house_dues record
        $this->assertDatabaseHas('house_dues', [
            'house_id' => $house->id,
            'tahun' => 2026,
            'bulan' => 11,
            'status' => 'lunas',
            'metode_bayar' => 'tunai',
        ]);

        // Verify auto-created cash ledger entry
        $this->assertDatabaseHas('cash_ledgers', [
            'rt_id' => $rt1->id,
            'jenis_transaksi' => 'masuk',
            'kategori' => 'iuran_warga',
        ]);
    }

    public function test_void_dues_payment_removes_due_and_voids_cash_ledger(): void
    {
        $bendahara = User::where('username', 'bendahara_rt01')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();
        $house = House::where('rt_id', $rt1->id)->firstOrFail();

        $this->actingAs($bendahara);

        // Find a paid due to void
        $due = HouseDue::where('house_id', $house->id)
            ->where('tahun', 2026)
            ->firstOrFail();

        $dueId = $due->id;
        $ledgerId = $due->cash_ledger_id;

        Livewire::test(DuesMatrix::class)
            ->call('voidPayment', $dueId)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('house_dues', [
            'id' => $dueId,
        ]);

        if ($ledgerId) {
            $this->assertDatabaseMissing('cash_ledgers', [
                'id' => $ledgerId,
            ]);
        }
    }

    public function test_bendahara_can_record_operational_expense(): void
    {
        $bendahara = User::where('username', 'bendahara_rt01')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();

        $this->actingAs($bendahara);

        Livewire::test(CashLedgerIndex::class)
            ->assertOk()
            ->call('openCreateModal', 'keluar')
            ->set('form_nominal', 75000)
            ->set('form_kategori', 'kebersihan_sampah')
            ->set('form_keterangan', 'Beli sapu lidi dan kantong sampah RT')
            ->set('form_tanggal', '2026-10-19')
            ->call('saveTransaction')
            ->assertHasNoErrors()
            ->assertSet('showCreateModal', false);

        $this->assertDatabaseHas('cash_ledgers', [
            'rt_id' => $rt1->id,
            'jenis_transaksi' => 'keluar',
            'nominal' => 75000,
            'kategori' => 'kebersihan_sampah',
        ]);
    }

    public function test_bendahara_rw_can_toggle_between_rw_and_rt_cash(): void
    {
        $bendaharaRw = User::where('username', 'bendahara_rw')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();

        $this->actingAs($bendaharaRw);

        Livewire::test(CashLedgerIndex::class)
            ->assertOk()
            ->set('scopeType', 'rw')
            ->assertSet('scopeType', 'rw')
            ->set('scopeType', 'rt')
            ->set('selectedRtId', $rt1->id)
            ->assertSet('selectedRtId', $rt1->id);
    }

    public function test_bendahara_rw_has_read_only_monitoring_on_dues_matrix(): void
    {
        $bendaharaRw = User::where('username', 'bendahara_rw')->firstOrFail();
        $house = House::firstOrFail();

        $this->actingAs($bendaharaRw);

        // Can access the page
        $this->get(route('finance.dues'))->assertOk();

        // Livewire component asserts
        $test = Livewire::test(DuesMatrix::class)
            ->assertOk()
            ->assertSee('Mode Pemantauan');

        // Attempting to open pay modal is blocked
        $test->call('openPayModal', $house->id, 12)
            ->assertSet('showPayModal', false);

        // Attempting to save payment is blocked
        $test->call('savePayment')
            ->assertSet('showPayModal', false);

        // Attempting to void payment is blocked and record remains intact
        $due = HouseDue::first();
        if ($due) {
            $dueId = $due->id;
            $test->call('voidPayment', $dueId);
            $this->assertDatabaseHas('house_dues', ['id' => $dueId]);
        }

        // Attempting to open settings modal is blocked
        $test->call('openSettingsModal')
            ->assertSet('showSettingsModal', false);
    }

    public function test_rt_transfer_to_rw_creates_pending_status_and_rw_one_click_accept(): void
    {
        $bendaharaRt = User::where('username', 'bendahara_rt01')->firstOrFail();
        $bendaharaRw = User::where('username', 'bendahara_rw')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();

        // 1. Bendahara RT catat pengeluaran setor kas ke RW
        $this->actingAs($bendaharaRt);

        Livewire::test(CashLedgerIndex::class)
            ->call('openCreateModal', 'keluar')
            ->set('form_nominal', 350000)
            ->set('form_kategori', 'setor_ke_rw')
            ->set('form_keterangan', 'Setoran kas wajib bulanan RT 01 bulan Oktober 2026')
            ->set('form_tanggal', '2026-10-20')
            ->call('saveTransaction')
            ->assertHasNoErrors();

        $rtTrx = CashLedger::where('rt_id', $rt1->id)
            ->where('kategori', 'setor_ke_rw')
            ->where('nominal', 350000)
            ->latest('id')
            ->firstOrFail();

        $this->assertSame('menunggu_konfirmasi', $rtTrx->status_transfer);
        $this->assertNull($rtTrx->related_ledger_id);

        // 2. Bendahara RT TIDAK BOLEH mengonfirmasi sendiri (harus ditolak)
        Livewire::test(CashLedgerIndex::class)
            ->call('acceptRtTransfer', $rtTrx->id);

        $this->assertSame('menunggu_konfirmasi', $rtTrx->fresh()->status_transfer);
        $this->assertNull($rtTrx->fresh()->related_ledger_id);

        // 3. Bendahara RW login & menerima setoran dengan 1-Klik
        $this->actingAs($bendaharaRw);

        Livewire::test(CashLedgerIndex::class)
            ->set('scopeType', 'rw')
            ->call('acceptRtTransfer', $rtTrx->id)
            ->assertHasNoErrors();

        $rtTrx->refresh();
        $this->assertSame('diterima', $rtTrx->status_transfer);
        $this->assertSame($bendaharaRw->id, $rtTrx->accepted_by_user_id);
        $this->assertNotNull($rtTrx->accepted_at);
        $this->assertNotNull($rtTrx->related_ledger_id);

        // Pastikan Kas Masuk otomatis tercipta di Buku Kas RW
        $rwTrx = CashLedger::find($rtTrx->related_ledger_id);
        $this->assertNotNull($rwTrx);
        $this->assertNull($rwTrx->rt_id); // Kas Induk RW
        $this->assertSame('masuk', $rwTrx->jenis_transaksi);
        $this->assertSame('setoran_rt', $rwTrx->kategori);
        $this->assertEquals(350000, (float) $rwTrx->nominal);
        $this->assertSame($rtTrx->id, $rwTrx->related_ledger_id);
    }

    public function test_bendahara_rw_can_reject_rt_transfer(): void
    {
        $bendaharaRt = User::where('username', 'bendahara_rt01')->firstOrFail();
        $bendaharaRw = User::where('username', 'bendahara_rw')->firstOrFail();
        $rt1 = Rt::where('nomor_rt', '001')->firstOrFail();

        $this->actingAs($bendaharaRt);

        $rtTrx = CashLedger::create([
            'rt_id' => $rt1->id,
            'user_id' => $bendaharaRt->id,
            'jenis_transaksi' => 'keluar',
            'kategori' => 'setor_ke_rw',
            'nominal' => 200000,
            'tanggal' => '2026-10-20',
            'keterangan' => 'Setoran salah nominal',
            'status_transfer' => 'menunggu_konfirmasi',
        ]);

        $this->actingAs($bendaharaRw);

        Livewire::test(CashLedgerIndex::class)
            ->set('scopeType', 'rw')
            ->call('rejectRtTransfer', $rtTrx->id)
            ->assertHasNoErrors();

        $this->assertSame('ditolak', $rtTrx->fresh()->status_transfer);
        $this->assertNull($rtTrx->fresh()->related_ledger_id);
    }
}
