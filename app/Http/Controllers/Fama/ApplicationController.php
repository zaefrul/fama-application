<?php

namespace App\Http\Controllers\Fama;

use App\Domain\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\ExportApplication;
use App\Services\JejakService;
use App\Services\QrImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function __construct(private readonly JejakService $jejak) {}

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $query = ExportApplication::query()->with(['company', 'produceType']);
        if ($status) {
            $query->where('status', $status);
        }

        return view('fama.applications.index', [
            'applications' => $query->latest()->get(),
        ]);
    }

    public function show(string $id, Request $request, QrImageService $qrImage): View
    {
        $application = ExportApplication::query()->with(['company', 'produceType', 'qrCode', 'approvals'])->findOrFail($id);
        if ($application->status === ApplicationStatus::Submitted) {
            $application = $this->jejak->startReview($id, $request->user())->load(['company', 'produceType', 'qrCode', 'approvals']);
        }

        return view('fama.applications.show', [
            'application' => $application,
            'publicUrl' => $application->qrCode ? $qrImage->traceUrl($application->qrCode->qr_code) : '',
            'error' => $request->query('error'),
        ]);
    }

    public function approve(string $id, Request $request): RedirectResponse
    {
        $this->jejak->approveApplication($id, $request->user(), (string) $request->input('remarks', 'Diluluskan'));

        return redirect()->route('fama.applications.show', $id);
    }

    public function reject(string $id, Request $request): RedirectResponse
    {
        $remarks = trim((string) $request->input('remarks', ''));
        if ($remarks === '') {
            return redirect()->route('fama.applications.show', ['id' => $id, 'error' => 'remarks']);
        }
        $this->jejak->rejectApplication($id, $request->user(), $remarks);

        return redirect()->route('fama.applications.show', $id);
    }
}
