<?php

namespace App\Http\Controllers\Exporter;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\ExportApplication;
use App\Models\ProduceType;
use App\Services\JejakService;
use App\Services\QrImageService;
use App\Services\UploadService;
use App\Support\ApplicationInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class ApplicationController extends Controller
{
    public function __construct(
        private readonly JejakService $jejak,
        private readonly UploadService $uploads,
    ) {}

    public function index(Request $request): View
    {
        $applications = ExportApplication::query()
            ->with('produceType')
            ->where('company_id', $request->user()->company_id)
            ->latest()
            ->get();

        return view('exporter.applications.index', compact('applications'));
    }

    public function create(Request $request): View
    {
        return view('exporter.applications.create', [
            'companyName' => $request->user()->company?->name,
            'produceTypes' => ProduceType::query()->orderBy('name')->get(),
            'certificates' => Certificate::query()->where('company_id', $request->user()->company_id)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $application = $this->jejak->createApplication(
                $this->applicationInput($request, $request->user()->company_id)
            );
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
        $this->jejak->generateQr($application->id, $request->user());

        return redirect()->route('exporter.applications.show', $application);
    }

    public function show(string $id, Request $request, QrImageService $qrImage): View
    {
        $application = ExportApplication::query()->with(['company', 'produceType', 'qrCode'])->findOrFail($id);
        abort_unless($application->company_id === $request->user()->company_id, 404);

        return view('exporter.applications.show', [
            'application' => $application,
            'companyName' => $application->company?->name,
            'produceTypes' => ProduceType::query()->orderBy('name')->get(),
            'certificates' => Certificate::query()->where('company_id', $application->company_id)->get(),
            'publicUrl' => $application->qrCode ? $qrImage->traceUrl($application->qrCode->qr_code) : '',
        ]);
    }

    public function update(string $id, Request $request): RedirectResponse
    {
        abort_unless(ExportApplication::query()->where('id', $id)->where('company_id', $request->user()->company_id)->exists(), 404);
        try {
            $this->jejak->updateApplication($id, $this->applicationInput($request, $request->user()->company_id));
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
        $this->jejak->generateQr($id, $request->user());

        return redirect()->route('exporter.applications.show', $id);
    }

    public function submit(string $id, Request $request): RedirectResponse
    {
        abort_unless(ExportApplication::query()->where('id', $id)->where('company_id', $request->user()->company_id)->exists(), 404);
        $this->jejak->submitApplication($id, $request->user());

        return redirect()->route('exporter.applications.show', $id);
    }

    public function generateQr(string $id, Request $request): RedirectResponse
    {
        abort_unless(ExportApplication::query()->where('id', $id)->where('company_id', $request->user()->company_id)->exists(), 404);
        $this->jejak->generateQr($id, $request->user());

        return redirect()->route('exporter.applications.show', $id);
    }

    /**
     * @return array<string, mixed>
     */
    private function applicationInput(Request $request, string $companyId): array
    {
        $input = ApplicationInput::from($request, $companyId);
        $path = $this->uploads->save($request->file('displayImage'), 'applications');
        if ($path) {
            $input['display_image_path'] = $path;
        }

        return $input;
    }
}
