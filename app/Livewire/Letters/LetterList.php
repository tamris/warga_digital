<?php

namespace App\Livewire\Letters;

use App\Models\Letter;
use App\Models\Rt;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Register Surat Pengantar RT/RW')]
class LetterList extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $selectedRtId = null;

    public string $selectedYear = '';

    public string $selectedMonth = '';

    public string $selectedStatus = '';

    public int $perPage = 10;

    // State untuk Modal Pembatalan (oleh RT atau RW)
    public bool $showCancelModal = false;

    public ?int $letterIdToCancel = null;

    public string $cancelReason = '';

    // State untuk Modal Penolakan (oleh RT atau RW)
    public bool $showRejectModal = false;

    public ?int $letterIdToReject = null;

    public string $rejectReason = '';

    // State untuk Modal Persetujuan Tunggal (khusus oleh RW)
    public bool $showApproveModal = false;

    public ?int $letterIdToApprove = null;

    public ?Letter $letterToApprove = null;

    // State untuk Modal Persetujuan Masal (khusus oleh RW)
    public bool $showBulkApproveModal = false;

    // State untuk Modal Persetujuan Tunggal (khusus oleh Ketua RT)
    public bool $showApproveRtModal = false;

    public ?int $letterIdToApproveRt = null;

    public ?Letter $letterToApproveRt = null;

    // State untuk Modal Persetujuan Masal (khusus oleh Ketua RT)
    public bool $showBulkApproveRtModal = false;

    public function mount(): void
    {
        $this->selectedYear = (string) date('Y');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedRtId(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedYear(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedMonth(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Setujui dan Tanda Tangani Surat secara Elektronik oleh Ketua RT (TTE RT)
     */
    public function approveByRt(int $letterId): void
    {
        $user = auth()->user();
        if (! $user || (! $user->isAdminRw() && $user->role !== 'ketua_rt')) {
            abort(403, 'Hanya Ketua RT atau Admin RW yang dapat menyetujui di tingkat RT.');
        }

        $letter = Letter::findOrFail($letterId);

        if ($user->role === 'ketua_rt') {
            if ($letter->rt_id !== $user->rt_id || $letter->status !== 'menunggu_rt') {
                abort(403, 'Anda hanya berwenang menyetujui surat pengantar untuk wilayah RT Anda yang berstatus Menunggu Persetujuan RT.');
            }
        } else {
            if ($letter->status !== 'menunggu_rt') {
                abort(403, 'Surat ini tidak berstatus Menunggu Persetujuan RT.');
            }
        }

        $letter->update([
            'status' => 'menunggu_rw',
            'signed_by_rt_at' => now(),
        ]);

        session()->flash('success', "Surat No. {$letter->nomor_surat} berhasil disetujui & ditandatangani oleh Ketua RT! Diteruskan ke Ketua RW.");
    }

    /**
     * Buka modal persetujuan surat oleh Ketua RT
     */
    public function openApproveRtModal(int $letterId): void
    {
        $user = auth()->user();
        if (! $user || (! $user->isAdminRw() && $user->role !== 'ketua_rt')) {
            abort(403, 'Hanya Ketua RT atau Admin RW yang dapat menyetujui di tingkat RT.');
        }

        $letter = Letter::with('rt')->findOrFail($letterId);

        if ($user->role === 'ketua_rt') {
            if ($letter->rt_id !== $user->rt_id || $letter->status !== 'menunggu_rt') {
                abort(403, 'Anda hanya berwenang menyetujui surat pengantar untuk wilayah RT Anda yang berstatus Menunggu Persetujuan RT.');
            }
        } else {
            if ($letter->status !== 'menunggu_rt') {
                abort(403, 'Surat ini tidak berstatus Menunggu Persetujuan RT.');
            }
        }

        $this->letterToApproveRt = $letter;
        $this->letterIdToApproveRt = $this->letterToApproveRt->id;
        $this->showApproveRtModal = true;
    }

    /**
     * Konfirmasi persetujuan surat oleh Ketua RT dari modal dialog
     */
    public function confirmApproveRtLetter(): void
    {
        if ($this->letterIdToApproveRt) {
            $this->approveByRt($this->letterIdToApproveRt);
        }
        $this->showApproveRtModal = false;
        $this->letterIdToApproveRt = null;
        $this->letterToApproveRt = null;
    }

    /**
     * Buka modal persetujuan masal oleh Ketua RT
     */
    public function openBulkApproveRtModal(): void
    {
        $user = auth()->user();
        if (! $user || (! $user->isAdminRw() && $user->role !== 'ketua_rt')) {
            abort(403, 'Hanya Ketua RT atau Admin RW yang dapat menyetujui surat.');
        }

        $this->showBulkApproveRtModal = true;
    }

    /**
     * Konfirmasi persetujuan masal oleh Ketua RT dari modal dialog
     */
    public function confirmBulkApproveRt(): void
    {
        $this->approveAllPendingRt();
        $this->showBulkApproveRtModal = false;
    }

    /**
     * Setujui Semua Surat yang Menunggu TTE RT Sekaligus (Bulk Approval RT)
     */
    public function approveAllPendingRt(): void
    {
        $user = auth()->user();
        if (! $user || (! $user->isAdminRw() && $user->role !== 'ketua_rt')) {
            abort(403, 'Hanya Ketua RT atau Admin RW yang dapat menyetujui surat.');
        }

        $query = Letter::where('status', 'menunggu_rt');
        if (! $user->isAdminRw()) {
            $query->where('rt_id', $user->rt_id);
        }

        $count = $query->update([
            'status' => 'menunggu_rw',
            'signed_by_rt_at' => now(),
        ]);

        session()->flash('success', "Sebanyak {$count} Surat Pengantar berhasil disetujui & ditandatangani oleh Ketua RT! Diteruskan ke Ketua RW.");
    }

    /**
     * Setujui dan Tanda Tangani Surat secara Elektronik oleh Ketua RW
     */
    public function approveByRw(int $letterId): void
    {
        $user = auth()->user();
        if (! $user || ! $user->isAdminRw()) {
            abort(403, 'Hanya Ketua RW / Admin RW yang dapat menyetujui surat.');
        }

        $letter = Letter::where('status', 'menunggu_rw')->findOrFail($letterId);
        $letter->update([
            'status' => 'terbit',
            'signed_by_rw_at' => now(),
        ]);

        session()->flash('success', "Surat No. {$letter->nomor_surat} berhasil disetujui & ditandatangani secara elektronik (TTE RW)!");
    }

    /**
     * Buka modal persetujuan surat oleh RW
     */
    public function openApproveModal(int $letterId): void
    {
        $user = auth()->user();
        if (! $user || ! $user->isAdminRw()) {
            abort(403, 'Hanya Ketua RW / Admin RW yang dapat menyetujui surat.');
        }

        $this->letterToApprove = Letter::with('rt')->where('status', 'menunggu_rw')->findOrFail($letterId);
        $this->letterIdToApprove = $this->letterToApprove->id;
        $this->showApproveModal = true;
    }

    /**
     * Konfirmasi persetujuan surat oleh RW dari modal dialog
     */
    public function confirmApproveLetter(): void
    {
        if ($this->letterIdToApprove) {
            $this->approveByRw($this->letterIdToApprove);
        }
        $this->showApproveModal = false;
        $this->letterIdToApprove = null;
        $this->letterToApprove = null;
    }

    /**
     * Buka modal persetujuan masal oleh RW
     */
    public function openBulkApproveModal(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->isAdminRw()) {
            abort(403, 'Hanya Ketua RW / Admin RW yang dapat menyetujui surat.');
        }

        $this->showBulkApproveModal = true;
    }

    /**
     * Konfirmasi persetujuan masal oleh RW dari modal dialog
     */
    public function confirmBulkApprove(): void
    {
        $this->approveAllPending();
        $this->showBulkApproveModal = false;
    }

    /**
     * Setujui Semua Surat yang Menunggu TTE RW Sekaligus (Bulk Approval)
     */
    public function approveAllPending(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->isAdminRw()) {
            abort(403, 'Hanya Ketua RW / Admin RW yang dapat menyetujui surat.');
        }

        $count = Letter::where('status', 'menunggu_rw')->update([
            'status' => 'terbit',
            'signed_by_rw_at' => now(),
        ]);

        session()->flash('success', "Sebanyak {$count} Surat Pengantar berhasil disetujui dan disahkan sekaligus!");
    }

    /**
     * Buka modal penolakan surat (oleh RT atau RW)
     */
    public function openRejectModal(int $letterId): void
    {
        $user = auth()->user();
        if (! $user || (! $user->isAdminRw() && $user->role !== 'ketua_rt')) {
            abort(403, 'Anda tidak memiliki hak akses untuk menolak surat ini.');
        }

        $letter = Letter::findOrFail($letterId);

        if ($user->role === 'ketua_rt') {
            if ($letter->rt_id !== $user->rt_id || $letter->status !== 'menunggu_rt') {
                abort(403, 'Anda hanya dapat menolak surat berstatus Menunggu Persetujuan RT di wilayah RT Anda.');
            }
        } else {
            if (! in_array($letter->status, ['menunggu_rw', 'menunggu_rt'])) {
                abort(403, 'Surat ini tidak dalam status yang dapat ditolak.');
            }
        }

        $this->letterIdToReject = $letter->id;
        $this->rejectReason = '';
        $this->showRejectModal = true;
    }

    /**
     * Konfirmasi penolakan surat oleh RT atau RW
     */
    public function confirmRejectLetter(): void
    {
        $this->validate([
            'rejectReason' => 'required|string|min:5|max:500',
        ], [
            'rejectReason.required' => 'Alasan penolakan wajib diisi.',
            'rejectReason.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $user = auth()->user();
        if (! $user || (! $user->isAdminRw() && $user->role !== 'ketua_rt')) {
            abort(403, 'Anda tidak memiliki hak akses untuk menolak surat ini.');
        }

        $letter = Letter::findOrFail($this->letterIdToReject);

        if ($user->role === 'ketua_rt') {
            if ($letter->rt_id !== $user->rt_id || $letter->status !== 'menunggu_rt') {
                abort(403, 'Anda hanya dapat menolak surat berstatus Menunggu Persetujuan RT di wilayah RT Anda.');
            }
        } else {
            if (! in_array($letter->status, ['menunggu_rw', 'menunggu_rt'])) {
                abort(403, 'Surat ini tidak dalam status yang dapat ditolak.');
            }
        }

        $newStatus = $letter->status === 'menunggu_rt' ? 'ditolak_rt' : 'ditolak_rw';

        $letter->update([
            'status' => $newStatus,
            'alasan_tolak' => $this->rejectReason,
        ]);

        $this->showRejectModal = false;
        $this->letterIdToReject = null;
        $this->rejectReason = '';

        $rejectorLabel = $newStatus === 'ditolak_rt' ? 'Ketua RT' : 'Ketua RW';
        session()->flash('success', "Surat No. {$letter->nomor_surat} telah ditolak oleh {$rejectorLabel} dengan catatan.");
    }

    /**
     * Buka modal konfirmasi pembatalan surat
     */
    public function openCancelModal(int $letterId): void
    {
        $user = auth()->user();
        if (! $user || (! $user->isAdminRw() && $user->role !== 'ketua_rt')) {
            abort(403, 'Hanya Ketua RT atau Admin RW yang dapat membatalkan surat.');
        }

        $letter = Letter::accessibleBy($user)->findOrFail($letterId);
        $this->letterIdToCancel = $letter->id;
        $this->cancelReason = '';
        $this->showCancelModal = true;
    }

    /**
     * Proses pembatalan surat
     */
    public function confirmCancelLetter(): void
    {
        $this->validate([
            'cancelReason' => 'required|string|min:5|max:500',
        ], [
            'cancelReason.required' => 'Alasan pembatalan wajib diisi.',
            'cancelReason.min' => 'Alasan pembatalan minimal 5 karakter.',
        ]);

        $user = auth()->user();
        if (! $user || (! $user->isAdminRw() && $user->role !== 'ketua_rt')) {
            abort(403, 'Hanya Ketua RT atau Admin RW yang dapat membatalkan surat.');
        }

        $letter = Letter::accessibleBy($user)->findOrFail($this->letterIdToCancel);
        $letter->update([
            'status' => 'dibatalkan',
            'alasan_batal' => $this->cancelReason,
        ]);

        $this->showCancelModal = false;
        $this->letterIdToCancel = null;
        $this->cancelReason = '';

        session()->flash('success', "Surat No. {$letter->nomor_surat} berhasil dibatalkan.");
    }

    public function render(): View
    {
        $user = auth()->user();
        $rts = Rt::orderBy('nomor_rt')->get();

        $query = Letter::query()
            ->with(['rt', 'resident', 'creator'])
            ->accessibleBy($user, $this->selectedRtId);

        if ($this->search) {
            $s = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($s) {
                $q->where('nomor_surat', 'like', $s)
                    ->orWhere('snapshot_nama', 'like', $s)
                    ->orWhere('snapshot_nik', 'like', $s)
                    ->orWhere('jenis_keperluan', 'like', $s);
            });
        }

        if ($this->selectedYear) {
            $query->where('tahun', (int) $this->selectedYear);
        }

        if ($this->selectedMonth) {
            $query->whereMonth('tanggal_surat', (int) $this->selectedMonth);
        }

        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        $letters = $query->latest('id')->paginate($this->perPage);

        // Perhitungan Statistik Cepat
        $baseStatQuery = Letter::query()->accessibleBy($user, $this->selectedRtId);
        $totalTerbitTahunIni = (clone $baseStatQuery)
            ->where('tahun', (int) date('Y'))
            ->where('status', 'terbit')
            ->count();

        $totalMenungguRt = (clone $baseStatQuery)
            ->where('status', 'menunggu_rt')
            ->count();

        $totalMenungguRw = (clone $baseStatQuery)
            ->where('status', 'menunggu_rw')
            ->count();

        $totalPending = $totalMenungguRt + $totalMenungguRw;

        $totalTerbitBulanIni = (clone $baseStatQuery)
            ->whereYear('tanggal_surat', (int) date('Y'))
            ->whereMonth('tanggal_surat', (int) date('m'))
            ->where('status', 'terbit')
            ->count();

        $totalDibatalkan = (clone $baseStatQuery)
            ->where('tahun', (int) date('Y'))
            ->whereIn('status', ['dibatalkan', 'ditolak', 'ditolak_rt', 'ditolak_rw'])
            ->count();

        return view('livewire.letters.letter-list', [
            'user' => $user,
            'rts' => $rts,
            'letters' => $letters,
            'totalTerbitTahunIni' => $totalTerbitTahunIni,
            'totalMenungguRt' => $totalMenungguRt,
            'totalMenungguRw' => $totalMenungguRw,
            'totalPending' => $totalPending,
            'totalTerbitBulanIni' => $totalTerbitBulanIni,
            'totalDibatalkan' => $totalDibatalkan,
        ]);
    }
}
