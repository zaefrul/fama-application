<?php

namespace App\Http\Controllers\Exporter;

use App\Domain\Role;
use App\Http\Controllers\Controller;
use App\Models\QrCode;
use App\Services\JejakService;
use App\Services\QrImageService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class QrController extends Controller
{
    public function index(Request $request, JejakService $jejak): View
    {
        $companyId = $request->user()->company_id;
        $with = ['application.produceType', 'rootApplication.produceType', 'pendingHolder'];

        return view('exporter.qr.index', [
            'ownQrs' => $jejak->listQrCodes($companyId),
            'receivedQrs' => QrCode::query()
                ->with($with)
                ->whereNotNull('parent_id')
                ->where(function ($query) use ($companyId) {
                    $query->where('holder_company_id', $companyId)
                        ->orWhere('pending_holder_company_id', $companyId);
                })
                ->orderByDesc('generated_at')
                ->get(),
        ]);
    }

    public function show(string $id, Request $request, QrImageService $qrImage): View
    {
        $qr = QrCode::query()->with(['application.produceType', 'parent', 'rootApplication.produceType'])->findOrFail($id);
        abort_unless($qr->accessibleByCompany($request->user()->company_id), 404);

        return view('exporter.qr.show', [
            'qr' => $qr,
            'application' => $qr->application ?? $qr->rootApplication,
            'publicUrl' => $qrImage->traceUrl($qr->qr_code),
        ]);
    }

    public function downloadPage(string $id, Request $request, QrImageService $qrImage): View
    {
        $qr = QrCode::query()->with(['application', 'parent', 'rootApplication'])->findOrFail($id);
        abort_unless($qr->accessibleByCompany($request->user()->company_id), 404);

        return view('exporter.qr.download', [
            'qr' => $qr,
            'publicUrl' => $qrImage->traceUrl($qr->qr_code),
        ]);
    }

    public function download(string $id, Request $request, QrImageService $qrImage): Response
    {
        $user = $request->user();
        $qr = QrCode::query()->with(['application', 'parent'])->findOrFail($id);
        if ($user->role === Role::Exporter && ! $qr->accessibleByCompany($user->company_id)) {
            abort(403);
        }

        $sizeCm = (int) $request->query('size', 5);
        $format = $request->query('format', 'png');
        $publicUrl = $qrImage->traceUrl($qr->qr_code);

        if ($format === 'pdf') {
            return response($qrImage->pdf($publicUrl, $qr->qr_code, $sizeCm), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$qr->qr_code.'.pdf"',
            ]);
        }

        return response($qrImage->png($publicUrl, max(160, $sizeCm * 48)), 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="'.$qr->qr_code.'.png"',
        ]);
    }

    public function audit(Request $request, JejakService $jejak): View
    {
        return view('exporter.audit', [
            'logs' => $jejak->listAudit($request->user()->company_id),
        ]);
    }
}
