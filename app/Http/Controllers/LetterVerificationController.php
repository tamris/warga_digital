<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Illuminate\Contracts\View\View;

class LetterVerificationController extends Controller
{
    /**
     * Tampilkan halaman verifikasi keabsahan dokumen publik
     */
    public function verify(string $code): View
    {
        $letter = Letter::with(['rt'])->where('verification_code', $code)->first();

        return view('letters.verify', [
            'letter' => $letter,
            'code' => $code,
        ]);
    }
}
