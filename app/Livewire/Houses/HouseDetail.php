<?php

namespace App\Livewire\Houses;

use App\Models\House;
use App\Models\Resident;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detail Rumah & Anggota Keluarga')]
class HouseDetail extends Component
{
    public House $house;

    // Properti Modal Form Warga (Bab 5B)
    public bool $showResidentModal = false;

    public ?int $editingResidentId = null;

    public string $form_no_kk = '';

    public string $form_nik = '';

    public string $form_nama_lengkap = '';

    public string $form_jenis_kelamin = 'L';

    public string $form_tempat_lahir = '';

    public string $form_tanggal_lahir = '';

    public string $form_agama = 'Islam';

    public string $form_pekerjaan = '';

    public string $form_status_keluarga = 'kepala_keluarga';

    public string $form_status_kependudukan = 'ktp_setempat';

    public ?string $form_no_wa = '';

    public string $form_status_warga = 'aktif';

    // Properti Modal Mutasi Pindah
    public bool $showMutasiModal = false;

    public ?int $mutasiResidentId = null;

    public string $mutasiResidentName = '';

    // Properti Modal Hapus Warga
    public bool $showDeleteModal = false;

    public ?int $deleteResidentId = null;

    public string $deleteResidentName = '';

    public function mount(int $id): void
    {
        $user = auth()->user();
        abort_if(! $user, 403);

        // Validasi scoping akses per user login (Sesuai Bab 4 baru.md)
        $this->house = House::with(['rt', 'residents' => function ($q) {
            $q->orderByRaw("CASE 
                WHEN status_keluarga = 'kepala_keluarga' THEN 1 
                WHEN status_keluarga = 'istri' THEN 2 
                WHEN status_keluarga = 'anak' THEN 3 
                ELSE 4 END")
                ->orderBy('tanggal_lahir', 'asc');
        }])->accessibleBy($user)->findOrFail($id);
    }

    public function openCreateResidentModal(): void
    {
        $user = auth()->user();
        abort_if(! $user || ! $user->canManageHouse(), 403, 'Anda tidak memiliki hak akses menambah data warga.');

        $this->resetValidation();
        $this->editingResidentId = null;

        // Auto-fill No. KK dari Kepala Keluarga jika sudah ada di rumah ini (Sesuai Bab 5B)
        $headOfFamily = $this->house->residents()->where('status_keluarga', 'kepala_keluarga')->first();
        $this->form_no_kk = $headOfFamily ? $headOfFamily->no_kk : '';

        $this->form_nik = '';
        $this->form_nama_lengkap = '';
        $this->form_jenis_kelamin = 'L';
        $this->form_tempat_lahir = '';
        $this->form_tanggal_lahir = '';
        $this->form_agama = 'Islam';
        $this->form_pekerjaan = '';
        $this->form_status_keluarga = $headOfFamily ? 'istri' : 'kepala_keluarga';
        $this->form_status_kependudukan = 'ktp_setempat';
        $this->form_no_wa = '';
        $this->form_status_warga = 'aktif';

        $this->showResidentModal = true;
    }

    public function openEditResidentModal(int $residentId): void
    {
        $user = auth()->user();
        abort_if(! $user || ! $user->canManageHouse(), 403, 'Anda tidak memiliki hak akses mengubah data warga.');

        $this->resetValidation();
        $resident = $this->house->residents()->findOrFail($residentId);

        $this->editingResidentId = $resident->id;
        $this->form_no_kk = $resident->no_kk;
        $this->form_nik = $resident->nik;
        $this->form_nama_lengkap = $resident->nama_lengkap;
        $this->form_jenis_kelamin = $resident->jenis_kelamin;
        $this->form_tempat_lahir = $resident->tempat_lahir;
        $this->form_tanggal_lahir = $resident->tanggal_lahir ? $resident->tanggal_lahir->format('Y-m-d') : '';
        $this->form_agama = $resident->agama;
        $this->form_pekerjaan = $resident->pekerjaan;
        $this->form_status_keluarga = $resident->status_keluarga;
        $this->form_status_kependudukan = $resident->status_kependudukan;
        $this->form_no_wa = $resident->no_wa ?? '';
        $this->form_status_warga = $resident->status_warga;

        $this->showResidentModal = true;
    }

    public function closeResidentModal(): void
    {
        $this->showResidentModal = false;
        $this->editingResidentId = null;
    }

    public function saveResident(): void
    {
        $uniqueNikRule = Rule::unique('residents', 'nik');
        if ($this->editingResidentId) {
            $uniqueNikRule = $uniqueNikRule->ignore($this->editingResidentId);
        }

        $this->validate([
            'form_no_kk' => ['required', 'digits:16'],
            'form_nik' => ['required', 'digits:16', $uniqueNikRule],
            'form_nama_lengkap' => ['required', 'string', 'max:150'],
            'form_jenis_kelamin' => ['required', 'in:L,P'],
            'form_tempat_lahir' => ['required', 'string', 'max:100'],
            'form_tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
            'form_agama' => ['required', 'in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu'],
            'form_pekerjaan' => ['required', 'string', 'max:100'],
            'form_status_keluarga' => ['required', 'in:kepala_keluarga,istri,anak,famili_lain'],
            'form_status_kependudukan' => ['required', 'in:ktp_setempat,domisili_luar'],
            'form_no_wa' => [
                $this->form_status_keluarga === 'kepala_keluarga' ? 'required' : 'nullable',
                'regex:/^08[0-9]{8,13}$/',
            ],
            'form_status_warga' => ['required', 'in:aktif,pindah,meninggal'],
        ], [
            'form_no_kk.required' => 'Nomor Kartu Keluarga (No. KK) wajib diisi.',
            'form_no_kk.digits' => 'Nomor Kartu Keluarga (No. KK) harus tepat 16 digit angka.',
            'form_nik.required' => 'Nomor Induk Kependudukan (NIK) wajib diisi.',
            'form_nik.digits' => 'Nomor Induk Kependudukan (NIK) harus tepat 16 digit angka.',
            'form_nik.unique' => 'NIK ini sudah terdaftar di database.',
            'form_nama_lengkap.required' => 'Nama lengkap wajib diisi sesuai KTP.',
            'form_tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'form_tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'form_pekerjaan.required' => 'Pekerjaan wajib diisi.',
            'form_no_wa.required' => 'Nomor WhatsApp wajib diisi untuk Kepala Keluarga.',
            'form_no_wa.regex' => 'Format nomor WhatsApp harus diawali 08 dengan panjang 10-15 digit angka.',
        ]);

        $data = [
            'house_id' => $this->house->id,
            'no_kk' => $this->form_no_kk,
            'nik' => $this->form_nik,
            'nama_lengkap' => strtoupper(trim($this->form_nama_lengkap)),
            'jenis_kelamin' => $this->form_jenis_kelamin,
            'tempat_lahir' => trim($this->form_tempat_lahir),
            'tanggal_lahir' => $this->form_tanggal_lahir,
            'agama' => $this->form_agama,
            'pekerjaan' => trim($this->form_pekerjaan),
            'status_keluarga' => $this->form_status_keluarga,
            'status_kependudukan' => $this->form_status_kependudukan,
            'no_wa' => $this->form_no_wa ? trim($this->form_no_wa) : null,
            'status_warga' => $this->form_status_warga,
        ];

        if ($this->editingResidentId) {
            $resident = $this->house->residents()->findOrFail($this->editingResidentId);
            $resident->update($data);
            session()->flash('success', "Data warga {$resident->nama_lengkap} berhasil diperbarui.");
        } else {
            Resident::create($data);
            session()->flash('success', "Anggota keluarga baru ({$data['nama_lengkap']}) berhasil ditambahkan ke rumah ini.");
        }

        $this->closeResidentModal();
        $this->house->load('residents');
    }

    public function confirmMutasi(int $residentId): void
    {
        $user = auth()->user();
        abort_if(! $user || ! $user->canManageHouse(), 403, 'Anda tidak memiliki hak akses mengubah status warga.');

        $resident = $this->house->residents()->findOrFail($residentId);
        $this->mutasiResidentId = $resident->id;
        $this->mutasiResidentName = $resident->nama_lengkap;
        $this->showMutasiModal = true;
    }

    public function executeMutasi(): void
    {
        $user = auth()->user();
        abort_if(! $user || ! $user->canManageHouse(), 403, 'Anda tidak memiliki hak akses mengubah status warga.');

        if ($this->mutasiResidentId) {
            $resident = $this->house->residents()->findOrFail($this->mutasiResidentId);
            $resident->update(['status_warga' => 'pindah']);
            session()->flash('success', "Warga {$resident->nama_lengkap} berhasil ditandai mutasi (Pindah Keluar). Riwayat tetap tersimpan di sistem.");
        }

        $this->showMutasiModal = false;
        $this->mutasiResidentId = null;
        $this->house->load('residents');
    }

    public function confirmDeleteResident(int $residentId): void
    {
        $user = auth()->user();
        abort_if(! $user || ! $user->canDeleteHouse(), 403, 'Hanya Ketua RT atau Admin RW yang dapat menghapus data warga.');

        $resident = $this->house->residents()->findOrFail($residentId);
        $this->deleteResidentId = $resident->id;
        $this->deleteResidentName = $resident->nama_lengkap;
        $this->showDeleteModal = true;
    }

    public function deleteResident(): void
    {
        $user = auth()->user();
        abort_if(! $user || ! $user->canDeleteHouse(), 403, 'Hanya Ketua RT atau Admin RW yang dapat menghapus data warga.');

        if ($this->deleteResidentId) {
            $resident = $this->house->residents()->findOrFail($this->deleteResidentId);
            $name = $resident->nama_lengkap;
            $resident->delete();
            session()->flash('success', "Data warga {$name} berhasil dihapus dari sistem.");
        }

        $this->showDeleteModal = false;
        $this->deleteResidentId = null;
        $this->house->load('residents');
    }

    public function render(): View
    {
        $headOfFamily = $this->house->residents->firstWhere('status_keluarga', 'kepala_keluarga');

        return view('livewire.houses.house-detail', [
            'house' => $this->house,
            'residents' => $this->house->residents,
            'headOfFamily' => $headOfFamily,
        ]);
    }
}
