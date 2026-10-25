<?php

namespace App\Livewire\SocialAid;

use App\Models\House;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\SocialAidProgram;
use App\Models\SocialAidRecipient;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Detail & Penyaluran Program Bantuan Sosial')]
class ProgramDetail extends Component
{
    use WithFileUploads;

    public SocialAidProgram $program;

    #[Url(as: 'tab')]
    public string $activeTab = 'usulan'; // usulan, sanggahan, penyaluran

    #[Url(as: 'rt')]
    public ?int $selectedRtId = null;

    // State Modal Usulkan Warga (Tab A)
    public bool $showProposeModal = false;

    /** @var array<int, bool> [house_id => bool] */
    public array $selectedHouseIds = [];

    // Filter kategori giliran di modal usulan (all, never, ready, cooldown)
    public string $candidateFilter = 'all';

    // State Modal Penggantian / Sanggahan (Tab B)
    public bool $showReplaceModal = false;

    public ?int $targetRecipientId = null;

    public ?string $targetRecipientName = null;

    public string $alasan_penggantian = 'meninggal';

    public ?int $digantikan_oleh_resident_id = null;

    public ?string $catatan_penggantian = '';

    // State Modal Serah Terima Bantuan Hari-H (Tab C)
    public bool $showDeliveryModal = false;

    public ?int $deliveringRecipientId = null;

    public ?string $deliveringRecipientName = null;

    public string $penerima_aktual = '';

    public string $status_penyerahan = 'sudah_diterima';

    public $bukti_foto = null;

    // State Modal Konfirmasi Hapus Usulan
    public bool $showDeleteModal = false;

    public ?int $deletingRecipientId = null;

    public ?string $deletingRecipientName = null;

    public ?string $deletingRecipientKupon = null;

    // State Modal Konfirmasi Pengesahan Massal RW
    public bool $showApproveAllModal = false;

    // Status Program untuk Dropdown Kontrol RW
    public string $currentProgramStatus = '';

    public function mount(SocialAidProgram $program): void
    {
        $user = auth()->user();
        if (! $user || ! $user->canManageSocialAid()) {
            abort(403, 'Akses terbatas untuk Pengurus Lingkungan RT/RW.');
        }

        $this->program = $program->load(['quotas.rt', 'recipients.resident', 'recipients.house', 'recipients.replacementResident']);

        if (! $user->isRwLevel() && $user->rt_id) {
            $this->selectedRtId = $user->rt_id;
        } else {
            // Untuk RW: default ke 'Semua RT' (null) agar langsung melihat rekapitulasi seluruh usulan
            $this->selectedRtId = request()->has('rt') && request()->query('rt') ? (int) request()->query('rt') : null;
        }

        // Sinkronisasi otomatis siklus hidup program:
        // 1. Pendataan RT (semua RT isi slot) -> 2. Verifikasi RW (RW acc semua) -> 3. Penyaluran (sesuai jadwal & acc RW) -> 4. Selesai & Arsip
        $this->syncLifecycleStatus();
        $this->currentProgramStatus = $this->program->status_program;

        if ($this->activeTab === 'penyaluran' && ! in_array($this->program->status_program, ['penyaluran', 'selesai'])) {
            $this->activeTab = 'usulan';
        }
    }

    public function syncLifecycleStatus(): void
    {
        $this->program->load(['quotas.rt', 'recipients']);

        $deliveredCount = $this->program->recipients()
            ->where('status_usulan', 'disetujui_rw')
            ->where('status_penyerahan', '!=', 'belum_diambil')
            ->count();

        $allQuotasFilled = $this->program->areAllRtQuotasFilled();
        $allProposalsApproved = $this->program->areAllProposalsApproved();
        $allDeliveriesCompleted = $this->program->areAllDeliveriesCompleted();

        // 1. TAHAP PENYERAHAN / SELESAI (Jika sudah ada bantuan yang diserahkan)
        if ($deliveredCount > 0) {
            if ($allDeliveriesCompleted) {
                if ($this->program->status_program !== 'selesai') {
                    $this->program->update(['status_program' => 'selesai']);
                    $this->program->refresh();
                    $this->currentProgramStatus = 'selesai';
                    session()->flash('success', "Seluruh bantuan ({$this->program->total_kuota} paket) telah selesai diserahkan dan ditandatangani! Status program otomatis dialihkan ke 'Tahap 4: Selesai & Arsip'.");
                }
            } else {
                if ($this->program->status_program !== 'penyaluran') {
                    $this->program->update(['status_program' => 'penyaluran']);
                    $this->program->refresh();
                    $this->currentProgramStatus = 'penyaluran';
                }
            }

            return;
        }

        // 2. TAHAP PERENCANAAN / PENYALURAN BELUM DIMULAI (deliveredCount === 0)
        // A. Jika kuota RT belum terisi lengkap (misal baru 6 dari 12 KK)
        if (! $allQuotasFilled) {
            if ($this->program->status_program !== 'pendataan_rt') {
                $this->program->update(['status_program' => 'pendataan_rt']);
                $this->program->refresh();
                $this->currentProgramStatus = 'pendataan_rt';
                $this->activeTab = 'usulan';

                $currentTotal = $this->program->recipients()->where('status_usulan', '!=', 'diganti')->count();
                session()->flash('warning', "Slot usulan kuota RT belum lengkap terisi ({$currentTotal}/{$this->program->total_kuota} KK). Status program dikembalikan ke 'Tahap 1: Pendataan RT'.");
            }

            return;
        }

        // B. Jika seluruh kuota RT sudah lengkap terisi dan seluruh usulan telah disetujui (ACC) oleh RW
        if ($allProposalsApproved) {
            $isDue = $this->program->isDistributionDue();

            // Jika tanggal penyaluran sudah tiba (Hari-H atau lewat)
            if ($isDue) {
                if ($this->program->status_program !== 'penyaluran') {
                    $this->program->update(['status_program' => 'penyaluran']);
                    $this->program->refresh();
                    $this->currentProgramStatus = 'penyaluran';
                    $this->activeTab = 'penyaluran';
                    session()->flash('success', "Seluruh usulan warga ({$this->program->total_kuota} KK) telah disetujui RW dan jadwal penyaluran telah tiba! Status program otomatis masuk ke 'Tahap 3: Penyaluran (Hari-H)'.");
                }

                return;
            }

            // Jika belum hari-H tapi sudah dibuka secara manual oleh RW
            if ($this->program->status_program === 'penyaluran') {
                return;
            }

            // Jika belum hari-H dan belum dibuka manual, tetap di verifikasi_rw (Siap Disalurkan)
            if ($this->program->status_program !== 'verifikasi_rw') {
                $this->program->update(['status_program' => 'verifikasi_rw']);
                $this->program->refresh();
                $this->currentProgramStatus = 'verifikasi_rw';
                if ($this->activeTab === 'penyaluran') {
                    $this->activeTab = 'usulan';
                }
            }

            return;
        }

        // C. Jika kuota RT sudah lengkap (12/12) tapi masih ada usulan yang menunggu verifikasi atau ditolak RW
        if ($this->program->status_program !== 'verifikasi_rw') {
            $this->program->update(['status_program' => 'verifikasi_rw']);
            $this->program->refresh();
            $this->currentProgramStatus = 'verifikasi_rw';
            if ($this->activeTab === 'penyaluran') {
                $this->activeTab = 'usulan';
            }
            session()->flash('info', "Seluruh RT telah mengisi warga sesuai alokasi slot kuota ({$this->program->total_kuota} KK)! Status program masuk ke 'Tahap 2: Verifikasi RW'.");
        }
    }

    public function setTab(string $tab): void
    {
        if ($tab === 'penyaluran' && ! in_array($this->program->status_program, ['penyaluran', 'selesai'])) {
            $this->activeTab = 'usulan';

            return;
        }

        $this->activeTab = $tab;
    }

    public function updatedCurrentProgramStatus(string $val): void
    {
        $this->updateProgramStatus($val);
    }

    public function updateProgramStatus(string $newStatus): void
    {
        $user = auth()->user();
        if (! $user->isRwLevel()) {
            abort(403, 'Hanya Pengurus RW yang berwenang mengubah status program bantuan.');
        }

        if (! in_array($newStatus, ['pendataan_rt', 'verifikasi_rw', 'penyaluran', 'selesai'], true)) {
            return;
        }

        $this->program->update(['status_program' => $newStatus]);
        $this->program->refresh();
        $this->currentProgramStatus = $this->program->status_program;
        session()->flash('success', "Status program berhasil diubah menjadi: {$this->program->status_label}.");
    }

    // ==========================================
    // TAB A: USULAN PENERIMA SESUAI KUOTA
    // ==========================================

    public function openProposeModal(): void
    {
        if (! in_array($this->program->status_program, ['pendataan_rt', 'verifikasi_rw'])) {
            session()->flash('error', 'Penambahan atau perubahan usulan penerima hanya dapat dilakukan pada tahap Pendataan RT dan Verifikasi RW.');

            return;
        }

        $user = auth()->user();
        $targetRtId = $user->isRwLevel() ? $this->selectedRtId : $user->rt_id;

        // Jika RW sedang di view 'Semua RT', default ke RT pertama
        if (! $targetRtId) {
            $firstRt = Rt::orderBy('nomor_rt')->first();
            $this->selectedRtId = $firstRt?->id;
            $targetRtId = $this->selectedRtId;
        }

        if (! $targetRtId) {
            return;
        }

        $quota = $this->program->getQuotaForRt($targetRtId);
        $currentCount = $this->program->recipients()->where('rt_id', $targetRtId)->where('status_usulan', '!=', 'diganti')->count();

        if ($currentCount >= $quota) {
            session()->flash('warning', "Kuota RT ini sudah terpenuhi ({$currentCount}/{$quota} KK). Tambah kuota atau batalkan usulan sebelumnya.");
        }

        // Checklist ID rumah yang sudah diusulkan
        $alreadyProposed = $this->program->recipients()->where('rt_id', $targetRtId)->where('status_usulan', '!=', 'diganti')->pluck('house_id')->toArray();
        $this->selectedHouseIds = array_fill_keys($alreadyProposed, true);
        $this->candidateFilter = 'all';

        $this->showProposeModal = true;
    }

    public function autoSelectByFairness(): void
    {
        if (! in_array($this->program->status_program, ['pendataan_rt', 'verifikasi_rw'])) {
            session()->flash('error', 'Penambahan atau perubahan usulan penerima hanya dapat dilakukan pada tahap Pendataan RT dan Verifikasi RW.');

            return;
        }

        $user = auth()->user();
        $targetRtId = $user->isRwLevel() ? $this->selectedRtId : $user->rt_id;

        if (! $targetRtId) {
            return;
        }

        $quota = $this->program->getQuotaForRt($targetRtId);
        if ($quota <= 0) {
            session()->flash('warning', 'Kuota untuk RT ini belum ditentukan.');

            return;
        }

        $candidates = House::where('rt_id', $targetRtId)
            ->whereHas('residents', fn ($q) => $q->where('status_warga', 'aktif'))
            ->with(['socialProfile', 'headOfFamily', 'residents', 'socialAidRecipients.program'])
            ->get()
            ->map(function (House $h) {
                $h->rotation_metrics = $h->getAidRotationMetrics($this->program->id);
                $h->fairness_score = $h->rotation_metrics['fairness_score'];

                return $h;
            })
            ->sortByDesc(fn (House $h) => $h->fairness_score);

        $topCandidates = $candidates->take($quota);

        $this->selectedHouseIds = [];
        foreach ($topCandidates as $c) {
            $this->selectedHouseIds[$c->id] = true;
        }

        session()->flash('info', "Otomatis memilih {$topCandidates->count()} KK teratas berdasarkan sistem rotasi giliran berkeadilan dan kuota RT ({$quota} KK).");
    }

    public function clearCandidateSelection(): void
    {
        $this->selectedHouseIds = [];
    }

    public function saveProposedRecipients(): void
    {
        if (! in_array($this->program->status_program, ['pendataan_rt', 'verifikasi_rw'])) {
            session()->flash('error', 'Penambahan atau perubahan usulan penerima hanya dapat dilakukan pada tahap Pendataan RT dan Verifikasi RW.');

            return;
        }

        $user = auth()->user();
        $targetRtId = $user->isRwLevel() ? $this->selectedRtId : $user->rt_id;

        if (! $targetRtId) {
            return;
        }

        $quota = $this->program->getQuotaForRt($targetRtId);
        $selectedIds = array_keys(array_filter($this->selectedHouseIds));

        if (count($selectedIds) > $quota) {
            $this->addError('selectedHouseIds', "Maksimal usulan untuk RT ini adalah {$quota} KK sesuai jatah kuota.");

            return;
        }

        // Sinkronisasi penerima
        $existingRecipients = $this->program->recipients()->where('rt_id', $targetRtId)->get();

        // 1. Hapus yang di-uncheck (jika belum diserahkan)
        foreach ($existingRecipients as $ex) {
            if (! in_array($ex->house_id, $selectedIds) && $ex->status_penyerahan === 'belum_diambil') {
                $ex->delete();
            }
        }

        // 2. Tambah yang baru
        $existingHouseIds = $existingRecipients->pluck('house_id')->toArray();
        $rtObj = Rt::find($targetRtId);
        $rtStr = str_pad((string) ((int) ($rtObj?->nomor_rt ?? $targetRtId)), 2, '0', STR_PAD_LEFT);

        $kuponNumber = 1;
        foreach ($selectedIds as $houseId) {
            if (! in_array($houseId, $existingHouseIds)) {
                $house = House::with('headOfFamily', 'residents')->find($houseId);
                $resident = $house?->headOfFamily ?? $house?->residents->first();

                if ($resident) {
                    $kuponStr = "RT{$rtStr}-".str_pad((string) $kuponNumber, 3, '0', STR_PAD_LEFT);
                    SocialAidRecipient::create([
                        'program_id' => $this->program->id,
                        'rt_id' => $targetRtId,
                        'house_id' => $house->id,
                        'resident_id' => $resident->id,
                        'nomor_urut_kupon' => $kuponStr,
                        'status_usulan' => 'diusulkan_rt',
                        'status_penyerahan' => 'belum_diambil',
                    ]);
                }
            }
            $kuponNumber++;
        }

        $this->showProposeModal = false;
        $this->program->load('recipients.resident', 'recipients.house');
        session()->flash('success', 'Daftar usulan calon penerima RT berhasil diperbarui.');

        $this->syncLifecycleStatus();
    }

    public function removeRecipient(int $recipientId): void
    {
        if (in_array($this->program->status_program, ['penyaluran', 'selesai'])) {
            session()->flash('error', 'Penerima tidak dapat dihapus karena program telah memasuki tahap penyaluran atau telah selesai.');

            return;
        }

        $recipient = SocialAidRecipient::findOrFail($recipientId);
        $user = auth()->user();

        if (! $user->isRwLevel() && $recipient->rt_id !== $user->rt_id) {
            abort(403);
        }

        // Jika usulan sudah disetujui RW, RT tidak bisa sembarangan menghapus
        if (! $user->isRwLevel() && $recipient->status_usulan === 'disetujui_rw') {
            session()->flash('error', 'Usulan ini sudah disahkan & dikunci oleh RW. Pengurus RW harus membatalkan pengesahan (↺) terlebih dahulu jika ingin menghapus atau mengganti penerima.');

            return;
        }

        $recipient->delete();
        $this->program->load('recipients');
        session()->flash('success', 'Usulan calon penerima berhasil dihapus.');

        $this->syncLifecycleStatus();
    }

    public function confirmRemoveRecipient(int $recipientId): void
    {
        if (in_array($this->program->status_program, ['penyaluran', 'selesai'])) {
            session()->flash('error', 'Penerima tidak dapat dihapus karena program telah memasuki tahap penyaluran atau telah selesai.');

            return;
        }

        $recipient = SocialAidRecipient::with(['resident', 'house'])->findOrFail($recipientId);
        $user = auth()->user();

        if (! $user->isRwLevel() && $recipient->rt_id !== $user->rt_id) {
            abort(403);
        }

        if (! $user->isRwLevel() && $recipient->status_usulan === 'disetujui_rw') {
            session()->flash('error', 'Usulan ini sudah disahkan & dikunci oleh RW. Pengurus RW harus membatalkan pengesahan (↺) terlebih dahulu jika ingin menghapus atau mengganti penerima.');

            return;
        }

        $this->deletingRecipientId = $recipient->id;
        $this->deletingRecipientName = $recipient->resident?->nama_lengkap ?? 'Warga';
        $this->deletingRecipientKupon = $recipient->nomor_urut_kupon ?? '-';
        $this->showDeleteModal = true;
    }

    public function cancelRemoveRecipient(): void
    {
        $this->showDeleteModal = false;
        $this->deletingRecipientId = null;
        $this->deletingRecipientName = null;
        $this->deletingRecipientKupon = null;
    }

    public function executeRemoveRecipient(): void
    {
        if ($this->deletingRecipientId) {
            $id = $this->deletingRecipientId;
            $this->showDeleteModal = false;
            $this->deletingRecipientId = null;
            $this->deletingRecipientName = null;
            $this->deletingRecipientKupon = null;
            $this->removeRecipient($id);
        }
    }

    // ==========================================
    // ALUR PERSETUJUAN & PENGESAHAN OLEH RW
    // ==========================================

    public function approveRecipient(int $recipientId): void
    {
        if (in_array($this->program->status_program, ['penyaluran', 'selesai'])) {
            session()->flash('error', 'Usulan tidak dapat diubah karena program telah memasuki tahap penyaluran atau telah selesai.');

            return;
        }

        $user = auth()->user();
        if (! $user->isRwLevel()) {
            abort(403, 'Hanya Pengurus RW yang berwenang mengesahkan usulan penerima.');
        }

        $recipient = SocialAidRecipient::with(['resident', 'rt'])->findOrFail($recipientId);
        $recipient->update([
            'status_usulan' => 'disetujui_rw',
        ]);

        $this->program->load('recipients');
        session()->flash('success', "Usulan untuk {$recipient->resident?->nama_lengkap} (RT {$recipient->rt?->nomor_rt}) berhasil disetujui & disahkan oleh RW.");

        $this->syncLifecycleStatus();
    }

    public function rejectRecipient(int $recipientId): void
    {
        if (in_array($this->program->status_program, ['penyaluran', 'selesai'])) {
            session()->flash('error', 'Usulan tidak dapat diubah karena program telah memasuki tahap penyaluran atau telah selesai.');

            return;
        }

        $user = auth()->user();
        if (! $user->isRwLevel()) {
            abort(403, 'Hanya Pengurus RW yang berwenang menolak usulan.');
        }

        $recipient = SocialAidRecipient::with(['resident', 'rt'])->findOrFail($recipientId);
        $recipient->update([
            'status_usulan' => 'ditolak_rw',
        ]);

        $this->program->load('recipients');
        session()->flash('warning', "Usulan untuk {$recipient->resident?->nama_lengkap} ditolak / diminta perbaikan kepada RT.");

        $this->syncLifecycleStatus();
    }

    public function unapproveRecipient(int $recipientId): void
    {
        if (in_array($this->program->status_program, ['penyaluran', 'selesai'])) {
            session()->flash('error', 'Status usulan tidak dapat dibatalkan karena program telah memasuki tahap penyaluran atau telah selesai.');

            return;
        }

        $user = auth()->user();
        if (! $user->isRwLevel()) {
            abort(403, 'Hanya Pengurus RW yang berwenang mengubah status usulan.');
        }

        $recipient = SocialAidRecipient::with(['resident', 'rt'])->findOrFail($recipientId);
        $oldStatus = $recipient->status_usulan;
        $recipient->update([
            'status_usulan' => 'diusulkan_rt',
        ]);

        $this->program->load('recipients');
        $label = $oldStatus === 'ditolak_rw' ? 'Penolakan usulan' : 'Pengesahan usulan';
        session()->flash('info', "{$label} {$recipient->resident?->nama_lengkap} dibatalkan (dikembalikan ke status usulan RT).");

        $this->syncLifecycleStatus();
    }

    public function approveAllRtRecipients(?int $rtId = null): void
    {
        if (in_array($this->program->status_program, ['penyaluran', 'selesai'])) {
            session()->flash('error', 'Usulan tidak dapat disahkan karena program telah memasuki tahap penyaluran atau telah selesai.');

            return;
        }

        $user = auth()->user();
        if (! $user->isRwLevel()) {
            abort(403, 'Hanya Pengurus RW yang berwenang mengesahkan usulan.');
        }

        $targetRt = $rtId ?: $this->selectedRtId;
        $query = $this->program->recipients()->where('status_usulan', 'diusulkan_rt');
        if ($targetRt) {
            $query->where('rt_id', $targetRt);
        }

        $count = $query->count();
        if ($count === 0) {
            session()->flash('info', 'Tidak ada usulan penerima yang berstatus menunggu verifikasi.');

            return;
        }

        $query->update(['status_usulan' => 'disetujui_rw']);
        $this->program->load('recipients');

        session()->flash('success', "Berhasil mengesahkan {$count} usulan calon penerima bantuan.");

        $this->syncLifecycleStatus();
    }

    public function approveAllAndAdvanceToPenyaluran(): void
    {
        $user = auth()->user();
        if (! $user->isRwLevel()) {
            abort(403, 'Hanya Pengurus RW yang berwenang mengesahkan dan memajukan tahapan program.');
        }

        // Sahkan seluruh usulan yang masih diusulkan_rt
        $updated = $this->program->recipients()
            ->where('status_usulan', 'diusulkan_rt')
            ->update(['status_usulan' => 'disetujui_rw']);

        $this->program->load('recipients');

        // Sinkronisasi status siklus hidup program secara otomatis
        $this->syncLifecycleStatus();

        if ($this->program->status_program === 'penyaluran') {
            $msg = 'Seluruh usulan penerima bantuan berhasil disahkan oleh RW! Status program resmi beralih ke Tahap Penyaluran (Hari-H). Dokumen daftar hadir & serah terima siap digunakan.';
            $this->activeTab = 'penyaluran';
        } else {
            $msg = "Sebanyak {$updated} usulan calon penerima berhasil disahkan oleh RW.";
            if (! $this->program->areAllRtQuotasFilled()) {
                $currentCount = $this->program->recipients()->where('status_usulan', '!=', 'diganti')->count();
                $msg .= " Program tetap berada pada Tahap 1: Pendataan RT karena alokasi kuota RT belum terisi penuh ({$currentCount}/{$this->program->total_kuota} KK).";
            } elseif ($this->program->areAllProposalsApproved() && ! $this->program->isDistributionDue()) {
                $msg .= " Status: Siap Disalurkan (Menunggu Jadwal). Tahap Penyaluran Hari-H otomatis aktif pada tanggal {$this->program->tanggal_penyaluran->locale('id')->translatedFormat('d F Y')}.";
            }
        }

        session()->flash('success', $msg);
    }

    public function confirmApproveAll(): void
    {
        $this->showApproveAllModal = true;
    }

    public function cancelApproveAll(): void
    {
        $this->showApproveAllModal = false;
    }

    public function executeApproveAll(): void
    {
        $this->showApproveAllModal = false;
        $this->approveAllAndAdvanceToPenyaluran();
    }

    public function advanceToPenyaluran(): void
    {
        $user = auth()->user();
        if (! $user->isRwLevel()) {
            abort(403);
        }

        if (! $this->program->areAllRtQuotasFilled() || ! $this->program->areAllProposalsApproved()) {
            session()->flash('error', 'Program belum dapat dialihkan ke Tahap Penyaluran karena kuota belum lengkap atau masih ada usulan yang belum disetujui RW.');

            return;
        }

        $this->program->update(['status_program' => 'penyaluran']);
        $this->program->load('recipients');
        $this->currentProgramStatus = 'penyaluran';
        session()->flash('success', "Program resmi dialihkan ke 'Tahap Penyaluran (Hari-H)'. Dokumen daftar hadir dan serah terima siap digunakan.");
        $this->activeTab = 'penyaluran';
    }

    public function advanceToSelesai(): void
    {
        $user = auth()->user();
        if (! $user->isRwLevel()) {
            abort(403);
        }

        $this->program->update(['status_program' => 'selesai']);
        $this->program->load('recipients');
        $this->currentProgramStatus = 'selesai';
        session()->flash('success', 'Program bantuan sosial telah diselesaikan dan seluruh data telah diarsipkan.');
    }

    // ==========================================
    // TAB B: PENGGANTIAN / SANGGAHAN PENERIMA
    // ==========================================

    public function openReplaceModal(int $recipientId): void
    {
        $recipient = SocialAidRecipient::with('resident', 'house')->findOrFail($recipientId);
        $user = auth()->user();

        if (! $user->isRwLevel() && $recipient->rt_id !== $user->rt_id) {
            abort(403);
        }

        if ($this->program->status_program === 'selesai') {
            session()->flash('error', 'Program sudah selesai dan diarsipkan. Daftar penerima tidak dapat diganti.');

            return;
        }

        if ($recipient->status_penyerahan !== 'belum_diambil') {
            session()->flash('error', 'Warga yang sudah mengambil/menerima bantuan tidak dapat diganti.');

            return;
        }

        if ($recipient->status_usulan === 'disetujui_rw') {
            session()->flash('error', 'Usulan warga ini telah disetujui & disahkan oleh RW. Batalkan persetujuan (↺) di Tab Usulan terlebih dahulu jika ingin mengajukan penggantian.');

            return;
        }

        $this->resetValidation();
        $this->targetRecipientId = $recipient->id;
        $this->targetRecipientName = $recipient->resident?->nama_lengkap ?? 'Penerima';
        $this->alasan_penggantian = 'meninggal';
        $this->digantikan_oleh_resident_id = null;
        $this->catatan_penggantian = '';

        $this->showReplaceModal = true;
    }

    public function confirmReplacement(): void
    {
        $this->validate([
            'alasan_penggantian' => 'required|in:meninggal,pindah_domisili,sudah_mampu,data_ganda',
            'digantikan_oleh_resident_id' => 'required|exists:residents,id',
            'catatan_penggantian' => 'nullable|string|max:500',
        ]);

        $recipient = SocialAidRecipient::findOrFail($this->targetRecipientId);
        $user = auth()->user();

        if (! $user->isRwLevel() && $recipient->rt_id !== $user->rt_id) {
            abort(403);
        }

        if ($this->program->status_program === 'selesai') {
            session()->flash('error', 'Program sudah selesai dan diarsipkan. Daftar penerima tidak dapat diganti.');

            return;
        }

        if ($recipient->status_penyerahan !== 'belum_diambil') {
            session()->flash('error', 'Warga yang sudah mengambil/menerima bantuan tidak dapat diganti.');

            return;
        }

        if ($recipient->status_usulan === 'disetujui_rw') {
            session()->flash('error', 'Usulan warga ini telah disetujui & disahkan oleh RW. Batalkan persetujuan (↺) di Tab Usulan terlebih dahulu jika ingin mengajukan penggantian.');

            return;
        }

        $replacementResident = Resident::with('house')->findOrFail($this->digantikan_oleh_resident_id);

        // Update record lama sebagai diganti
        $recipient->update([
            'status_usulan' => 'diganti',
            'alasan_penggantian' => $this->alasan_penggantian,
            'digantikan_oleh_resident_id' => $replacementResident->id,
            'catatan_penggantian' => $this->catatan_penggantian,
        ]);

        // Buat record penerima baru yang menggantikan
        SocialAidRecipient::create([
            'program_id' => $this->program->id,
            'rt_id' => $recipient->rt_id,
            'house_id' => $replacementResident->house_id,
            'resident_id' => $replacementResident->id,
            'nomor_urut_kupon' => $recipient->nomor_urut_kupon.'-G',
            'status_usulan' => 'diusulkan_rt',
            'status_penyerahan' => 'belum_diambil',
        ]);

        $this->showReplaceModal = false;
        $this->program->load('recipients.resident', 'recipients.replacementResident');
        session()->flash('success', "Penggantian penerima atas nama {$this->targetRecipientName} berhasil diproses. Calon penerima pengganti masuk sebagai usulan RT.");

        $this->syncLifecycleStatus();
    }

    // ==========================================
    // TAB C: HARI-H PENYALURAN & BUKTI SERAH TERIMA
    // ==========================================

    public function openDeliveryModal(int $recipientId): void
    {
        $recipient = SocialAidRecipient::with('resident')->findOrFail($recipientId);
        $user = auth()->user();

        if (! $user->isRwLevel() && $recipient->rt_id !== $user->rt_id) {
            abort(403);
        }

        $this->resetValidation();
        $this->deliveringRecipientId = $recipient->id;
        $this->deliveringRecipientName = $recipient->resident?->nama_lengkap ?? 'Penerima';
        $this->penerima_aktual = $recipient->resident?->nama_lengkap ?? '';
        $this->status_penyerahan = $recipient->status_penyerahan === 'belum_diambil' ? 'sudah_diterima' : $recipient->status_penyerahan;
        $this->bukti_foto = null;

        $this->showDeliveryModal = true;
    }

    public function saveDelivery(): void
    {
        $this->validate([
            'status_penyerahan' => 'required|in:sudah_diterima,diwakilkan',
            'penerima_aktual' => 'required|string|max:100',
            'bukti_foto' => 'nullable|image|max:3072', // Max 3MB
        ]);

        $recipient = SocialAidRecipient::findOrFail($this->deliveringRecipientId);
        $user = auth()->user();

        if (! $user->isRwLevel() && $recipient->rt_id !== $user->rt_id) {
            abort(403);
        }

        $photoPath = $recipient->bukti_foto;
        if ($this->bukti_foto) {
            $photoPath = $this->bukti_foto->store('bansos-bukti', 'public');
        }

        $recipient->update([
            'status_penyerahan' => $this->status_penyerahan,
            'penerima_aktual' => $this->penerima_aktual,
            'waktu_diterima' => now(),
            'bukti_foto' => $photoPath,
            'diserahkan_oleh' => $user->id,
        ]);

        $this->showDeliveryModal = false;
        $this->program->load('recipients');
        session()->flash('success', "Penyerahan bantuan untuk {$this->deliveringRecipientName} berhasil dicatat.");

        $this->syncLifecycleStatus();
    }

    public function resetDelivery(int $recipientId): void
    {
        $recipient = SocialAidRecipient::with('resident')->findOrFail($recipientId);
        $user = auth()->user();

        if (! $user->isRwLevel() && $recipient->rt_id !== $user->rt_id) {
            abort(403);
        }

        $recipient->update([
            'status_penyerahan' => 'belum_diambil',
            'penerima_aktual' => null,
            'waktu_diterima' => null,
            'bukti_foto' => null,
            'diserahkan_oleh' => null,
        ]);

        $this->program->load('recipients');
        session()->flash('info', "Tanda terima bantuan untuk {$recipient->resident?->nama_lengkap} dibatalkan (status kembali ke Belum Diambil).");

        $this->syncLifecycleStatus();
    }

    public function render(): View
    {
        $user = auth()->user();
        $targetRtId = $user->isRwLevel() ? $this->selectedRtId : $user->rt_id;

        // Query penerima bantuan aktif untuk tab yang dipilih
        $recipientsQuery = $this->program->recipients()
            ->with(['resident', 'house.socialProfile', 'rt', 'replacementResident', 'diserahkanOlehUser']);

        if ($targetRtId) {
            $recipientsQuery->where('rt_id', $targetRtId);
        }

        $allRecipients = $recipientsQuery->get();

        // Penerima aktif (bukan yang diganti)
        $activeRecipients = $allRecipients->where('status_usulan', '!=', 'diganti');

        // Penerima yang mengalami sanggahan / penggantian
        $replacedRecipients = $allRecipients->where('status_usulan', '===', 'diganti');

        // Statistik kuota RT aktif
        $currentRtQuota = $targetRtId ? $this->program->getQuotaForRt($targetRtId) : $this->program->total_kuota;
        $currentRtFilled = $activeRecipients->count();
        $currentRtDelivered = $activeRecipients->whereIn('status_penyerahan', ['sudah_diterima', 'diwakilkan'])->count();
        $currentRtApproved = $activeRecipients->where('status_usulan', 'disetujui_rw')->count();

        // Data Kandidat Rekomendasi untuk Modal Usulan Tab A
        $candidateHouses = collect();
        $rotationCounts = ['all' => 0, 'never' => 0, 'ready' => 0, 'cooldown' => 0];

        if ($this->showProposeModal && $targetRtId) {
            $rawCandidates = House::where('rt_id', $targetRtId)
                ->whereHas('residents', fn ($q) => $q->where('status_warga', 'aktif'))
                ->with(['socialProfile', 'headOfFamily', 'residents', 'socialAidRecipients.program'])
                ->get()
                ->map(function (House $h) {
                    $h->rotation_metrics = $h->getAidRotationMetrics($this->program->id);
                    $h->fairness_score = $h->rotation_metrics['fairness_score'];

                    return $h;
                });

            $rotationCounts['all'] = $rawCandidates->count();
            $rotationCounts['never'] = $rawCandidates->where('rotation_metrics.status', 'never')->count();
            $rotationCounts['ready'] = $rawCandidates->filter(fn ($h) => in_array($h->rotation_metrics['status'], ['never', 'ready'], true))->count();
            $rotationCounts['cooldown'] = $rawCandidates->filter(fn ($h) => in_array($h->rotation_metrics['status'], ['cooldown', 'recent'], true))->count();

            $filtered = match ($this->candidateFilter) {
                'never' => $rawCandidates->filter(fn ($h) => $h->rotation_metrics['status'] === 'never'),
                'ready' => $rawCandidates->filter(fn ($h) => in_array($h->rotation_metrics['status'], ['never', 'ready'], true)),
                'cooldown' => $rawCandidates->filter(fn ($h) => in_array($h->rotation_metrics['status'], ['cooldown', 'recent'], true)),
                default => $rawCandidates,
            };

            $candidateHouses = $filtered->sortByDesc(fn (House $h) => $h->fairness_score)->values();
        }

        // Data Calon Pengganti untuk Modal Sanggahan Tab B
        $availableReplacements = collect();
        if ($this->showReplaceModal) {
            $modalRtId = $targetRtId ?: SocialAidRecipient::find($this->targetRecipientId)?->rt_id;
            if ($modalRtId) {
                $existingResidentIds = $this->program->recipients()->where('status_usulan', '!=', 'diganti')->pluck('resident_id')->toArray();
                $availableReplacements = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $modalRtId))
                    ->where('status_warga', 'aktif')
                    ->whereNotIn('id', $existingResidentIds)
                    ->with(['house.socialProfile', 'house.socialAidRecipients.program'])
                    ->get()
                    ->map(function (Resident $r) {
                        if ($r->house) {
                            $r->house->rotation_metrics = $r->house->getAidRotationMetrics($this->program->id);
                            $r->fairness_score = $r->house->rotation_metrics['fairness_score'];
                        } else {
                            $r->fairness_score = 0;
                        }

                        return $r;
                    })
                    ->sortByDesc(fn ($r) => $r->fairness_score)->values();
            }
        }

        $rts = $user->isRwLevel() ? Rt::orderBy('nomor_rt')->get() : collect();

        // Penghitungan usulan yang menunggu approval RW
        $totalPendingApproval = $this->program->recipients()->where('status_usulan', 'diusulkan_rt')->count();
        $rtPendingApproval = $targetRtId
            ? $this->program->recipients()->where('rt_id', $targetRtId)->where('status_usulan', 'diusulkan_rt')->count()
            : $totalPendingApproval;
        $totalApproved = $this->program->recipients()->where('status_usulan', 'disetujui_rw')->count();

        return view('livewire.social-aid.program-detail', [
            'user' => $user,
            'rts' => $rts,
            'targetRtId' => $targetRtId,
            'activeRecipients' => $activeRecipients,
            'replacedRecipients' => $replacedRecipients,
            'currentRtQuota' => $currentRtQuota,
            'currentRtFilled' => $currentRtFilled,
            'currentRtDelivered' => $currentRtDelivered,
            'currentRtApproved' => $currentRtApproved,
            'candidateHouses' => $candidateHouses,
            'rotationCounts' => $rotationCounts,
            'availableReplacements' => $availableReplacements,
            'totalPendingApproval' => $totalPendingApproval,
            'rtPendingApproval' => $rtPendingApproval,
            'totalApproved' => $totalApproved,
        ]);
    }
}
