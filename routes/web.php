<?php

use App\Http\Controllers\ResidentExportController;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Houses\HouseDetail;
use App\Livewire\Houses\HouseIndex;
use App\Livewire\Residents\ResidentIndex;
use App\Livewire\Rts\RtIndex;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Rute Autentikasi Tamu
Route::get('/login', Login::class)->name('login')->middleware('guest');

// Rute Keluar (Logout)
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout')->middleware('auth');

// Rute Switch Akun Cepat (Mempermudah Pengujian Multi-RT & Peran)
Route::post('/auth/switch', function (Request $request) {
    $request->validate(['user_id' => 'required|exists:users,id']);
    $user = User::findOrFail($request->input('user_id'));
    Auth::login($user);
    $request->session()->regenerate();

    return back()->with('success', "Beralih ke akun {$user->nama} ({$user->role_label})");
})->name('auth.switch');

// Rute Aplikasi Utama (Wajib Login)
Route::middleware('auth')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');

    // Modul Data Unit Rumah
    Route::get('/houses', HouseIndex::class)->name('houses.index');
    Route::get('/houses/{id}', HouseDetail::class)->name('houses.show');

    // Modul Master Data Warga
    Route::get('/residents', ResidentIndex::class)->name('residents.index');
    Route::get('/residents/export', [ResidentExportController::class, 'export'])->name('residents.export');

    // Modul Master Wilayah RT (Khusus Admin RW)
    Route::get('/rts', RtIndex::class)->name('rts.index');
});
