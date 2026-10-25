<?php

namespace App\Livewire\SocialAid;

use App\Models\House;
use App\Models\Rt;
use App\Models\SocialAidProgram;
use App\Models\SocialAidQuota;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Program Bantuan Sosial')]
class ProgramIndex extends Component
{
    use WithPagination;

    #[Url(as: 'cari')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    public int $perPage = 10;

    // State Modal Tambah Program Baru (Khusus RW)
    public bool $showCreateModal = false;

    // State Modal Konfirmasi Hapus Program (Khusus RW)
    public bool $showDeleteModal = false;

    public ?int $deletingProgramId = null;

    public ?string $deletingProgramName = null;

    public string $nama_program = '';

    public string $sumber_dana = 'pemerintah_pusat';

    public string $jenis_bantuan = 'sembako_barang';

    public string $detail_bantuan = '';

    public string $tanggal_penyaluran = '';

    public string $lokasi_pengambilan = 'Balai Pertemuan RW 01 / Pos RT Setempat';

    /** @var array<int, int> [rt_id => jumlah_kuota] */
    public array $rtQuotas = [];

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->canManageSocialAid()) {
            abort(403, 'Akses terbatas untuk Pengurus Lingkungan RT/RW.');
        }

        $this->tanggal_penyaluran = now()->addDays(3)->toDateString();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $user = auth()->user();
        if (! $user->canCreateSocialAidProgram()) {
            abort(403, 'Hanya Pengurus RW yang berwenang membuat program bantuan baru.');
        }

        $this->resetValidation();
        $this->nama_program = '';
        $this->sumber_dana = 'pemerintah_pusat';
        $this->jenis_bantuan = 'sembako_barang';
        $this->detail_bantuan = '';
        $this->tanggal_penyaluran = now()->addDays(3)->toDateString();
        $this->lokasi_pengambilan = 'Balai Pertemuan RW 01 / Pos RT Setempat';

        // Inisialisasi kuota RT default (0)
        $this->rtQuotas = [];
        $rts = Rt::orderBy('nomor_rt')->get();
        foreach ($rts as $rt) {
            $this->rtQuotas[$rt->id] = 0;
        }

        $this->showCreateModal = true;
    }

    public function getTotalAllocatedQuotaProperty(): int
    {
        $sum = 0;
        foreach ($this->rtQuotas as $val) {
            $sum += (int) $val;
        }

        return $sum;
    }

    public function saveProgram(): void
    {
        $user = auth()->user();
        if (! $user->canCreateSocialAidProgram()) {
            abort(403);
        }

        $this->validate([
            'nama_program' => 'required|string|max:150',
            'sumber_dana' => 'required|in:pemerintah_pusat,pemda_kelurahan,kas_rw,csr_donatur',
            'jenis_bantuan' => 'required|in:sembako_barang,uang_tunai',
            'detail_bantuan' => 'required|string|max:150',
            'tanggal_penyaluran' => 'required|date',
            'lokasi_pengambilan' => 'required|string|max:150',
            'rtQuotas.*' => 'nullable|integer|min:0',
        ]);

        $totalQuota = $this->totalAllocatedQuota;
        if ($totalQuota <= 0) {
            $this->addError('rtQuotas', 'Alokasi total kuota harus lebih dari 0.');

            return;
        }

        $program = SocialAidProgram::create([
            'nama_program' => $this->nama_program,
            'sumber_dana' => $this->sumber_dana,
            'jenis_bantuan' => $this->jenis_bantuan,
            'detail_bantuan' => $this->detail_bantuan,
            'total_kuota' => $totalQuota,
            'tanggal_penyaluran' => $this->tanggal_penyaluran,
            'lokasi_pengambilan' => $this->lokasi_pengambilan,
            'status_program' => 'pendataan_rt',
            'created_by' => $user->id,
        ]);

        foreach ($this->rtQuotas as $rtId => $kuota) {
            $intQuota = (int) $kuota;
            if ($intQuota > 0) {
                SocialAidQuota::create([
                    'program_id' => $program->id,
                    'rt_id' => $rtId,
                    'jumlah_kuota' => $intQuota,
                ]);
            }
        }

        $this->showCreateModal = false;
        session()->flash('success', "Program '{$program->nama_program}' berhasil dibuat dengan total kuota {$totalQuota} penerima.");
    }

    public function confirmDeleteProgram(int $programId): void
    {
        $user = auth()->user();
        if (! $user->isRwLevel()) {
            abort(403, 'Hanya Pengurus RW yang berwenang menghapus program bantuan.');
        }

        $program = SocialAidProgram::findOrFail($programId);

        if (in_array($program->status_program, ['penyaluran', 'selesai'], true)) {
            session()->flash('error', 'Program yang sudah masuk tahap Penyaluran atau Selesai tidak dapat dihapus demi keamanan arsip dan bukti serah terima.');

            return;
        }

        $this->deletingProgramId = $program->id;
        $this->deletingProgramName = $program->nama_program;
        $this->showDeleteModal = true;
    }

    public function cancelDeleteProgram(): void
    {
        $this->showDeleteModal = false;
        $this->deletingProgramId = null;
        $this->deletingProgramName = null;
    }

    public function executeDeleteProgram(): void
    {
        $user = auth()->user();
        if (! $user->isRwLevel()) {
            abort(403);
        }

        if ($this->deletingProgramId) {
            $program = SocialAidProgram::findOrFail($this->deletingProgramId);

            if (in_array($program->status_program, ['penyaluran', 'selesai'], true)) {
                $this->showDeleteModal = false;
                $this->deletingProgramId = null;
                $this->deletingProgramName = null;
                session()->flash('error', 'Program tidak dapat dihapus karena sudah masuk tahap Penyaluran atau Selesai.');

                return;
            }

            $name = $program->nama_program;
            $program->delete();

            $this->showDeleteModal = false;
            $this->deletingProgramId = null;
            $this->deletingProgramName = null;

            session()->flash('success', "Program '{$name}' berhasil dihapus.");
        }
    }

    public function render(): View
    {
        $user = auth()->user();

        $query = SocialAidProgram::with(['creator', 'quotas.rt', 'recipients'])
            ->latest('id');

        if (trim($this->search) !== '') {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term): void {
                $q->where('nama_program', 'like', $term)
                    ->orWhere('detail_bantuan', 'like', $term);
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('status_program', $this->statusFilter);
        }

        $programs = $query->paginate($this->perPage);

        // Data RT & Statistik Keluarga Rentan untuk Modal
        $rtsData = [];
        if ($user->canCreateSocialAidProgram()) {
            $allRts = Rt::orderBy('nomor_rt')->get();
            foreach ($allRts as $rt) {
                $poorCount = House::where('rt_id', $rt->id)
                    ->whereHas('socialProfile', fn ($q) => $q->whereIn('kategori_ekonomi', ['miskin_ekstrem', 'rentan_miskin']))
                    ->count();

                $rtsData[] = [
                    'rt' => $rt,
                    'poor_count' => $poorCount,
                ];
            }
        }

        return view('livewire.social-aid.program-index', [
            'user' => $user,
            'programs' => $programs,
            'rtsData' => $rtsData,
        ]);
    }
}
