<?php

namespace App\Livewire\Houses;

use App\Models\House;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\SensusSubmission;
use App\Services\ResidentImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Title('Daftar Unit Rumah & Onboarding Warga')]
class HouseIndex extends Component
{
    use WithFileUploads, WithPagination;

    // Tab Navigasi Utama: 'houses' (Data Rumah) | 'sensus' (Antrean Sensus Warga Mandiri)
    #[Url(as: 'tab')]
    public string $activeTab = 'houses';

    #[Url(as: 'cari')]
    public string $search = '';

    #[Url(as: 'rt')]
    public ?int $selectedRtId = null;

    #[Url(as: 'status')]
    public string $statusHuni = '';

    #[Url(as: 'urutkan')]
    public string $sortBy = 'terbaru';

    // Properti Modal Form Tambah/Edit Rumah Manual
    public bool $showModal = false;

    public ?int $editingHouseId = null;

    public ?int $form_rt_id = null;

    public string $form_nomor_rumah = '';

    public string $form_status_huni = 'dihuni_pemilik';

    public string $form_kategori_tarif = 'standar';

    public ?string $form_catatan = '';

    // Properti Konfirmasi Hapus Rumah
    public bool $showDeleteModal = false;

    public ?int $deletingHouseId = null;

    public string $deletingHouseNumber = '';

    // --- PROPERTI BATCH IMPORT EXCEL ---
    public bool $showImportModal = false;

    public $importFile = null;

    public ?int $importRtId = null;

    public string $importStep = 'upload'; // 'upload' | 'preview'

    /** @var array<string, mixed> */
    public array $importPreviewData = [];

    /** @var list<string> */
    public array $importErrors = [];

    public int $importNewHousesCount = 0;

    public int $importNewResidentsCount = 0;

    /** @var list<array<string, mixed>> */
    public array $importValidRows = [];

    // --- PROPERTI VERIFIKASI SENSUS MANDIRI ---
    public bool $showSensusReviewModal = false;

    public ?int $reviewingSensusId = null;

    public ?SensusSubmission $reviewingSensus = null;

    public string $rejectReasonInput = '';

    public bool $isRejecting = false;

    public function mount(): void
    {
        $user = auth()->user();
        if ($user && ! $user->isAdminRw() && $user->rt_id) {
            $this->selectedRtId = $user->rt_id;
            $this->form_rt_id = $user->rt_id;
            $this->importRtId = $user->rt_id;
        } else {
            $this->importRtId = Rt::first()?->id;
        }
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedRtId(): void
    {
        $this->resetPage();
    }

    public function updatingStatusHuni(): void
    {
        $this->resetPage();
    }

    public function updatingSortBy(): void
    {
        $this->resetPage();
    }

    // ==========================================
    // METODE KELOLA UNIT RUMAH MANUAL
    // ==========================================
    public function openCreateModal(): void
    {
        $user = auth()->user();
        abort_if(! $user || ! $user->canManageHouse(), 403, 'Anda tidak memiliki hak akses menambah unit rumah.');

        $this->resetValidation();
        $this->editingHouseId = null;
        $this->form_nomor_rumah = '';
        $this->form_status_huni = 'dihuni_pemilik';
        $this->form_kategori_tarif = 'standar';
        $this->form_catatan = '';

        if (! $user->isAdminRw()) {
            $this->form_rt_id = $user->rt_id;
        } else {
            $this->form_rt_id = $this->selectedRtId ?: (Rt::first()?->id);
        }

        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $user = auth()->user();
        abort_if(! $user || ! $user->canManageHouse(), 403, 'Anda tidak memiliki hak akses mengubah unit rumah.');

        $this->resetValidation();
        $house = House::accessibleBy($user, $this->selectedRtId)->findOrFail($id);

        $this->editingHouseId = $house->id;
        $this->form_rt_id = $house->rt_id;
        $this->form_nomor_rumah = $house->nomor_rumah;
        $this->form_status_huni = $house->status_huni;
        $this->form_kategori_tarif = $house->kategori_tarif;
        $this->form_catatan = $house->catatan;

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingHouseId = null;
    }

    public function saveHouse(): void
    {
        $user = auth()->user();
        abort_if(! $user || ! $user->canManageHouse(), 403, 'Anda tidak memiliki hak akses menyimpan unit rumah.');

        if (! $user->isAdminRw()) {
            $this->form_rt_id = $user->rt_id;
        }

        $this->validate([
            'form_rt_id' => 'required|exists:rts,id',
            'form_nomor_rumah' => 'required|string|max:50',
            'form_status_huni' => 'required|in:dihuni_pemilik,kontrak,kosong',
            'form_kategori_tarif' => 'required|in:standar,usaha,kosong',
            'form_catatan' => 'nullable|string|max:500',
        ], [
            'form_rt_id.required' => 'Wilayah RT wajib dipilih.',
            'form_nomor_rumah.required' => 'Nomor atau Blok Rumah wajib diisi.',
            'form_status_huni.required' => 'Status hunian wajib dipilih.',
            'form_kategori_tarif.required' => 'Kategori tarif wajib dipilih.',
        ]);

        if ($this->editingHouseId) {
            $house = House::accessibleBy($user, $this->selectedRtId)->findOrFail($this->editingHouseId);
            $house->update([
                'rt_id' => $this->form_rt_id,
                'nomor_rumah' => $this->form_nomor_rumah,
                'status_huni' => $this->form_status_huni,
                'kategori_tarif' => $this->form_kategori_tarif,
                'catatan' => $this->form_catatan,
            ]);
            session()->flash('success', "Data rumah {$this->form_nomor_rumah} berhasil diperbarui.");
        } else {
            House::create([
                'rt_id' => $this->form_rt_id,
                'nomor_rumah' => $this->form_nomor_rumah,
                'status_huni' => $this->form_status_huni,
                'kategori_tarif' => $this->form_kategori_tarif,
                'catatan' => $this->form_catatan,
            ]);
            session()->flash('success', "Data rumah {$this->form_nomor_rumah} berhasil ditambahkan ke sistem.");
        }

        $this->closeModal();
    }

    public function confirmDelete(int $id): void
    {
        $user = auth()->user();
        abort_if(! $user || ! $user->canDeleteHouse(), 403, 'Hanya Ketua RT atau Admin RW yang dapat menghapus unit rumah.');

        $house = House::accessibleBy($user, $this->selectedRtId)->findOrFail($id);
        $this->deletingHouseId = $house->id;
        $this->deletingHouseNumber = $house->nomor_rumah;
        $this->showDeleteModal = true;
    }

    public function deleteHouse(): void
    {
        $user = auth()->user();
        abort_if(! $user || ! $user->canDeleteHouse(), 403, 'Hanya Ketua RT atau Admin RW yang dapat menghapus unit rumah.');

        if ($this->deletingHouseId) {
            $house = House::accessibleBy($user, $this->selectedRtId)->findOrFail($this->deletingHouseId);
            $num = $house->nomor_rumah;
            $house->delete();
            session()->flash('success', "Unit rumah {$num} beserta seluruh data warga di dalamnya berhasil dihapus.");
        }

        $this->showDeleteModal = false;
        $this->deletingHouseId = null;
    }

    // ==========================================
    // METODE BATCH IMPORT EXCEL
    // ==========================================
    public function openImportModal(): void
    {
        $user = auth()->user();
        abort_if(! $user || ! $user->canManageHouse(), 403, 'Akses ditolak.');

        $this->resetValidation();
        $this->importFile = null;
        $this->importStep = 'upload';
        $this->importPreviewData = [];
        $this->importErrors = [];
        $this->importValidRows = [];
        $this->importNewHousesCount = 0;
        $this->importNewResidentsCount = 0;

        if (! $user->isAdminRw()) {
            $this->importRtId = $user->rt_id;
        } else {
            $this->importRtId = $this->selectedRtId ?: (Rt::first()?->id);
        }

        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->importFile = null;
        $this->importStep = 'upload';
    }

    public function processImportPreview(ResidentImportService $service): void
    {
        $user = auth()->user();
        abort_if(! $user || ! $user->canManageHouse(), 403, 'Akses ditolak.');

        if (! $user->isAdminRw() || ! $this->importRtId) {
            $this->importRtId = $user->rt_id ?: (Rt::first()?->id);
        }

        $this->validate([
            'importFile' => ['required', 'file', 'max:5120', 'extensions:xlsx,xls'],
            'importRtId' => ['required', 'exists:rts,id'],
        ], [
            'importFile.required' => 'Pilih berkas Excel (.xlsx) terlebih dahulu.',
            'importFile.extensions' => 'Format berkas harus berekstensi .xlsx atau .xls',
            'importFile.max' => 'Ukuran berkas maksimal 5MB.',
            'importRtId.required' => 'Wilayah RT tujuan wajib dipilih.',
        ]);

        $filePath = $this->importFile->getRealPath() ?: $this->importFile->path();
        $rtId = (int) ($user->isAdminRw() ? $this->importRtId : $user->rt_id);

        $result = $service->parseAndValidate($filePath, $rtId);

        $this->importErrors = $result['errors'];
        $this->importPreviewData = $result['preview_data'];
        $this->importValidRows = $result['valid_rows'];
        $this->importNewHousesCount = $result['new_houses_count'];
        $this->importNewResidentsCount = $result['new_residents_count'];

        $this->importStep = 'preview';
    }

    public function executeImport(ResidentImportService $service): void
    {
        $user = auth()->user();
        abort_if(! $user || ! $user->canManageHouse(), 403, 'Akses ditolak.');

        if (empty($this->importValidRows)) {
            session()->flash('error', 'Tidak ada data valid yang dapat diimpor.');

            return;
        }

        $rtId = (int) ($user->isAdminRw() ? $this->importRtId : $user->rt_id);
        $result = $service->executeImport($this->importValidRows, $rtId);

        $rt = Rt::find($rtId);
        $rtLabel = $rt ? 'RT '.str_pad((string) ((int) $rt->nomor_rt), 2, '0', STR_PAD_LEFT) : '';

        session()->flash('success', "Berhasil mengimpor {$result['residents_count']} data warga ke dalam {$result['houses_count']} unit rumah di {$rtLabel}.");

        $this->closeImportModal();
        $this->resetPage();
    }

    // ==========================================
    // METODE VERIFIKASI SENSUS MANDIRI WARGA
    // ==========================================
    public function openSensusReview(int $id): void
    {
        $user = auth()->user();
        abort_if(! $user || ! $user->canManageHouse(), 403, 'Akses ditolak.');

        $query = SensusSubmission::with(['rt', 'verifiedBy']);
        if (! $user->isAdminRw()) {
            $query->where('rt_id', $user->rt_id);
        }

        $this->reviewingSensus = $query->findOrFail($id);
        $this->reviewingSensusId = $this->reviewingSensus->id;
        $this->rejectReasonInput = '';
        $this->isRejecting = false;
        $this->showSensusReviewModal = true;
    }

    public function closeSensusReview(): void
    {
        $this->showSensusReviewModal = false;
        $this->reviewingSensus = null;
        $this->reviewingSensusId = null;
        $this->isRejecting = false;
    }

    public function approveSensus(int $id): void
    {
        $user = auth()->user();
        abort_if(! $user || ! $user->canManageHouse(), 403, 'Akses ditolak.');

        $query = SensusSubmission::query();
        if (! $user->isAdminRw()) {
            $query->where('rt_id', $user->rt_id);
        }

        $sensus = $query->findOrFail($id);

        DB::transaction(function () use ($sensus, $user) {
            // 1. Buat atau temukan rumah
            $house = House::firstOrCreate(
                [
                    'rt_id' => $sensus->rt_id,
                    'nomor_rumah' => $sensus->nomor_rumah,
                ],
                [
                    'status_huni' => $sensus->status_huni,
                    'kategori_tarif' => $sensus->kategori_tarif,
                    'catatan' => $sensus->catatan_rumah ?: "Terdaftar via Sensus Mandiri Tiket {$sensus->kode_tiket}",
                ]
            );

            // 2. Insert atau update anggota keluarga
            $keluarga = is_array($sensus->data_keluarga) ? $sensus->data_keluarga : [];
            foreach ($keluarga as $member) {
                $statusKel = $member['status_keluarga'] ?? 'anak';
                if (! in_array($statusKel, ['kepala_keluarga', 'istri', 'anak', 'famili_lain'], true)) {
                    $statusKel = 'famili_lain';
                }

                $statusDom = $member['status_domisili'] ?? 'ktp_setempat';
                if (! in_array($statusDom, ['ktp_setempat', 'domisili_luar'], true)) {
                    $statusDom = 'ktp_setempat';
                }

                $agama = $member['agama'] ?? 'Islam';
                if (! in_array($agama, ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'], true)) {
                    $agama = 'Islam';
                }

                $noWa = ! empty($member['no_hp']) ? $member['no_hp'] : ($statusKel === 'kepala_keluarga' ? $sensus->no_wa_pengirim : null);

                Resident::updateOrCreate(
                    [
                        'nik' => $member['nik'],
                    ],
                    [
                        'house_id' => $house->id,
                        'no_kk' => $sensus->no_kk,
                        'nama_lengkap' => strtoupper(trim($member['nama_lengkap'])),
                        'jenis_kelamin' => $member['jenis_kelamin'] === 'P' ? 'P' : 'L',
                        'tempat_lahir' => strtoupper(trim($member['tempat_lahir'] ?: '-')),
                        'tanggal_lahir' => $member['tanggal_lahir'],
                        'agama' => $agama,
                        'pekerjaan' => strtoupper(trim($member['pekerjaan'] ?: 'Lainnya')),
                        'status_keluarga' => $statusKel,
                        'status_kependudukan' => $statusDom,
                        'no_wa' => $noWa,
                        'status_warga' => 'aktif',
                    ]
                );
            }

            // 3. Update status tiket sensus
            $sensus->update([
                'status' => 'disetujui',
                'catatan_verifikasi' => 'Telah disetujui & dimasukkan ke sistem oleh '.$user->nama,
                'verified_by_user_id' => $user->id,
                'verified_at' => now(),
            ]);
        });

        session()->flash('success', "Pengajuan sensus tiket {$sensus->kode_tiket} ({$sensus->nomor_rumah}) berhasil disetujui dan data keluarga telah resmi masuk ke sistem.");

        $this->closeSensusReview();
    }

    public function rejectSensus(int $id): void
    {
        $user = auth()->user();
        abort_if(! $user || ! $user->canManageHouse(), 403, 'Akses ditolak.');

        $query = SensusSubmission::query();
        if (! $user->isAdminRw()) {
            $query->where('rt_id', $user->rt_id);
        }

        $sensus = $query->findOrFail($id);

        $reason = trim($this->rejectReasonInput) ?: 'Data sensus belum lengkap atau NIK tidak sesuai.';

        $sensus->update([
            'status' => 'ditolak',
            'catatan_verifikasi' => $reason,
            'verified_by_user_id' => $user->id,
            'verified_at' => now(),
        ]);

        session()->flash('success', "Pengajuan tiket {$sensus->kode_tiket} ditolak dengan catatan: {$reason}");

        $this->closeSensusReview();
    }

    public function render(): View
    {
        $user = auth()->user();

        // Hitung antrean sensus pending untuk notifikasi badge tab
        $pendingSensusCount = SensusSubmission::where('status', 'menunggu_verifikasi')
            ->when(! $user->isAdminRw(), fn ($q) => $q->where('rt_id', $user->rt_id))
            ->count();

        // Query Data Rumah
        $housesQuery = House::with(['rt', 'headOfFamily'])
            ->accessibleBy($user, $this->selectedRtId)
            ->when($this->statusHuni, fn ($q) => $q->where('status_huni', $this->statusHuni))
            ->when($this->search, function ($query) {
                $query->where('nomor_rumah', 'like', '%'.$this->search.'%')
                    ->orWhereHas('residents', function ($q) {
                        $q->where('nama_lengkap', 'like', '%'.$this->search.'%');
                    });
            })
            ->withCount(['residents as total_jiwa' => fn ($q) => $q->where('status_warga', 'aktif')]);

        match ($this->sortBy) {
            'terlama' => $housesQuery->oldest('id'),
            'nomor_asc' => $housesQuery->orderBy('nomor_rumah', 'asc'),
            'nomor_desc' => $housesQuery->orderBy('nomor_rumah', 'desc'),
            default => $housesQuery->latest('id'),
        };

        $houses = $housesQuery->paginate(15);

        // Query Data Sensus (jika aktif di tab sensus)
        $sensusSubmissions = null;
        if ($this->activeTab === 'sensus') {
            $sensusSubmissions = SensusSubmission::with(['rt', 'verifiedBy'])
                ->when(! $user->isAdminRw(), fn ($q) => $q->where('rt_id', $user->rt_id))
                ->when($this->selectedRtId && $user->isAdminRw(), fn ($q) => $q->where('rt_id', $this->selectedRtId))
                ->when($this->search, function ($q) {
                    $q->where('kode_tiket', 'like', '%'.$this->search.'%')
                        ->orWhere('nomor_rumah', 'like', '%'.$this->search.'%')
                        ->orWhere('nama_pengirim', 'like', '%'.$this->search.'%')
                        ->orWhere('no_kk', 'like', '%'.$this->search.'%');
                })
                ->orderByRaw("CASE WHEN status = 'menunggu_verifikasi' THEN 0 ELSE 1 END")
                ->latest()
                ->paginate(15);
        }

        return view('livewire.houses.house-index', [
            'houses' => $houses,
            'sensusSubmissions' => $sensusSubmissions,
            'pendingSensusCount' => $pendingSensusCount,
            'rts' => $user->isAdminRw() ? Rt::orderBy('nomor_rt')->get() : collect(),
            'user' => $user,
        ]);
    }
}
