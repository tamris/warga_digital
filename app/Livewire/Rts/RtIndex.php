<?php

namespace App\Livewire\Rts;

use App\Models\CashLedger;
use App\Models\HouseDue;
use App\Models\Letter;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Master Data Wilayah RT')]
class RtIndex extends Component
{
    use WithPagination;

    // Tab Navigasi ('rt' | 'rw')
    public string $activeTab = 'rt';

    // RT Modal
    public bool $showModal = false;

    public ?int $editingRtId = null;

    public string $nomor_rt = '';

    public ?string $nama_ketua = '';

    public ?string $no_wa_ketua = '';

    // Modal Akun Pengurus
    public bool $showUserModal = false;

    public bool $isRwOfficerModal = false;

    public ?int $editingUserId = null;

    public ?int $selectedRtIdForUser = null;

    public ?int $selectedResidentId = null;

    public string $searchResident = '';

    public string $user_nama = '';

    public string $user_username = '';

    public string $user_password = '';

    public string $user_role = 'ketua_rt';

    public string $user_no_wa = '';

    public bool $user_is_active = true;

    // Filter & Search untuk tabel akun pengurus
    public string $accountSearch = '';

    public string $accountRtFilter = '';

    public string $accountRoleFilter = '';

    public int $accountPerPage = 10;

    public function switchTab(string $tab): void
    {
        if (auth()->user()?->isKetuaRt()) {
            $this->activeTab = 'rt';

            return;
        }

        if (in_array($tab, ['rt', 'rw'], true)) {
            $this->activeTab = $tab;
            $this->resetPage();
        }
    }

    public function updatingAccountSearch(): void
    {
        $this->resetPage();
    }

    public function updatingAccountRtFilter(): void
    {
        $this->resetPage();
    }

    public function updatingAccountRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatingAccountPerPage(): void
    {
        $this->resetPage();
    }

    // Modal Konfirmasi Hapus Akun
    public bool $showDeleteUserModal = false;

    public ?int $deletingUserId = null;

    public string $deletingUserName = '';

    public bool $deletingUserHasHistory = false;

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->canManageCredentials()) {
            abort(403, 'Akses terbatas hanya untuk Ketua RW dan Ketua RT.');
        }

        if ($user->isKetuaRt()) {
            $this->activeTab = 'rt';
            $this->accountRtFilter = (string) $user->rt_id;
        }
    }

    public function openCreateModal(): void
    {
        if (! auth()->user()?->isAdminRw()) {
            session()->flash('error', 'Hanya Ketua RW yang berwenang menambah wilayah RT.');

            return;
        }

        $this->resetValidation();
        $this->editingRtId = null;
        $this->nomor_rt = '';
        $this->nama_ketua = '';
        $this->no_wa_ketua = '';
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        if (! auth()->user()?->isAdminRw()) {
            session()->flash('error', 'Hanya Ketua RW yang berwenang mengubah data wilayah RT.');

            return;
        }

        $this->resetValidation();
        $rt = Rt::findOrFail($id);
        $this->editingRtId = $rt->id;
        $this->nomor_rt = $rt->nomor_rt;
        $this->nama_ketua = $rt->nama_ketua;
        $rawWa = preg_replace('/[^0-9]/', '', (string) ($rt->no_wa_ketua ?? ''));
        $this->no_wa_ketua = $rawWa ? implode('-', str_split($rawWa, 4)) : '';
        $this->showModal = true;
    }

    public function saveRt(): void
    {
        if (! auth()->user()?->isAdminRw()) {
            abort(403, 'Hanya Ketua RW yang berwenang mengelola data wilayah RT.');
        }

        if ($this->no_wa_ketua) {
            $this->no_wa_ketua = preg_replace('/[^0-9]/', '', (string) $this->no_wa_ketua);
        }

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

    public function openCreateUserModal(?int $rtId = null, bool $isRw = false): void
    {
        $this->resetValidation();
        $this->editingUserId = null;
        $currentUser = auth()->user();

        if ($currentUser->isKetuaRt()) {
            $this->isRwOfficerModal = false;
            $this->selectedRtIdForUser = $currentUser->rt_id;
            $this->user_role = 'sekretaris_rt';
        } else {
            $this->isRwOfficerModal = $isRw || $this->activeTab === 'rw';

            if ($this->isRwOfficerModal) {
                $this->selectedRtIdForUser = null;
                $this->user_role = 'sekretaris_rw';
            } else {
                $this->selectedRtIdForUser = $rtId ?? Rt::orderBy('nomor_rt')->first()?->id;
                $this->user_role = 'ketua_rt';
            }
        }

        $this->selectedResidentId = null;
        $this->searchResident = '';
        $this->user_nama = '';
        $this->user_username = '';
        $this->user_password = 'password';
        $this->user_no_wa = '';
        $this->user_is_active = true;
        $this->showUserModal = true;
    }

    public function openEditUserModal(int $userId): void
    {
        $this->resetValidation();
        $currentUser = auth()->user();
        $user = User::with('resident.house.rt')->findOrFail($userId);

        if ($currentUser->isKetuaRt() && $user->rt_id !== $currentUser->rt_id) {
            abort(403, 'Anda hanya dapat mengelola akun di wilayah RT Anda.');
        }

        $this->editingUserId = $user->id;
        $this->selectedRtIdForUser = $user->rt_id;
        $this->isRwOfficerModal = is_null($user->rt_id) || $user->isRwLevel();
        $this->selectedResidentId = $user->resident_id;
        $this->searchResident = $user->resident ? $user->resident->nama_lengkap : '';
        $this->user_nama = $user->nama;
        $this->user_username = $user->username;
        $this->user_password = ''; // Kosongkan jika tidak ingin ganti kata sandi
        $this->user_role = $user->role;
        $this->user_is_active = $user->isActive();
        $rawWa = preg_replace('/[^0-9]/', '', (string) ($user->no_wa ?? ''));
        $this->user_no_wa = $rawWa ? implode('-', str_split($rawWa, 4)) : '';
        $this->showUserModal = true;
    }

    public function updatedUserNama(string $value): void
    {
        if ($this->selectedResidentId) {
            $resident = Resident::find($this->selectedResidentId);
            if (! $resident || trim($resident->nama_lengkap) !== trim($value)) {
                $this->selectedResidentId = null;
                $this->searchResident = '';
                if (trim($value) === '') {
                    $this->user_username = '';
                    $this->user_no_wa = '';
                }
            }
        }
    }

    public function updatedSelectedRtIdForUser(?int $rtId): void
    {
        $this->selectedResidentId = null;
        $this->searchResident = '';
    }

    public function selectResident(int $residentId): void
    {
        if ($this->isRwOfficerModal) {
            $resident = Resident::where('status_warga', 'aktif')
                ->with('house.rt')
                ->find($residentId);
        } else {
            $resident = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $this->selectedRtIdForUser))
                ->where('status_warga', 'aktif')
                ->with('house.rt')
                ->find($residentId);
        }

        if ($resident) {
            $this->selectedResidentId = $resident->id;
            $this->searchResident = $resident->nama_lengkap;
            $this->user_nama = $resident->nama_lengkap;

            if ($resident->no_wa) {
                $digits = preg_replace('/[^0-9]/', '', $resident->no_wa);
                $this->user_no_wa = $digits ? implode('-', str_split($digits, 4)) : '';
            }

            // Otomatis sarankan username jika sedang buat akun baru & username masih kosong
            if (! $this->editingUserId && empty($this->user_username)) {
                $firstWord = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode(' ', $resident->nama_lengkap)[0] ?? 'rw'));
                if ($this->isRwOfficerModal) {
                    if ($this->user_role === 'sekretaris_rw' && ! User::where('username', 'sekretaris_rw')->exists()) {
                        $this->user_username = 'sekretaris_rw';
                    } elseif ($this->user_role === 'bendahara_rw' && ! User::where('username', 'bendahara_rw')->exists()) {
                        $this->user_username = 'bendahara_rw';
                    } else {
                        $this->user_username = 'rw_'.$firstWord;
                    }
                } else {
                    $rtNum = $resident->house?->rt?->nomor_rt ?? '01';
                    $this->user_username = 'rt'.ltrim((string) $rtNum, '0').'_'.$firstWord;
                }
            }
        }
    }

    public function resetSelectedResident(): void
    {
        $this->selectedResidentId = null;
        $this->searchResident = '';
        $this->user_nama = '';
        $this->user_username = '';
        $this->user_no_wa = '';
    }

    public function updatedSelectedResidentId($residentId): void
    {
        if ($residentId) {
            $this->selectResident((int) $residentId);
        } else {
            $this->resetSelectedResident();
        }
    }

    public function saveUser(): void
    {
        $this->user_no_wa = preg_replace('/[^0-9]/', '', (string) $this->user_no_wa);
        $currentUser = auth()->user();

        if ($this->isRwOfficerModal) {
            if ($currentUser->isKetuaRt()) {
                abort(403, 'Akses ditolak.');
            }

            $uniqueRule = $this->editingUserId ? 'unique:users,username,'.$this->editingUserId : 'unique:users,username';
            $passwordRule = $this->editingUserId ? 'nullable|string|min:4' : 'required|string|min:4';

            $this->validate([
                'selectedResidentId' => 'nullable|exists:residents,id',
                'user_nama' => 'required|string|max:100',
                'user_username' => 'required|string|max:50|'.$uniqueRule,
                'user_password' => $passwordRule,
                'user_role' => 'required|in:admin_rw,ketua_rw,sekretaris_rw,bendahara_rw',
                'user_no_wa' => 'required|string|max:20',
            ], [
                'user_nama.required' => 'Nama lengkap pengurus wajib diisi.',
                'user_username.required' => 'Username login wajib diisi.',
                'user_username.unique' => 'Username ini sudah digunakan.',
                'user_password.required' => 'Kata sandi awal wajib diisi (contoh: password).',
                'user_password.min' => 'Kata sandi minimal 4 karakter.',
                'user_no_wa.required' => 'Nomor WhatsApp pengurus wajib diisi.',
            ]);

            if ($this->editingUserId) {
                $user = User::findOrFail($this->editingUserId);
                $data = [
                    'rt_id' => null,
                    'resident_id' => $this->selectedResidentId ?: null,
                    'nama' => $this->user_nama,
                    'username' => $this->user_username,
                    'role' => $this->user_role,
                    'no_wa' => $this->user_no_wa,
                    'is_active' => $this->user_is_active,
                ];

                if (! empty($this->user_password)) {
                    $data['password'] = Hash::make($this->user_password);
                }

                $user->update($data);

                if ($this->selectedResidentId) {
                    $resident = Resident::find($this->selectedResidentId);
                    if ($resident && empty($resident->no_wa) && ! empty($this->user_no_wa)) {
                        $resident->update(['no_wa' => $this->user_no_wa]);
                    }
                }

                session()->flash('success', "Data akun pengurus RW ({$user->nama}) berhasil diperbarui.");
            } else {
                User::create([
                    'rt_id' => null,
                    'resident_id' => $this->selectedResidentId ?: null,
                    'nama' => $this->user_nama,
                    'username' => $this->user_username,
                    'password' => Hash::make($this->user_password),
                    'role' => $this->user_role,
                    'no_wa' => $this->user_no_wa,
                    'is_active' => true,
                ]);

                if ($this->selectedResidentId) {
                    $resident = Resident::find($this->selectedResidentId);
                    if ($resident && empty($resident->no_wa) && ! empty($this->user_no_wa)) {
                        $resident->update(['no_wa' => $this->user_no_wa]);
                    }
                }

                session()->flash('success', "Akun pengurus RW baru ({$this->user_nama}) berhasil dibuat.");
            }

            $this->showUserModal = false;

            return;
        }

        // Penanganan Akun Pengurus RT
        if ($currentUser->isKetuaRt()) {
            $this->selectedRtIdForUser = $currentUser->rt_id;
        }

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);

            if ($currentUser->isKetuaRt() && $user->rt_id !== $currentUser->rt_id) {
                abort(403, 'Anda hanya dapat mengelola akun di wilayah RT Anda.');
            }

            $allowedRoles = ($currentUser->isKetuaRt() && $user->id !== $currentUser->id)
                ? 'required|in:sekretaris_rt,bendahara_rt'
                : 'required|in:ketua_rt,sekretaris_rt,bendahara_rt';

            $this->validate([
                'selectedRtIdForUser' => 'required|exists:rts,id',
                'selectedResidentId' => 'nullable|exists:residents,id',
                'user_nama' => 'required|string|max:100',
                'user_username' => 'required|string|max:50|unique:users,username,'.$this->editingUserId,
                'user_password' => 'nullable|string|min:4',
                'user_role' => $allowedRoles,
                'user_no_wa' => 'required|string|max:20',
            ], [
                'selectedRtIdForUser.required' => 'Wilayah RT penugasan wajib dipilih.',
                'user_nama.required' => 'Nama lengkap pengurus wajib diisi.',
                'user_username.required' => 'Username login wajib diisi.',
                'user_username.unique' => 'Username ini sudah digunakan.',
                'user_password.min' => 'Kata sandi minimal 4 karakter.',
                'user_no_wa.required' => 'Nomor WhatsApp pengurus wajib diisi.',
            ]);

            $targetRole = ($currentUser->isKetuaRt() && $user->id === $currentUser->id)
                ? 'ketua_rt'
                : $this->user_role;

            $targetActive = ($user->id === $currentUser->id)
                ? true
                : $this->user_is_active;

            $data = [
                'rt_id' => $this->selectedRtIdForUser,
                'resident_id' => $this->selectedResidentId ?: null,
                'nama' => $this->user_nama,
                'username' => $this->user_username,
                'role' => $targetRole,
                'no_wa' => $this->user_no_wa,
                'is_active' => $targetActive,
            ];

            if (! empty($this->user_password)) {
                $data['password'] = Hash::make($this->user_password);
            }

            $user->update($data);

            if ($this->selectedResidentId) {
                $resident = Resident::find($this->selectedResidentId);
                if ($resident && empty($resident->no_wa) && ! empty($this->user_no_wa)) {
                    $resident->update(['no_wa' => $this->user_no_wa]);
                }
            }

            if ($this->user_role === 'ketua_rt' && $this->user_is_active) {
                Rt::where('id', $this->selectedRtIdForUser)->update([
                    'nama_ketua' => $this->user_nama,
                    'no_wa_ketua' => $this->user_no_wa,
                ]);
            }

            session()->flash('success', "Data akun pengurus ({$user->nama}) berhasil diperbarui.");
        } else {
            $allowedRoles = $currentUser->isKetuaRt()
                ? 'required|in:sekretaris_rt,bendahara_rt'
                : 'required|in:ketua_rt,sekretaris_rt,bendahara_rt';

            $this->validate([
                'selectedRtIdForUser' => 'required|exists:rts,id',
                'selectedResidentId' => 'nullable|exists:residents,id',
                'user_nama' => 'required|string|max:100',
                'user_username' => 'required|string|max:50|unique:users,username',
                'user_password' => 'required|string|min:4',
                'user_role' => $allowedRoles,
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
                'resident_id' => $this->selectedResidentId ?: null,
                'nama' => $this->user_nama,
                'username' => $this->user_username,
                'password' => Hash::make($this->user_password),
                'role' => $this->user_role,
                'no_wa' => $this->user_no_wa,
                'is_active' => true,
            ]);

            if ($this->selectedResidentId) {
                $resident = Resident::find($this->selectedResidentId);
                if ($resident && empty($resident->no_wa) && ! empty($this->user_no_wa)) {
                    $resident->update(['no_wa' => $this->user_no_wa]);
                }
            }

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

    public function toggleUserStatus(int $userId): void
    {
        $currentUser = auth()->user();
        $user = User::findOrFail($userId);

        if ($user->id === $currentUser->id || $user->isAdminRw()) {
            session()->flash('error', 'Status akun ini tidak dapat diubah.');

            return;
        }

        if ($currentUser->isKetuaRt()) {
            if ($user->rt_id !== $currentUser->rt_id) {
                abort(403, 'Anda hanya dapat mengubah status akun di wilayah RT Anda.');
            }
            if ($user->isKetuaRt()) {
                session()->flash('error', 'Status akun Ketua RT tidak dapat diubah.');

                return;
            }
        }

        $newStatus = ! $user->isActive();
        $user->is_active = $newStatus;
        $user->save();

        $statusLabel = $newStatus ? 'diaktifkan kembali' : 'dinonaktifkan (Purna Tugas)';
        session()->flash('success', "Status akun pengurus {$user->nama} berhasil {$statusLabel}.");
    }

    public function confirmDeleteUser(int $userId): void
    {
        $currentUser = auth()->user();
        $user = User::findOrFail($userId);

        if ($user->id === $currentUser->id || $user->isAdminRw()) {
            session()->flash('error', 'Akun Ketua tidak dapat dihapus.');

            return;
        }

        if ($currentUser->isKetuaRt()) {
            if ($user->rt_id !== $currentUser->rt_id) {
                abort(403, 'Anda hanya dapat menghapus akun di wilayah RT Anda.');
            }
            if ($user->isKetuaRt()) {
                session()->flash('error', 'Akun Ketua RT tidak dapat dihapus.');

                return;
            }
        }

        $this->deletingUserId = $user->id;
        $this->deletingUserName = $user->nama;

        $hasLetters = Letter::where('created_by_user_id', $user->id)->exists();
        $hasCash = CashLedger::where('user_id', $user->id)->orWhere('accepted_by_user_id', $user->id)->exists();
        $hasDues = HouseDue::where('received_by_user_id', $user->id)->exists();
        $this->deletingUserHasHistory = $hasLetters || $hasCash || $hasDues;

        $this->showDeleteUserModal = true;
    }

    public function deactivateFromDeleteModal(): void
    {
        if ($this->deletingUserId) {
            $this->toggleUserStatus($this->deletingUserId);
        }

        $this->showDeleteUserModal = false;
        $this->deletingUserId = null;
        $this->deletingUserName = '';
        $this->deletingUserHasHistory = false;
    }

    public function deleteUser(): void
    {
        if ($this->deletingUserId) {
            $currentUser = auth()->user();
            $user = User::find($this->deletingUserId);

            if ($user && $user->id !== $currentUser->id && ! $user->isAdminRw()) {
                if ($currentUser->isKetuaRt() && ($user->rt_id !== $currentUser->rt_id || $user->isKetuaRt())) {
                    abort(403, 'Aksi tidak diizinkan.');
                }

                try {
                    $nama = $user->nama;
                    $user->delete();
                    session()->flash('success', "Akun pengurus ({$nama}) berhasil dihapus.");
                } catch (\Throwable $e) {
                    session()->flash('error', "Gagal menghapus akun {$user->nama}: data masih terikat dengan arsip surat atau transaksi.");
                }
            }
        }

        $this->showDeleteUserModal = false;
        $this->deletingUserId = null;
        $this->deletingUserName = '';
        $this->deletingUserHasHistory = false;
    }

    public function render(): View
    {
        $currentUser = auth()->user();

        $rtsQuery = Rt::withCount(['houses', 'residents' => function ($q) {
            $q->where('status_warga', 'aktif');
        }, 'users']);

        if ($currentUser->isKetuaRt()) {
            $rtsQuery->where('id', $currentUser->rt_id);
        }

        $rts = $rtsQuery->orderBy('nomor_rt')->get();

        $accountsQuery = User::with(['rt', 'resident.house'])->whereNotNull('rt_id');

        if ($currentUser->isKetuaRt()) {
            $accountsQuery->where('rt_id', $currentUser->rt_id);
        } elseif ($this->accountRtFilter !== '') {
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
                    ->orWhere('no_wa', 'like', $term)
                    ->orWhereHas('resident', fn ($resQ) => $resQ->where('nik', 'like', $term));
            });
        }

        $accounts = $accountsQuery->orderBy('rt_id')->orderBy('role')->paginate($this->accountPerPage);

        $rwAccounts = $currentUser->isKetuaRt()
            ? collect()
            : User::with(['resident.house.rt'])
                ->where(function ($q) {
                    $q->whereNull('rt_id')
                        ->orWhereIn('role', ['admin_rw', 'ketua_rw', 'sekretaris_rw', 'bendahara_rw']);
                })
                ->orderByRaw("CASE WHEN role IN ('admin_rw', 'ketua_rw') THEN 1 WHEN role = 'sekretaris_rw' THEN 2 WHEN role = 'bendahara_rw' THEN 3 ELSE 4 END")
                ->orderBy('nama')
                ->get();

        $residentSuggestions = collect();
        $searchTerm = trim($this->searchResident ?: $this->user_nama);
        if (! $this->selectedResidentId && strlen($searchTerm) >= 2) {
            $s = '%'.$searchTerm.'%';
            if ($this->isRwOfficerModal && $currentUser->isAdminRw()) {
                $residentSuggestions = Resident::where('status_warga', 'aktif')
                    ->where(function ($q) use ($s) {
                        $q->where('nama_lengkap', 'like', $s)
                            ->orWhere('nik', 'like', $s)
                            ->orWhereHas('house', fn ($hq) => $hq->where('nomor_rumah', 'like', $s));
                    })
                    ->with('house.rt')
                    ->take(6)
                    ->get();
            } else {
                $targetRtId = $currentUser->isKetuaRt() ? $currentUser->rt_id : $this->selectedRtIdForUser;
                if ($targetRtId) {
                    $residentSuggestions = Resident::whereHas('house', fn ($q) => $q->where('rt_id', $targetRtId))
                        ->where('status_warga', 'aktif')
                        ->where(function ($q) use ($s) {
                            $q->where('nama_lengkap', 'like', $s)
                                ->orWhere('nik', 'like', $s)
                                ->orWhereHas('house', fn ($hq) => $hq->where('nomor_rumah', 'like', $s));
                        })
                        ->with('house.rt')
                        ->take(6)
                        ->get();
                }
            }
        }

        $selectedResident = null;
        if ($this->selectedResidentId) {
            $selectedResident = Resident::with('house.rt')->find($this->selectedResidentId);
        }

        return view('livewire.rts.rt-index', [
            'rts' => $rts,
            'accounts' => $accounts,
            'rwAccounts' => $rwAccounts,
            'residentSuggestions' => $residentSuggestions,
            'selectedResident' => $selectedResident,
        ]);
    }
}
