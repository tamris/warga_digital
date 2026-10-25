<?php

namespace App\Http\Controllers;

use App\Models\Rt;
use App\Models\SocialAidProgram;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SocialAidPrintController extends Controller
{
    /**
     * Tampilan Preview & Cetak Langsung Browser: Daftar Hadir Penyaluran Bansos
     */
    public function attendancePrint(SocialAidProgram $program): View
    {
        $user = auth()->user();

        if (! $user || ! $user->canManageSocialAid()) {
            abort(403, 'Anda tidak memiliki hak akses ke modul bantuan sosial.');
        }

        $program->load(['quotas.rt']);

        $requestedRtId = request()->query('rt_id');
        $targetRtId = ($user->isPengurusRt() && $user->rt_id)
            ? $user->rt_id
            : ($requestedRtId ? (int) $requestedRtId : null);

        $recipientsQuery = $program->recipients()
            ->with(['house.rt', 'resident'])
            ->whereIn('status_usulan', ['disetujui_rw', 'diusulkan_rt']);

        if ($targetRtId) {
            $recipientsQuery->where('rt_id', $targetRtId);
        }

        $recipients = $recipientsQuery->orderBy('rt_id')
            ->orderBy('nomor_urut_kupon', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $rtContext = $targetRtId ? Rt::find($targetRtId) : null;
        $rts = $user->isRwLevel() ? Rt::orderBy('nomor_rt')->get() : collect();
        $ketuaRw = User::where('role', 'admin_rw')->first();

        return view('social-aid.attendance-print', [
            'program' => $program,
            'recipients' => $recipients,
            'user' => $user,
            'rtContext' => $rtContext,
            'targetRtId' => $targetRtId,
            'rts' => $rts,
            'ketuaRw' => $ketuaRw,
        ]);
    }

    /**
     * Unduh PDF Resmi: Daftar Hadir & Tanda Terima Penyaluran Bansos
     */
    public function attendancePdf(SocialAidProgram $program): Response
    {
        $user = auth()->user();

        if (! $user || ! $user->canManageSocialAid()) {
            abort(403, 'Anda tidak memiliki hak akses ke modul bantuan sosial.');
        }

        $program->load(['quotas.rt']);

        $requestedRtId = request()->query('rt_id');
        $targetRtId = ($user->isPengurusRt() && $user->rt_id)
            ? $user->rt_id
            : ($requestedRtId ? (int) $requestedRtId : null);

        $recipientsQuery = $program->recipients()
            ->with(['house.rt', 'resident'])
            ->whereIn('status_usulan', ['disetujui_rw', 'diusulkan_rt']);

        if ($targetRtId) {
            $recipientsQuery->where('rt_id', $targetRtId);
        }

        $recipients = $recipientsQuery->orderBy('rt_id')
            ->orderBy('nomor_urut_kupon', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $rtContext = $targetRtId ? Rt::find($targetRtId) : null;
        $ketuaRw = User::where('role', 'admin_rw')->first();

        $pdf = Pdf::loadView('social-aid.attendance-pdf', [
            'program' => $program,
            'recipients' => $recipients,
            'user' => $user,
            'rtContext' => $rtContext,
            'ketuaRw' => $ketuaRw,
        ])->setPaper('a4', 'portrait');

        $safeProgramName = preg_replace('/[^A-Za-z0-9_-]/', '_', $program->nama_program);
        $rtSuffix = $rtContext ? "_RT_{$rtContext->nomor_rt}" : '_Semua_RT';
        $filename = "Daftar_Hadir_Bansos_{$safeProgramName}{$rtSuffix}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Tampilan Preview & Cetak Langsung Browser: Berita Acara Penggantian Penerima
     */
    public function replacementPrint(SocialAidProgram $program): View
    {
        $user = auth()->user();

        if (! $user || ! $user->canManageSocialAid()) {
            abort(403, 'Anda tidak memiliki hak akses ke modul bantuan sosial.');
        }

        $program->load(['quotas.rt']);

        $requestedRtId = request()->query('rt_id');
        $targetRtId = ($user->isPengurusRt() && $user->rt_id)
            ? $user->rt_id
            : ($requestedRtId ? (int) $requestedRtId : null);

        $replacementsQuery = $program->recipients()
            ->with(['house.rt', 'resident', 'replacementResident'])
            ->where(function ($q) {
                $q->whereNotNull('alasan_penggantian')
                    ->orWhereNotNull('digantikan_oleh_resident_id')
                    ->orWhere('status_usulan', 'diganti');
            });

        if ($targetRtId) {
            $replacementsQuery->where('rt_id', $targetRtId);
        }

        $replacements = $replacementsQuery->orderBy('rt_id')
            ->orderBy('id', 'asc')
            ->get();

        $rtContext = $targetRtId ? Rt::find($targetRtId) : null;
        $rts = $user->isRwLevel() ? Rt::orderBy('nomor_rt')->get() : collect();
        $ketuaRw = User::where('role', 'admin_rw')->first();

        return view('social-aid.replacement-print', [
            'program' => $program,
            'replacements' => $replacements,
            'user' => $user,
            'rtContext' => $rtContext,
            'targetRtId' => $targetRtId,
            'rts' => $rts,
            'ketuaRw' => $ketuaRw,
        ]);
    }

    /**
     * Unduh PDF Resmi: Berita Acara Penggantian Penerima Bantuan
     */
    public function replacementPdf(SocialAidProgram $program): Response
    {
        $user = auth()->user();

        if (! $user || ! $user->canManageSocialAid()) {
            abort(403, 'Anda tidak memiliki hak akses ke modul bantuan sosial.');
        }

        $program->load(['quotas.rt']);

        $requestedRtId = request()->query('rt_id');
        $targetRtId = ($user->isPengurusRt() && $user->rt_id)
            ? $user->rt_id
            : ($requestedRtId ? (int) $requestedRtId : null);

        $replacementsQuery = $program->recipients()
            ->with(['house.rt', 'resident', 'replacementResident'])
            ->where(function ($q) {
                $q->whereNotNull('alasan_penggantian')
                    ->orWhereNotNull('digantikan_oleh_resident_id')
                    ->orWhere('status_usulan', 'diganti');
            });

        if ($targetRtId) {
            $replacementsQuery->where('rt_id', $targetRtId);
        }

        $replacements = $replacementsQuery->orderBy('rt_id')
            ->orderBy('id', 'asc')
            ->get();

        $rtContext = $targetRtId ? Rt::find($targetRtId) : null;
        $ketuaRw = User::where('role', 'admin_rw')->first();

        $pdf = Pdf::loadView('social-aid.replacement-pdf', [
            'program' => $program,
            'replacements' => $replacements,
            'user' => $user,
            'rtContext' => $rtContext,
            'ketuaRw' => $ketuaRw,
        ])->setPaper('a4', 'portrait');

        $safeProgramName = preg_replace('/[^A-Za-z0-9_-]/', '_', $program->nama_program);
        $rtSuffix = $rtContext ? "_RT_{$rtContext->nomor_rt}" : '_Semua_RT';
        $filename = "Berita_Acara_Penggantian_{$safeProgramName}{$rtSuffix}.pdf";

        return $pdf->download($filename);
    }
}
