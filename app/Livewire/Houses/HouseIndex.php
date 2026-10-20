<?php

namespace App\Livewire\Houses;

use App\Models\House;
use App\Models\Rt;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Daftar Unit Rumah Warga')]
class HouseIndex extends Component
{
    use WithPagination;

    #[Url(as: 'cari')]
    public string $search = '';

    #[Url(as: 'rt')]
    public ?int $selectedRtId = null;

    #[Url(as: 'status')]
    public string $statusHuni = '';

    // Properti Modal Form Tambah/Edit Rumah
    public bool $showModal = false;

    public ?int $editingHouseId = null;

    public ?int $form_rt_id = null;

    public string $form_nomor_rumah = '';

    public string $form_status_huni = 'dihuni_pemilik';

    public string $form_kategori_tarif = 'standar';

    public ?string $form_catatan = '';

    // Properti Konfirmasi Hapus
    public bool $showDeleteModal = false;

    public ?int $deletingHouseId = null;

    public string $deletingHouseNumber = '';

    public function mount(): void
    {
        $user = auth()->user();
        if ($user && ! $user->isAdminRw() && $user->rt_id) {
            $this->selectedRtId = $user->rt_id;
            $this->form_rt_id = $user->rt_id;
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

    public function updatingStatusHuni(): void
    {
        $this->resetPage();
    }

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

        // Aturan: Jika bukan admin_rw, paksa rt_id sesuai akun login
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

    public function render(): View
    {
        $user = auth()->user();

        $houses = House::with(['rt', 'headOfFamily'])
            ->accessibleBy($user, $this->selectedRtId)
            ->when($this->statusHuni, fn ($q) => $q->where('status_huni', $this->statusHuni))
            ->when($this->search, function ($query) {
                $query->where('nomor_rumah', 'like', '%'.$this->search.'%')
                    ->orWhereHas('residents', function ($q) {
                        $q->where('nama_lengkap', 'like', '%'.$this->search.'%');
                    });
            })
            ->withCount(['residents as total_jiwa' => fn ($q) => $q->where('status_warga', 'aktif')])
            ->orderBy('rt_id')
            ->orderBy('nomor_rumah')
            ->paginate(15);

        return view('livewire.houses.house-index', [
            'houses' => $houses,
            'rts' => $user->isAdminRw() ? Rt::orderBy('nomor_rt')->get() : collect(),
            'user' => $user,
        ]);
    }
}
