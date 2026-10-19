<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;

class LetterPrintController extends Controller
{
    /**
     * Tampilan cetak / preview langsung di browser
     */
    public function show(Letter $letter): View
    {
        $user = auth()->user();

        // Validasi Hak Akses Isolasi RT/RW dan Peran Pengurus
        if (! $user || ! $user->canManageLetters() || (! $user->isAdminRw() && $user->rt_id !== $letter->rt_id)) {
            abort(403, 'Anda tidak memiliki akses ke surat pengantar di wilayah RT ini.');
        }

        return view('letters.print-view', [
            'letter' => $letter->load(['rt', 'resident']),
        ]);
    }

    /**
     * Unduh file PDF resmi via DomPDF
     */
    public function download(Letter $letter): Response
    {
        $user = auth()->user();

        if (! $user || ! $user->canManageLetters() || (! $user->isAdminRw() && $user->rt_id !== $letter->rt_id)) {
            abort(403, 'Anda tidak memiliki akses ke surat pengantar di wilayah RT ini.');
        }

        $letter->load(['rt', 'resident']);

        $pdf = Pdf::loadView('letters.pdf-template', [
            'letter' => $letter,
        ])->setPaper('a4', 'portrait');

        $safeName = preg_replace('/[^A-Za-z0-9_-]/', '_', $letter->snapshot_nama);
        $filename = "Surat_Pengantar_{$letter->nomor_urut}_{$safeName}.pdf";

        return $pdf->download($filename);
    }
}
