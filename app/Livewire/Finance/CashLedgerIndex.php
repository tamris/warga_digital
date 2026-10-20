<?php

namespace App\Livewire\Finance;

use App\Models\CashLedger;
use App\Models\Rt;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Title('Buku Kas Operasional RT / RW')]
class CashLedgerIndex extends Component
{
    use WithFileUploads, WithPagination;

    #[Url(as: 'lingkup')]
    public string $scopeType = 'rw'; // 'rw' atau 'rt'

    #[Url(as: 'rt')]
    public ?int $selectedRtId = null;

    #[Url(as: 'jenis')]
    public string $jenisTransaksi = ''; // '', 'masuk', 'keluar'

    #[Url(as: 'kategori')]
    public string $kategori = '';

    #[Url(as: 'bulan')]
    public ?int $filterBulan = null;

    #[Url(as: 'tahun')]
    public int $filterTahun;

    #[Url(as: 'cari')]
    public string $search = '';

    // Properti Modal Catat Transaksi Baru
    public bool $showCreateModal = false;

    public string $form_jenis = 'masuk'; // 'masuk' atau 'keluar'

    public ?int $form_rt_id = null;

    public string $form_tanggal = '';

    public string $form_kategori = 'lainnya';

    public float $form_nominal = 0;

    public string $form_keterangan = '';

    public $form_bukti_foto = null;

    public bool $showPhotoModal = false;

    public ?string $photoUrl = null;

    public string $photoTitle = '';

    // Properti Modal Konfirmasi Hapus Transaksi
    public bool $showDeleteModal = false;

    public ?int $deletingTransactionId = null;

    public ?string $deletingTransactionDesc = null;

    public ?string $deletingTransactionNominal = null;

    public function mount(): void
    {
        abort_unless(auth()->user()?->canManageFinance(), 403, 'Akses ditolak. Halaman khusus pengurus keuangan.');

        $this->filterTahun = (int) date('Y');
        $this->filterBulan = (int) date('n');
        $this->form_tanggal = Carbon::today()->toDateString();

        $user = auth()->user();
        if ($user && ! $user->isRwLevel() && $user->rt_id) {
            $this->scopeType = 'rt';
            $this->selectedRtId = $user->rt_id;
            $this->form_rt_id = $user->rt_id;
        } else {
            $this->scopeType = 'rw';
            $this->form_rt_id = null;
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingScopeType(&$value): void
    {
        $user = auth()->user();
        if ($user && ! $user->isRwLevel()) {
            $value = 'rt';
        }
        $this->resetPage();
    }

    public function updatedScopeType(): void
    {
        $user = auth()->user();
        if ($user && ! $user->isRwLevel()) {
            $this->scopeType = 'rt';
        }
        $this->resetPage();
    }

    public function updatingSelectedRtId(&$value): void
    {
        $user = auth()->user();
        if ($user && ! $user->isRwLevel() && $user->rt_id) {
            $value = $user->rt_id;
        }
        $this->resetPage();
    }

    public function updatedSelectedRtId(): void
    {
        $user = auth()->user();
        if ($user && ! $user->isRwLevel() && $user->rt_id) {
            $this->selectedRtId = $user->rt_id;
        }
        $this->resetPage();
    }

    public function updatingJenisTransaksi(): void
    {
        $this->resetPage();
    }

    public function updatingKategori(): void
    {
        $this->resetPage();
    }

    public function updatingFilterBulan(): void
    {
        $this->resetPage();
    }

    public function updatingFilterTahun(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(string $jenis = 'masuk'): void
    {
        $user = auth()->user();
        if (! $user || ! $user->canManageFinance()) {
            session()->flash('error', 'Akses ditolak.');

            return;
        }

        $this->form_jenis = $jenis;
        $this->form_tanggal = Carbon::today()->toDateString();
        $this->form_nominal = 0;
        $this->form_keterangan = '';
        $this->form_bukti_foto = null;

        if ($user->isRwLevel()) {
            $this->form_rt_id = $this->scopeType === 'rt' ? $this->selectedRtId : null;
        } else {
            $this->form_rt_id = $user->rt_id;
        }

        $this->form_kategori = $jenis === 'masuk'
            ? ($this->form_rt_id ? 'donasi_warga' : 'setoran_rt')
            : ($this->form_rt_id ? 'gaji_keamanan' : 'acara_lingkungan');

        $this->showCreateModal = true;
    }

    public function saveTransaction(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->canManageFinance()) {
            session()->flash('error', 'Akses ditolak.');

            return;
        }

        $this->validate([
            'form_jenis' => 'required|in:masuk,keluar',
            'form_tanggal' => 'required|date',
            'form_kategori' => 'required|string|max:50',
            'form_nominal' => 'required|numeric|min:1000',
            'form_keterangan' => 'required|string|min:3|max:500',
            'form_bukti_foto' => 'nullable|image|max:3072',
        ], [
            'form_nominal.min' => 'Nominal transaksi minimal Rp 1.000.',
            'form_keterangan.required' => 'Keterangan transaksi wajib diisi secara jelas.',
            'form_bukti_foto.max' => 'Ukuran foto nota/kuitansi maksimal 3 MB.',
        ]);

        $photoPath = null;
        if ($this->form_bukti_foto instanceof UploadedFile) {
            $photoPath = $this->form_bukti_foto->store('cash-proofs', 'public');
        }

        $targetRtId = $user->isRwLevel() ? $this->form_rt_id : $user->rt_id;
        $isTransferToRw = ($this->form_jenis === 'keluar' && $this->form_kategori === 'setor_ke_rw' && $targetRtId !== null);

        CashLedger::create([
            'rt_id' => $targetRtId,
            'user_id' => $user->id,
            'house_due_id' => null,
            'jenis_transaksi' => $this->form_jenis,
            'kategori' => $this->form_kategori,
            'nominal' => $this->form_nominal,
            'tanggal' => $this->form_tanggal,
            'keterangan' => $this->form_keterangan,
            'bukti_foto' => $photoPath,
            'status_transfer' => $isTransferToRw ? 'menunggu_konfirmasi' : null,
        ]);

        $this->showCreateModal = false;
        $labelJenis = $this->form_jenis === 'masuk' ? 'Pemasukan Kas' : 'Pengeluaran Kas';
        $successMsg = $isTransferToRw
            ? 'Setoran ke RW sebesar Rp '.number_format($this->form_nominal, 0, ',', '.').' berhasil dicatat dan sedang menunggu konfirmasi penerimaan oleh Bendahara RW.'
            : "Transaksi {$labelJenis} sebesar Rp ".number_format($this->form_nominal, 0, ',', '.').' berhasil dicatat.';

        session()->flash('success', $successMsg);
    }

    public function deleteTransaction(int $id): void
    {
        $user = auth()->user();
        if (! $user || ! $user->canManageFinance()) {
            session()->flash('error', 'Akses ditolak.');

            return;
        }

        $ledger = CashLedger::findOrFail($id);

        if (! $user->isRwLevel() && $user->rt_id !== $ledger->rt_id) {
            session()->flash('error', 'Anda tidak memiliki hak akses untuk menghapus transaksi ini.');

            return;
        }

        // Jika transaksi ini terikat dengan pelunasan iuran, batalkan juga status iuran
        if ($ledger->house_due_id) {
            $ledger->houseDue?->delete();
        }

        // Jika transaksi ini terikat dengan setoran antar RT-RW
        if ($ledger->related_ledger_id) {
            $related = CashLedger::find($ledger->related_ledger_id);
            if ($related) {
                if ($ledger->rt_id === null && $related->rt_id !== null) {
                    $related->update([
                        'status_transfer' => 'menunggu_konfirmasi',
                        'related_ledger_id' => null,
                        'accepted_by_user_id' => null,
                        'accepted_at' => null,
                    ]);
                } elseif ($ledger->rt_id !== null && $related->rt_id === null) {
                    $related->delete();
                }
            }
        }

        if ($ledger->bukti_foto) {
            Storage::disk('public')->delete($ledger->bukti_foto);
        }

        $ket = $ledger->keterangan;
        $nom = $ledger->formatted_nominal;
        $ledger->delete();

        session()->flash('success', "Transaksi '{$ket}' ({$nom}) berhasil dihapus dari buku kas.");
    }

    public function acceptRtTransfer(int $ledgerId): void
    {
        $user = auth()->user();
        if (! $user || ! $user->canManageFinance() || ! $user->isRwLevel()) {
            session()->flash('error', 'Akses ditolak. Hanya pengurus RW yang dapat mengonfirmasi setoran RT.');

            return;
        }

        $rtTrx = CashLedger::with('rt')->findOrFail($ledgerId);

        if ($rtTrx->status_transfer !== 'menunggu_konfirmasi' || $rtTrx->kategori !== 'setor_ke_rw') {
            session()->flash('error', 'Transaksi ini tidak dalam status menunggu konfirmasi setoran.');

            return;
        }

        $rtNumber = $rtTrx->rt ? str_pad((string) ((int) $rtTrx->rt->nomor_rt), 2, '0', STR_PAD_LEFT) : 'RT';
        $rtName = "RT {$rtNumber}";

        // 1. Otomatis catat Kas Masuk di Kas Induk RW
        $rwTrx = CashLedger::create([
            'rt_id' => null,
            'user_id' => $user->id,
            'related_ledger_id' => $rtTrx->id,
            'jenis_transaksi' => 'masuk',
            'kategori' => 'setoran_rt',
            'nominal' => $rtTrx->nominal,
            'tanggal' => $rtTrx->tanggal ?: Carbon::today()->toDateString(),
            'keterangan' => "Penerimaan Setoran Wajib {$rtName} ({$rtTrx->keterangan})",
            'bukti_foto' => $rtTrx->bukti_foto,
        ]);

        // 2. Hubungkan transaksi asal RT
        $rtTrx->update([
            'status_transfer' => 'diterima',
            'related_ledger_id' => $rwTrx->id,
            'accepted_by_user_id' => $user->id,
            'accepted_at' => now(),
        ]);

        session()->flash('success', "Setoran dari {$rtName} sebesar {$rtTrx->formatted_nominal} berhasil diterima dan otomatis dicatat ke Buku Kas RW.");
    }

    public function rejectRtTransfer(int $ledgerId): void
    {
        $user = auth()->user();
        if (! $user || ! $user->canManageFinance() || ! $user->isRwLevel()) {
            session()->flash('error', 'Akses ditolak.');

            return;
        }

        $rtTrx = CashLedger::with('rt')->findOrFail($ledgerId);
        $rtNumber = $rtTrx->rt ? str_pad((string) ((int) $rtTrx->rt->nomor_rt), 2, '0', STR_PAD_LEFT) : 'RT';

        $rtTrx->update([
            'status_transfer' => 'ditolak',
        ]);

        session()->flash('success', "Setoran dari RT {$rtNumber} telah ditolak.");
    }

    public function confirmDelete(int $id): void
    {
        $ledger = CashLedger::findOrFail($id);
        $this->deletingTransactionId = $ledger->id;
        $this->deletingTransactionDesc = $ledger->clean_keterangan;
        $this->deletingTransactionNominal = $ledger->formatted_nominal;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingTransactionId = null;
        $this->deletingTransactionDesc = null;
        $this->deletingTransactionNominal = null;
    }

    public function executeDelete(): void
    {
        if ($this->deletingTransactionId) {
            $id = $this->deletingTransactionId;
            $this->cancelDelete();
            $this->deleteTransaction($id);
        }
    }

    public function viewPhoto(string $photoPath, string $title): void
    {
        $this->photoUrl = Storage::url($photoPath);
        $this->photoTitle = $title;
        $this->showPhotoModal = true;
    }

    public function render(): View
    {
        $user = auth()->user();

        // 1. Query Buku Kas
        $query = CashLedger::query()
            ->with(['rt', 'user', 'houseDue.house', 'relatedLedger.rt'])
            ->accessibleBy($user, $this->scopeType, $this->selectedRtId);

        if ($this->jenisTransaksi) {
            $query->where('jenis_transaksi', $this->jenisTransaksi);
        }

        if ($this->kategori) {
            $query->where('kategori', $this->kategori);
        }

        if ($this->filterTahun) {
            $query->whereYear('tanggal', $this->filterTahun);
        }

        if ($this->filterBulan) {
            $query->whereMonth('tanggal', $this->filterBulan);
        }

        if ($this->search) {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('keterangan', 'like', $term)
                    ->orWhere('kategori', 'like', $term);
            });
        }

        $transactions = $query->orderByDesc('tanggal')->orderByDesc('id')->paginate(15);

        // 2. Hitung Ringkasan Saldo Kas Dinamis (Akurat 100%)
        // Saldo Bulan Ini
        $scopeBaseQuery = CashLedger::accessibleBy($user, $this->scopeType, $this->selectedRtId);

        $monthQuery = (clone $scopeBaseQuery)
            ->whereYear('tanggal', $this->filterTahun)
            ->whereMonth('tanggal', $this->filterBulan ?: (int) date('n'));

        $totalMasukBulanIni = (float) (clone $monthQuery)->where('jenis_transaksi', 'masuk')->sum('nominal');
        $totalKeluarBulanIni = (float) (clone $monthQuery)->where('jenis_transaksi', 'keluar')->sum('nominal');

        // Saldo Total Kumulatif Aktif
        $totalMasukAllTime = (float) (clone $scopeBaseQuery)->where('jenis_transaksi', 'masuk')->sum('nominal');
        $totalKeluarAllTime = (float) (clone $scopeBaseQuery)->where('jenis_transaksi', 'keluar')->sum('nominal');
        $saldoKasAktif = $totalMasukAllTime - $totalKeluarAllTime;

        $rts = $user && $user->isRwLevel() ? Rt::orderBy('nomor_rt')->get() : collect();

        $pendingTransfers = collect();
        if ($user && $user->isRwLevel() && $this->scopeType === 'rw') {
            $pendingTransfers = CashLedger::with(['rt', 'user'])
                ->whereNotNull('rt_id')
                ->where('jenis_transaksi', 'keluar')
                ->where('kategori', 'setor_ke_rw')
                ->where('status_transfer', 'menunggu_konfirmasi')
                ->orderByDesc('tanggal')
                ->get();
        }

        return view('livewire.finance.cash-ledger-index', [
            'transactions' => $transactions,
            'totalMasukBulanIni' => $totalMasukBulanIni,
            'totalKeluarBulanIni' => $totalKeluarBulanIni,
            'saldoKasAktif' => $saldoKasAktif,
            'rts' => $rts,
            'user' => $user,
            'pendingTransfers' => $pendingTransfers,
        ]);
    }
}
