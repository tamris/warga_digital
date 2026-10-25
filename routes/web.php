<?php

use App\Http\Controllers\LetterPrintController;
use App\Http\Controllers\LetterVerificationController;
use App\Http\Controllers\ResidentExportController;
use App\Http\Controllers\ResidentImportController;
use App\Http\Controllers\SocialAidDtksExportController;
use App\Http\Controllers\SocialAidPrintController;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Finance\CashLedgerIndex;
use App\Livewire\Finance\DuesMatrix;
use App\Livewire\Houses\HouseDetail;
use App\Livewire\Houses\HouseIndex;
use App\Livewire\Letters\LetterCreate;
use App\Livewire\Letters\LetterList;
use App\Livewire\Public\PublicSensus;
use App\Livewire\Residents\ResidentIndex;
use App\Livewire\Rts\RtIndex;
use App\Livewire\SocialAid\ProgramDetail;
use App\Livewire\SocialAid\ProgramIndex;
use App\Livewire\SocialAid\WelfareMappingIndex;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Rute Publik (Tamu, Sensus Mandiri Warga, & Verifikasi Surat Scan QR TTE)
Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::get('/sensus/{nomor_rt?}', PublicSensus::class)->name('public.sensus');
Route::get('/verifikasi-surat/{code}', [LetterVerificationController::class, 'verify'])->name('letters.verify');

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
    Route::get('/residents/import/template', [ResidentImportController::class, 'downloadTemplate'])->name('residents.import.template');

    // Modul Master Wilayah RT (Khusus Admin RW)
    Route::get('/rts', RtIndex::class)->name('rts.index');

    // Modul Surat Pengantar RT/RW
    Route::get('/letters', LetterList::class)->name('letters.index');
    Route::get('/letters/create', LetterCreate::class)->name('letters.create');
    Route::get('/letters/{letter}/print', [LetterPrintController::class, 'show'])->name('letters.print');
    Route::get('/letters/{letter}/download', [LetterPrintController::class, 'download'])->name('letters.download');

    // Modul Keuangan & Kas RT/RW
    Route::get('/finance/dues', DuesMatrix::class)->name('finance.dues');
    Route::get('/finance/cash-ledger', CashLedgerIndex::class)->name('finance.cash-ledger');

    // Modul Kesejahteraan (DTKS) & Penyaluran Bansos Tepat Sasaran
    Route::get('/social-aid/welfare-mapping', WelfareMappingIndex::class)->name('social-aid.welfare-mapping');
    Route::get('/social-aid/programs', ProgramIndex::class)->name('social-aid.programs.index');
    Route::get('/social-aid/programs/{program}', ProgramDetail::class)->name('social-aid.programs.show');
    Route::get('/social-aid/programs/{program}/attendance-print', [SocialAidPrintController::class, 'attendancePrint'])->name('social-aid.programs.attendance-print');
    Route::get('/social-aid/programs/{program}/attendance-pdf', [SocialAidPrintController::class, 'attendancePdf'])->name('social-aid.programs.attendance-pdf');
    Route::get('/social-aid/programs/{program}/replacement-print', [SocialAidPrintController::class, 'replacementPrint'])->name('social-aid.programs.replacement-print');
    Route::get('/social-aid/programs/{program}/replacement-pdf', [SocialAidPrintController::class, 'replacementPdf'])->name('social-aid.programs.replacement-pdf');
    Route::get('/social-aid/dtks-export', [SocialAidDtksExportController::class, 'export'])->name('social-aid.dtks-export');
});
