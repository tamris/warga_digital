<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Masuk ke Portal Warga RW')]
class Login extends Component
{
    public string $username = '';

    public string $password = '';

    public bool $remember = false;

    /**
     * @var array<string, string>
     */
    protected array $rules = [
        'username' => 'required|string',
        'password' => 'required|string',
    ];

    /**
     * @var array<string, string>
     */
    protected array $messages = [
        'username.required' => 'Nama pengguna (username) wajib diisi.',
        'password.required' => 'Kata sandi (password) wajib diisi.',
    ];

    public function login(): void
    {
        $this->validate();

        $input = trim($this->username);

        // Cari user berdasarkan username, nama lengkap, atau variasi slug nama (misal: amat_maja / AMAT MAJA)
        $user = User::where('username', $input)
            ->orWhere('nama', $input)
            ->orWhereRaw('LOWER(REPLACE(nama, " ", "_")) = ?', [strtolower($input)])
            ->orWhereRaw('LOWER(REPLACE(nama, " ", "")) = ?', [strtolower(str_replace(['_', ' '], '', $input))])
            ->first();

        if ($user && ! $user->isActive()) {
            $this->addError('username', 'Akun pengurus ini berstatus nonaktif (purna tugas). Silakan hubungi Ketua RW.');

            return;
        }

        $credentials = $user
            ? ['username' => $user->username, 'password' => $this->password]
            : ['username' => $input, 'password' => $this->password];

        if (Auth::attempt($credentials, $this->remember)) {
            session()->regenerate();
            session()->flash('success', 'Selamat datang kembali, '.Auth::user()->nama.'!');

            $this->redirectRoute('dashboard');

            return;
        }

        $this->addError('username', 'Nama pengguna atau kata sandi yang Anda masukkan tidak cocok.');
    }

    public function quickLogin(int|string $identifier): void
    {
        $user = is_numeric($identifier)
            ? User::find((int) $identifier)
            : User::where('username', $identifier)->orWhere('role', $identifier)->first();

        if ($user) {
            if (! $user->isActive()) {
                session()->flash('error', 'Akun pengurus ini berstatus nonaktif (purna tugas).');

                return;
            }

            Auth::login($user);
            session()->regenerate();
            session()->flash('success', 'Berhasil masuk sebagai '.$user->nama.' ('.$user->role_label.')');

            $this->redirectRoute('dashboard');
        }
    }

    public function render(): View
    {
        return view('livewire.auth.login');
    }
}
