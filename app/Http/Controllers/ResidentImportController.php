<?php

namespace App\Http\Controllers;

use App\Services\ResidentImportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResidentImportController extends Controller
{
    public function downloadTemplate(Request $request, ResidentImportService $importService): StreamedResponse
    {
        $user = auth()->user();
        $rtId = $user?->isRwLevel() ? $request->input('rt_id') : $user?->rt_id;

        return $importService->downloadTemplate($rtId ? (int) $rtId : null);
    }
}
