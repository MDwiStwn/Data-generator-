<?php

namespace App\Http\Controllers;

use App\Services\ExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

// Controller untuk handle download data (CSV & Excel)
class TestDataController extends Controller
{
    public function __construct(
        private ExportService $exportService
    ) {}

    // Export CSV
    public function exportCsv(Request $request): StreamedResponse|BinaryFileResponse|RedirectResponse
    {
        $sessionData = session('generated_data');

        // Cek data session milik user yang login
        if (! $sessionData || (isset($sessionData['user_id']) && $sessionData['user_id'] !== auth()->id())) {
            return back()->with('error', 'Tidak ada data untuk diekspor. Generate data terlebih dahulu.');
        }

        return $this->exportService->exportCsv(
            $sessionData['category'],
            $sessionData['headers'],
            $sessionData['keys'],
            $sessionData['data']
        );
    }

    // Export Excel XLSX
    public function exportExcel(Request $request): BinaryFileResponse|StreamedResponse|RedirectResponse
    {
        $sessionData = session('generated_data');

        // Cek data session milik user yang login
        if (! $sessionData || (isset($sessionData['user_id']) && $sessionData['user_id'] !== auth()->id())) {
            return back()->with('error', 'Tidak ada data untuk diekspor. Generate data terlebih dahulu.');
        }

        return $this->exportService->exportExcel(
            $sessionData['category'],
            $sessionData['headers'],
            $sessionData['keys'],
            $sessionData['data']
        );
    }
}
