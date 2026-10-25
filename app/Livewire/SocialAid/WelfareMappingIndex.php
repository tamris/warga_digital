<?php

namespace App\Livewire\SocialAid;

use App\Models\House;
use App\Models\HouseSocialProfile;
use App\Models\Rt;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Pemetaan Kesejahteraan & DTKS Warga')]
class WelfareMappingIndex extends Component
{
    use WithPagination;

    #[Url(as: 'cari')]
    public string $search = '';

    #[Url(as: 'rt')]
    public ?int $selectedRtId = null;

    #[Url(as: 'filter')]
    public string $filterType = 'semua'; // semua, miskin_rentan, belum_dtks, terdaftar_dtks

    public int $perPage = 10;

    // Modal Edit Profil
    public bool $showEditModal = false;

    public ?int $editingHouseId = null;

    public ?string $editingHouseNumber = null;

    public ?string $editingHeadName = null;

    public string $form_kategori_ekonomi = 'menengah';

    public string $form_rentang_penghasilan = '1_5_sampai_3_jt';

    public string $form_status_dtks = 'belum_terdaftar';

    /** @var array<string> */
    public array $form_bantuan_rutin = [];

    public bool $form_is_janda_duda = false;

    public bool $form_ada_disabilitas_kronis = false;

    public bool $form_rumah_tidak_layak = false;

    public ?string $form_catatan_kondisi = '';

    public int $previewScore = 0;

    public bool $hasLansia = false;

    public int $residentChildCount = 0;

    public string $houseStatusHuni = 'dihuni_pemilik';

    public ?string $headGender = null;

    public bool $isAutoJandaDuda = false;

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->canManageSocialAid()) {
            abort(403, 'Akses terbatas untuk Pengurus Lingkungan RT/RW.');
        }

        if (! $user->isRwLevel() && $user->rt_id) {
            $this->selectedRtId = $user->rt_id;
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedRtId(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function openEditModal(int $houseId): void
    {
        $house = House::with(['socialProfile', 'headOfFamily', 'residents'])->findOrFail($houseId);
        $user = auth()->user();

        if (! $user->isRwLevel() && $house->rt_id !== $user->rt_id) {
            abort(403, 'Anda hanya dapat mengedit profil rumah di wilayah RT Anda.');
        }

        $head = $house->headOfFamily ?? $house->residents->where('status_warga', 'aktif')->firstWhere('status_keluarga', 'kepala_keluarga');
        $hasIstri = $house->residents->where('status_warga', 'aktif')->contains('status_keluarga', 'istri');

        $this->headGender = $head?->jenis_kelamin;
        // Otomatis terdeteksi janda / duda jika ada KK tetapi tidak ada istri, atau KK adalah perempuan
        $this->isAutoJandaDuda = $head ? (($head->jenis_kelamin === 'P') || ! $hasIstri) : false;

        $this->editingHouseId = $house->id;
        $this->editingHouseNumber = $house->nomor_rumah;
        $this->editingHeadName = $head?->nama_lengkap ?? $house->residents->first()?->nama_lengkap ?? 'Belum ada data KK';
        $this->houseStatusHuni = $house->status_huni;

        $this->hasLansia = $house->residents
            ->where('status_warga', 'aktif')
            ->contains(fn ($r) => $r->tanggal_lahir && $r->tanggal_lahir->age >= 60);

        $this->residentChildCount = $house->residents
            ->where('status_warga', 'aktif')
            ->where('status_keluarga', 'anak')
            ->count();

        $profile = $house->getOrCreateSocialProfile();

        $this->form_kategori_ekonomi = $profile->kategori_ekonomi;
        $this->form_rentang_penghasilan = $profile->rentang_penghasilan;
        $this->form_status_dtks = $profile->status_dtks;
        $this->form_bantuan_rutin = $profile->bantuan_rutin_pemerintah ?? [];
        $this->form_is_janda_duda = $this->isAutoJandaDuda || (bool) $profile->is_kepala_keluarga_janda_duda;
        $this->form_ada_disabilitas_kronis = (bool) $profile->ada_anggota_disabilitas_sakit_kronis;
        $this->form_rumah_tidak_layak = (bool) $profile->kondisi_rumah_tidak_layak;
        $this->form_catatan_kondisi = $profile->catatan_kondisi;

        $this->recalculatePreviewScore();
        $this->showEditModal = true;
    }

    public function recalculatePreviewScore(): void
    {
        $score = 0;

        // 1. Rentang Penghasilan Total Kepala Keluarga / Bulan (Bobot Max 40 Poin)
        $score += match ($this->form_rentang_penghasilan) {
            'di_bawah_1_5_jt' => 40,
            '1_5_sampai_3_jt' => 20,
            default => 0,
        };

        // 2. Kondisi Fisik Rumah & Tempat Tinggal (Bobot Max 30 Poin)
        // A. Fisik Rumah Tidak Layak Huni (+20 Poin)
        if ($this->form_rumah_tidak_layak) {
            $score += 20;
        }

        // B. Status Tempat Tinggal Kontrak / Sewa (+10 Poin)
        if ($this->houseStatusHuni === 'kontrak') {
            $score += 10;
        }

        // 3. Indikator Kerentanan Sosial & Beban Keluarga (Bobot Max 40 Poin)
        // A. Kepala Keluarga Tunggal (+10 Poin)
        if ($this->form_is_janda_duda) {
            $score += 10;
        }

        // B. Ada Anggota Disabilitas / Sakit Kronis (+10 Poin)
        if ($this->form_ada_disabilitas_kronis) {
            $score += 10;
        }

        // C. Lansia di dalam KK (+10 Poin)
        if ($this->hasLansia) {
            $score += 10;
        }

        // D. Tanggungan Anak >= 3 orang (+10 Poin)
        if ($this->residentChildCount >= 3) {
            $score += 10;
        }

        $this->previewScore = min(100, $score);

        // Kategori Ekonomi Otomatis Berdasarkan Skor
        $this->form_kategori_ekonomi = match (true) {
            $this->previewScore >= 80 => 'miskin_ekstrem',
            $this->previewScore >= 55 => 'rentan_miskin',
            $this->previewScore >= 30 => 'menengah',
            default => 'mampu',
        };
    }

    public function updatedFormKategoriEkonomi(): void
    {
        $this->recalculatePreviewScore();
    }

    public function updatedFormRentangPenghasilan(): void
    {
        $this->recalculatePreviewScore();
    }

    public function updatedFormIsJandaDuda(): void
    {
        $this->recalculatePreviewScore();
    }

    public function updatedFormAdaDisabilitasKronis(): void
    {
        $this->recalculatePreviewScore();
    }

    public function updatedFormRumahTidakLayak(): void
    {
        $this->recalculatePreviewScore();
    }

    public function saveSocialProfile(): void
    {
        $this->recalculatePreviewScore();

        $this->validate([
            'form_rentang_penghasilan' => 'required|in:di_bawah_1_5_jt,1_5_sampai_3_jt,di_atas_3_jt',
            'form_status_dtks' => 'required|in:terdaftar,belum_terdaftar,proses_usulan',
            'form_catatan_kondisi' => 'nullable|string|max:500',
        ]);

        $house = House::findOrFail($this->editingHouseId);
        $user = auth()->user();

        if (! $user->isRwLevel() && $house->rt_id !== $user->rt_id) {
            abort(403);
        }

        $profile = $house->getOrCreateSocialProfile();
        $profile->fill([
            'kategori_ekonomi' => $this->form_kategori_ekonomi,
            'rentang_penghasilan' => $this->form_rentang_penghasilan,
            'status_dtks' => $this->form_status_dtks,
            'bantuan_rutin_pemerintah' => array_values(array_filter($this->form_bantuan_rutin)),
            'is_kepala_keluarga_janda_duda' => $this->form_is_janda_duda,
            'ada_anggota_disabilitas_sakit_kronis' => $this->form_ada_disabilitas_kronis,
            'kondisi_rumah_tidak_layak' => $this->form_rumah_tidak_layak,
            'catatan_kondisi' => $this->form_catatan_kondisi,
            'updated_by' => $user->id,
        ]);
        $profile->skor_prioritas = $this->previewScore;
        $profile->save();

        $this->showEditModal = false;
        session()->flash('success', "Profil sosial ekonomi {$house->nomor_rumah} berhasil diperbarui (Skor: {$profile->skor_prioritas} Poin • Status: {$profile->kategori_ekonomi_label}).");
    }

    public function render(): View
    {
        $user = auth()->user();

        // Query Dasar Rumah dengan relasi
        $query = House::with(['rt', 'headOfFamily', 'residents', 'socialProfile'])
            ->accessibleBy($user, $this->selectedRtId);

        // Search
        if (trim($this->search) !== '') {
            $term = '%'.trim($this->search).'%';
            $query->where(function (Builder $q) use ($term): void {
                $q->where('nomor_rumah', 'like', $term)
                    ->orWhereHas('residents', function (Builder $qr) use ($term): void {
                        $qr->where('nama_lengkap', 'like', $term)
                            ->orWhere('nik', 'like', $term);
                    });
            });
        }

        // Filter Cepat
        if ($this->filterType === 'miskin_rentan') {
            $query->whereHas('socialProfile', function (Builder $q): void {
                $q->whereIn('kategori_ekonomi', ['miskin_ekstrem', 'rentan_miskin']);
            });
        } elseif ($this->filterType === 'belum_dtks') {
            $query->whereHas('socialProfile', function (Builder $q): void {
                $q->whereIn('kategori_ekonomi', ['miskin_ekstrem', 'rentan_miskin'])
                    ->where('status_dtks', 'belum_terdaftar');
            });
        } elseif ($this->filterType === 'terdaftar_dtks') {
            $query->whereHas('socialProfile', function (Builder $q): void {
                $q->where('status_dtks', 'terdaftar');
            });
        }

        // Order by priority score descending via subquery, then house number
        $houses = (clone $query)
            ->orderBy(
                HouseSocialProfile::select('skor_prioritas')
                    ->whereColumn('house_social_profiles.house_id', 'houses.id'),
                'desc'
            )
            ->orderBy('houses.nomor_rumah')
            ->paginate($this->perPage);

        // Hitung Statistik 4 Card Atas (Scope sesuai RT login / filter)
        $statQuery = House::query()->accessibleBy($user, $this->selectedRtId);

        $totalRumah = (clone $statQuery)->count();

        $totalRentanMiskin = (clone $statQuery)
            ->whereHas('socialProfile', fn ($q) => $q->whereIn('kategori_ekonomi', ['miskin_ekstrem', 'rentan_miskin']))
            ->count();

        $totalTerdaftarDtks = (clone $statQuery)
            ->whereHas('socialProfile', fn ($q) => $q->where('status_dtks', 'terdaftar'))
            ->count();

        $totalBelumDtks = (clone $statQuery)
            ->whereHas('socialProfile', fn ($q) => $q->whereIn('kategori_ekonomi', ['miskin_ekstrem', 'rentan_miskin'])->where('status_dtks', 'belum_terdaftar'))
            ->count();

        $totalLansiaDisabilitas = (clone $statQuery)
            ->where(function (Builder $q): void {
                $q->whereHas('socialProfile', fn ($sp) => $sp->where('ada_anggota_disabilitas_sakit_kronis', true)->orWhere('is_kepala_keluarga_janda_duda', true))
                    ->orWhereHas('residents', fn ($r) => $r->where('status_warga', 'aktif')->whereDate('tanggal_lahir', '<=', now()->subYears(60)));
            })
            ->count();

        $rts = $user->isRwLevel() ? Rt::orderBy('nomor_rt')->get() : collect();

        // Parameter untuk export Excel
        $exportParams = array_filter([
            'search' => $this->search,
            'rt_id' => $user->isRwLevel() ? $this->selectedRtId : null,
            'filter' => $this->filterType,
        ]);

        return view('livewire.social-aid.welfare-mapping-index', [
            'user' => $user,
            'rts' => $rts,
            'houses' => $houses,
            'totalRumah' => $totalRumah,
            'totalRentanMiskin' => $totalRentanMiskin,
            'totalTerdaftarDtks' => $totalTerdaftarDtks,
            'totalBelumDtks' => $totalBelumDtks,
            'totalLansiaDisabilitas' => $totalLansiaDisabilitas,
            'exportUrl' => route('social-aid.dtks-export', $exportParams),
        ]);
    }
}
