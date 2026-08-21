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
        return view('exporter.qr.index', [
            'qrs' => $jejak->listQrCodes($request->user()->company_id),
        ]);
    }

    public function show(string $id, Request $request, QrImageService $qrImage): View
    {
        $qr = QrCode::query()->with('application.produceType')->findOrFail($id);
        abort_unless($qr->application?->company_id === $request->user()->company_id, 404);

        return view('exporter.qr.show', [
            'qr' => $qr,
            'application' => $qr->application,
            'publicUrl' => $qrImage->traceUrl($qr->qr_code),
        ]);
    }

    public function downloadPage(string $id, Request $request, QrImageService $qrImage): View
    {
        $qr = QrCode::query()->with('application')->findOrFail($id);
        abort_unless($qr->application?->company_id === $request->user()->company_id, 404);

        return view('exporter.qr.download', [
            'qr' => $qr,
            'publicUrl' => $qrImage->traceUrl($qr->qr_code),
        ]);
    }

    public function download(string $id, Request $request, QrImageService $qrImage): Response
    {
        $user = $request->user();
        $qr = QrCode::query()->with('application')->findOrFail($id);
        $application = $qr->application;
        abort_unless($application, 404);
        if ($user->role === Role::Exporter && $application->company_id !== $user->company_id) {
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
