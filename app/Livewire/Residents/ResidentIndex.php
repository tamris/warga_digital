<?php

namespace App\Livewire\Residents;

use App\Models\Resident;
use App\Models\Rt;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Master Data Kependudukan Warga')]
class ResidentIndex extends Component
{
    use WithPagination;

    #[Url(as: 'cari')]
    public string $search = '';

    #[Url(as: 'rt')]
    public ?int $selectedRtId = null;

    #[Url(as: 'lansia')]
    public bool $filterLansia = false;

    #[Url(as: 'balita')]
    public bool $filterBalita = false;

    #[Url(as: 'domisili')]
    public bool $filterDomisiliLuar = false;

    #[Url(as: 'status')]
    public string $statusWarga = 'aktif';

    #[Url(as: 'urutkan')]
    public string $sortBy = 'terbaru';

    public function mount(): void
    {
        $user = auth()->user();
        if ($user && ! $user->isAdminRw() && $user->rt_id) {
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

    public function updatingFilterLansia(): void
    {
        $this->resetPage();
    }

    public function updatingFilterBalita(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDomisiliLuar(): void
    {
        $this->resetPage();
    }

    public function updatingStatusWarga(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterLansia = false;
        $this->filterBalita = false;
        $this->filterDomisiliLuar = false;
        $this->statusWarga = 'aktif';
        $this->sortBy = 'terbaru';

        $user = auth()->user();
        if ($user && $user->isAdminRw()) {
            $this->selectedRtId = null;
        }

        $this->resetPage();
    }

    public function updatingSortBy(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $user = auth()->user();
        $rts = Rt::orderBy('nomor_rt')->get();

        $query = Resident::query()
            ->with(['house.rt', 'user'])
            ->accessibleBy($user, $this->selectedRtId);

        // Filter Pencarian Teks
        if (trim($this->search) !== '') {
            $term = trim($this->search);
            $query->where(function (Builder $q) use ($term) {
                $q->where('nama_lengkap', 'like', "%{$term}%")
                    ->orWhere('nik', 'like', "%{$term}%")
                    ->orWhere('no_kk', 'like', "%{$term}%")
                    ->orWhere('pekerjaan', 'like', "%{$term}%")
                    ->orWhereHas('house', function (Builder $houseQuery) use ($term) {
                        $houseQuery->where('nomor_rumah', 'like', "%{$term}%");
                    });
            });
        }

        // Filter Lansia (usia >= 60 tahun)
        if ($this->filterLansia) {
            $threshold = Carbon::today()->subYears(60);
            $query->whereDate('tanggal_lahir', '<=', $threshold);
        }

        // Filter Balita (usia < 5 tahun)
        if ($this->filterBalita) {
            $threshold = Carbon::today()->subYears(5);
            $query->whereDate('tanggal_lahir', '>', $threshold);
        }

        // Filter Domisili Luar
        if ($this->filterDomisiliLuar) {
            $query->where('status_kependudukan', 'domisili_luar');
        }

        // Filter Status Warga
        if ($this->statusWarga !== 'semua' && $this->statusWarga !== '') {
            $query->where('status_warga', $this->statusWarga);
        }

        match ($this->sortBy) {
            'terlama' => $query->oldest('id'),
            'nama_asc' => $query->orderBy('nama_lengkap', 'asc'),
            'nama_desc' => $query->orderBy('nama_lengkap', 'desc'),
            default => $query->latest('id'),
        };

        $residents = $query->paginate(12);

        // Parameter query string untuk URL unduh Excel
        $exportParams = array_filter([
            'search' => $this->search,
            'rt_id' => $user && $user->isAdminRw() ? $this->selectedRtId : null,
            'filter_lansia' => $this->filterLansia ? 1 : null,
            'filter_balita' => $this->filterBalita ? 1 : null,
            'filter_domisili_luar' => $this->filterDomisiliLuar ? 1 : null,
            'status_warga' => $this->statusWarga,
        ]);

        return view('livewire.residents.resident-index', [
            'residents' => $residents,
            'rts' => $rts,
            'user' => $user,
            'exportUrl' => route('residents.export', $exportParams),
        ]);
    }
}
