<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

class LetterVerificationController extends Controller
{
    /**
     * Tampilkan halaman verifikasi keabsahan dokumen publik
     */
    public function verify(string $code): View
    {
        $letter = Letter::with(['rt', 'resident'])->where('verification_code', $code)->first();

        return view('letters.verify', [
            'letter' => $letter,
            'code' => $code,
        ]);
    }

    /**
     * Tampilkan halaman cetak publik untuk warga (mandiri)
     */
    public function print(string $code): View
    {
        $letter = Letter::with(['rt', 'resident'])->where('verification_code', $code)->firstOrFail();

        if ($letter->status !== 'terbit') {
            abort(403, 'Surat belum disahkan secara resmi atau tidak berlaku.');
        }

        return view('letters.print-view', [
            'letter' => $letter,
            'isPublic' => true,
        ]);
    }

    /**
     * Unduh file PDF resmi publik untuk warga (mandiri)
     */
    public function download(string $code): Response
    {
        $letter = Letter::with(['rt', 'resident'])->where('verification_code', $code)->firstOrFail();

        if ($letter->status !== 'terbit') {
            abort(403, 'Surat belum disahkan secara resmi atau tidak berlaku.');
        }

        $pdf = Pdf::loadView('letters.pdf-template', [
            'letter' => $letter,
        ])->setPaper('a4', 'portrait');

        $safeName = preg_replace('/[^A-Za-z0-9_-]/', '_', $letter->snapshot_nama);
        $filename = "Surat_Pengantar_{$letter->nomor_urut}_{$safeName}.pdf";

        return $pdf->download($filename);
    }
}
