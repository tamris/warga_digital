<?php

namespace App\Livewire\Rts;

use App\Models\Rt;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Master Data Wilayah RT')]
class RtIndex extends Component
{
    // RT Modal
    public bool $showModal = false;

    public ?int $editingRtId = null;

    public string $nomor_rt = '';

    public ?string $nama_ketua = '';

    public ?string $no_wa_ketua = '';

    // Modal Akun Pengurus
    public bool $showUserModal = false;

    public ?int $editingUserId = null;

    public ?int $selectedRtIdForUser = null;

    public string $user_nama = '';

    public string $user_username = '';

    public string $user_password = '';

    public string $user_role = 'ketua_rt';

    public string $user_no_wa = '';

    // Filter & Search untuk tabel akun pengurus
    public string $accountSearch = '';

    public string $accountRtFilter = '';

    public string $accountRoleFilter = '';

    // Modal Konfirmasi Hapus Akun
    public bool $showDeleteUserModal = false;

    public ?int $deletingUserId = null;

    public string $deletingUserName = '';

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->isAdminRw()) {
            abort(403, 'Akses terbatas hanya untuk Ketua RW.');
        }
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->editingRtId = null;
        $this->nomor_rt = '';
        $this->nama_ketua = '';
        $this->no_wa_ketua = '';
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $rt = Rt::findOrFail($id);
        $this->editingRtId = $rt->id;
        $this->nomor_rt = $rt->nomor_rt;
        $this->nama_ketua = $rt->nama_ketua;
        $this->no_wa_ketua = $rt->no_wa_ketua;
        $this->showModal = true;
    }

    public function saveRt(): void
    {
        $uniqueRule = $this->editingRtId ? 'unique:rts,nomor_rt,'.$this->editingRtId : 'unique:rts,nomor_rt';

        $this->validate([
            'nomor_rt' => 'required|string|max:5|'.$uniqueRule,
            'nama_ketua' => 'nullable|string|max:100',
            'no_wa_ketua' => 'nullable|string|max:20',
        ], [
            'nomor_rt.required' => 'Nomor RT wajib diisi (contoh: 004).',
            'nomor_rt.unique' => 'Nomor RT ini sudah terdaftar.',
        ]);

        if ($this->editingRtId) {
            $rt = Rt::findOrFail($this->editingRtId);
            $rt->update([
                'nomor_rt' => $this->nomor_rt,
                'nama_ketua' => $this->nama_ketua,
                'no_wa_ketua' => $this->no_wa_ketua,
            ]);
            session()->flash('success', "Data RT {$this->nomor_rt} berhasil diperbarui.");
        } else {
            Rt::create([
                'nomor_rt' => $this->nomor_rt,
                'nama_ketua' => $this->nama_ketua,
                'no_wa_ketua' => $this->no_wa_ketua,
            ]);
            session()->flash('success', "Wilayah RT {$this->nomor_rt} baru berhasil ditambahkan.");
        }

        $this->showModal = false;
    }

    public function openCreateUserModal(?int $rtId = null): void
    {
        $this->resetValidation();
        $this->editingUserId = null;
        $this->selectedRtIdForUser = $rtId ?? Rt::orderBy('nomor_rt')->first()?->id;
        $this->user_nama = '';
        $this->user_username = '';
        $this->user_password = 'password';
        $this->user_role = 'ketua_rt';
        $this->user_no_wa = '';
        $this->showUserModal = true;
    }

    public function openEditUserModal(int $userId): void
    {
        $this->resetValidation();
        $user = User::findOrFail($userId);
        $this->editingUserId = $user->id;
        $this->selectedRtIdForUser = $user->rt_id;
        $this->user_nama = $user->nama;
        $this->user_username = $user->username;
        $this->user_password = ''; // Kosongkan jika tidak ingin ganti kata sandi
        $this->user_role = $user->role;
        $this->user_no_wa = $user->no_wa ?? '';
        $this->showUserModal = true;
    }

    public function saveUser(): void
    {
        if ($this->editingUserId) {
            $this->validate([
                'selectedRtIdForUser' => 'required|exists:rts,id',
                'user_nama' => 'required|string|max:100',
                'user_username' => 'required|string|max:50|unique:users,username,'.$this->editingUserId,
                'user_password' => 'nullable|string|min:4',
                'user_role' => 'required|in:ketua_rt,sekretaris_rt,bendahara_rt',
                'user_no_wa' => 'required|string|max:20',
            ], [
                'selectedRtIdForUser.required' => 'Wilayah RT penugasan wajib dipilih.',
                'user_nama.required' => 'Nama lengkap pengurus wajib diisi.',
                'user_username.required' => 'Username login wajib diisi.',
                'user_username.unique' => 'Username ini sudah digunakan.',
                'user_password.min' => 'Kata sandi minimal 4 karakter.',
                'user_no_wa.required' => 'Nomor WhatsApp pengurus wajib diisi.',
            ]);

            $user = User::findOrFail($this->editingUserId);
            $data = [
                'rt_id' => $this->selectedRtIdForUser,
                'nama' => $this->user_nama,
                'username' => $this->user_username,
                'role' => $this->user_role,
                'no_wa' => $this->user_no_wa,
            ];

            if (! empty($this->user_password)) {
                $data['password'] = Hash::make($this->user_password);
            }

            $user->update($data);

            // Sinkronkan data Ketua RT jika perannya ketua_rt
            if ($this->user_role === 'ketua_rt') {
                Rt::where('id', $this->selectedRtIdForUser)->update([
                    'nama_ketua' => $this->user_nama,
                    'no_wa_ketua' => $this->user_no_wa,
                ]);
            }

            session()->flash('success', "Data akun pengurus ({$user->nama}) berhasil diperbarui.");
        } else {
            $this->validate([
                'selectedRtIdForUser' => 'required|exists:rts,id',
                'user_nama' => 'required|string|max:100',
                'user_username' => 'required|string|max:50|unique:users,username',
                'user_password' => 'required|string|min:4',
                'user_role' => 'required|in:ketua_rt,sekretaris_rt,bendahara_rt',
                'user_no_wa' => 'required|string|max:20',
            ], [
                'selectedRtIdForUser.required' => 'Wilayah RT penugasan wajib dipilih.',
                'user_nama.required' => 'Nama lengkap pengurus wajib diisi.',
                'user_username.required' => 'Username login wajib diisi.',
                'user_username.unique' => 'Username ini sudah digunakan.',
                'user_password.required' => 'Kata sandi awal wajib diisi (contoh: password).',
                'user_password.min' => 'Kata sandi minimal 4 karakter.',
                'user_no_wa.required' => 'Nomor WhatsApp pengurus wajib diisi.',
            ]);

            User::create([
                'rt_id' => $this->selectedRtIdForUser,
                'nama' => $this->user_nama,
                'username' => $this->user_username,
                'password' => Hash::make($this->user_password),
                'role' => $this->user_role,
                'no_wa' => $this->user_no_wa,
            ]);

            // Sinkronkan data Ketua RT jika perannya ketua_rt
            if ($this->user_role === 'ketua_rt') {
                Rt::where('id', $this->selectedRtIdForUser)->update([
                    'nama_ketua' => $this->user_nama,
                    'no_wa_ketua' => $this->user_no_wa,
                ]);
            }

            session()->flash('success', "Akun pengurus baru ({$this->user_nama}) berhasil dibuat.");
        }

        $this->showUserModal = false;
    }

    public function confirmDeleteUser(int $userId): void
    {
        $user = User::findOrFail($userId);
        if ($user->id === auth()->id() || $user->isAdminRw()) {
            session()->flash('error', 'Akun Ketua RW tidak dapat dihapus.');

            return;
        }

        $this->deletingUserId = $user->id;
        $this->deletingUserName = $user->nama;
        $this->showDeleteUserModal = true;
    }

    public function deleteUser(): void
    {
        if ($this->deletingUserId) {
            $user = User::find($this->deletingUserId);
            if ($user && $user->id !== auth()->id() && ! $user->isAdminRw()) {
                $nama = $user->nama;
                $user->delete();
                session()->flash('success', "Akun pengurus ({$nama}) berhasil dihapus.");
            }
        }

        $this->showDeleteUserModal = false;
        $this->deletingUserId = null;
        $this->deletingUserName = '';
    }

    public function render(): View
    {
        $rts = Rt::withCount(['houses', 'residents' => function ($q) {
            $q->where('status_warga', 'aktif');
        }, 'users'])
            ->orderBy('nomor_rt')
            ->get();

        $accountsQuery = User::with('rt')->whereNotNull('rt_id');

        if ($this->accountRtFilter !== '') {
            $accountsQuery->where('rt_id', $this->accountRtFilter);
        }

        if ($this->accountRoleFilter !== '') {
            $accountsQuery->where('role', $this->accountRoleFilter);
        }

        if (trim($this->accountSearch) !== '') {
            $term = '%'.trim($this->accountSearch).'%';
            $accountsQuery->where(function ($q) use ($term) {
                $q->where('nama', 'like', $term)
                    ->orWhere('username', 'like', $term)
                    ->orWhere('no_wa', 'like', $term);
            });
        }

        $accounts = $accountsQuery->orderBy('rt_id')->orderBy('role')->get();

        return view('livewire.rts.rt-index', [
            'rts' => $rts,
            'accounts' => $accounts,
        ]);
    }
}
