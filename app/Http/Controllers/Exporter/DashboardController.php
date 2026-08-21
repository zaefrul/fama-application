<?php

namespace App\Http\Controllers\Exporter;

use App\Domain\QrStatus;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ExportApplication;
use App\Models\GalleryItem;
use App\Services\JejakService;
use App\Services\QrImageService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, JejakService $jejak, QrImageService $qrImage): View
    {
        $user = $request->user();
        $companyId = $user->company_id;
        $company = $companyId ? Company::query()->find($companyId) : null;
        $stats = $companyId
            ? $jejak->dashboardExporter($companyId)
            : ['qrActive' => 0, 'qrInactive' => 0, 'totalApplications' => 0, 'approved' => 0, 'rejected' => 0];
        $applications = $companyId
            ? ExportApplication::query()->with('produceType')->where('company_id', $companyId)->latest()->get()
            : collect();
        $qrs = $companyId ? $jejak->listQrCodes($companyId) : collect();
        $gallery = $companyId ? GalleryItem::query()->where('company_id', $companyId)->get() : collect();
        $featuredQr = $qrs->first(fn ($qr) => $qr->status === QrStatus::Active) ?? $qrs->first();
        $featuredApp = $featuredQr
            ? $applications->firstWhere('id', $featuredQr->application_id)
            : null;

        return view('exporter.dashboard', [
            'company' => $company,
            'stats' => $stats,
            'applications' => $applications,
            'featuredQr' => $featuredQr,
            'featuredApp' => $featuredApp,
            'gallery' => $gallery,
            'publicUrl' => $featuredQr ? $qrImage->traceUrl($featuredQr->qr_code) : '',
        ]);
    }
}
