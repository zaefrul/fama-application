<?php

namespace App\Http\Controllers\Fama;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Company;
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

class CompanyController extends Controller
{
    public function __construct(
        private readonly JejakService $jejak,
        private readonly UploadService $uploads,
    ) {}

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $like = '%'.addcslashes($q, '%_\\').'%';

        $companies = Company::query()
            ->when($q !== '', function ($query) use ($like) {
                $query->where(function ($inner) use ($like) {
                    $inner->where('name', 'like', $like)
                        ->orWhere('registration_no', 'like', $like)
                        ->orWhere('external_account_no', 'like', $like);
                });
            })
            ->get()
            ->sortBy(fn (Company $company) => $company->nameSortKey(), SORT_NATURAL)
            ->values();

        return view('fama.companies.index', [
            'companies' => $companies,
            'q' => $q,
        ]);
    }

    public function create(Request $request): View
    {
        return view('fama.companies.create', ['error' => $request->query('error')]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $company = $this->jejak->createCompany($this->companyFields($request), $request->user());
        } catch (RuntimeException $e) {
            return redirect()->route('fama.companies.create', ['error' => $e->getMessage()]);
        }

        return redirect()->route('fama.companies.show', $company);
    }

    public function show(string $id, Request $request, JejakService $jejak): View
    {
        $company = Company::query()->with(['produce.produceType', 'certificates'])->findOrFail($id);
        $applications = ExportApplication::query()->where('company_id', $id)->get();
        $qrs = $jejak->listQrCodes($id);

        return view('fama.companies.show', [
            'company' => $company,
            'applications' => $applications,
            'qrs' => $qrs,
            'error' => $request->query('error'),
        ]);
    }

    public function update(string $id, Request $request): RedirectResponse
    {
        try {
            $this->jejak->updateManagedCompany($id, $this->companyFields($request), $request->user());
        } catch (RuntimeException $e) {
            return redirect()->route('fama.companies.show', ['id' => $id, 'error' => $e->getMessage()]);
        }

        return redirect()->route('fama.companies.show', $id);
    }

    public function createQr(string $id, Request $request): View
    {
        $company = Company::query()->findOrFail($id);

        return view('fama.companies.qr-create', [
            'company' => $company,
            'companyName' => $company->name,
            'produceTypes' => ProduceType::query()->orderBy('name')->get(),
            'certificates' => Certificate::query()->where('company_id', $id)->get(),
            'error' => $request->query('error'),
        ]);
    }

    public function storeQr(string $id, Request $request): RedirectResponse
    {
        try {
            $result = $this->jejak->createAndActivateQr(
                $id,
                $this->applicationInput($request, $id),
                $request->user(),
            );
        } catch (RuntimeException $e) {
            return redirect()->route('fama.companies.qr.create', ['id' => $id, 'error' => $e->getMessage()]);
        }

        return redirect()->route('fama.companies.qr.edit', [
            'id' => $id,
            'applicationId' => $result['application']->id,
        ]);
    }

    public function editQr(string $id, string $applicationId, Request $request, QrImageService $qrImage): View
    {
        $company = Company::query()->findOrFail($id);
        $application = ExportApplication::query()->with('qrCode')->findOrFail($applicationId);
        abort_unless($application->company_id === $id, 404);

        return view('fama.companies.qr-edit', [
            'company' => $company,
            'companyName' => $company->name,
            'application' => $application,
            'qr' => $application->qrCode,
            'produceTypes' => ProduceType::query()->orderBy('name')->get(),
            'certificates' => Certificate::query()->where('company_id', $id)->get(),
            'publicUrl' => $application->qrCode ? $qrImage->traceUrl($application->qrCode->qr_code) : '',
            'error' => $request->query('error'),
            'saved' => $request->query('saved'),
        ]);
    }

    public function updateQr(string $id, string $applicationId, Request $request): RedirectResponse
    {
        try {
            $this->jejak->updateManagedApplication(
                $applicationId,
                $this->applicationInput($request, $id),
                $request->user(),
            );
        } catch (RuntimeException $e) {
            return redirect()->route('fama.companies.qr.edit', [
                'id' => $id,
                'applicationId' => $applicationId,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('fama.companies.qr.edit', [
            'id' => $id,
            'applicationId' => $applicationId,
            'saved' => 1,
        ]);
    }

    public function addCertificate(string $id, Request $request): RedirectResponse
    {
        try {
            $documentPath = $this->uploads->save($request->file('document'), 'certificates', true, true);
        } catch (RuntimeException $e) {
            return redirect()->route('fama.companies.show', ['id' => $id, 'error' => $e->getMessage()]);
        }

        $this->jejak->addCertificate([
            'company_id' => $id,
            'type' => (string) $request->input('type', 'CoC'),
            'certificate_no' => (string) $request->input('certificateNo', ''),
            'document_path' => $documentPath ?? '/placeholders/certificate-coc.svg',
            'issue_date' => trim((string) $request->input('issueDate', '')) ?: now()->toDateString(),
            'expiry_date' => trim((string) $request->input('expiryDate', '')) ?: null,
            'status' => 'ACTIVE',
        ]);

        return redirect()->route('fama.companies.show', $id);
    }

    public function deleteCertificate(string $id, Request $request): RedirectResponse
    {
        Certificate::query()->where('id', $request->input('id'))->delete();

        return redirect()->route('fama.companies.show', $id);
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

    /**
     * @return array<string, string>
     */
    private function companyFields(Request $request): array
    {
        return [
            'name' => (string) $request->input('name', ''),
            'registration_no' => (string) $request->input('registrationNo', ''),
            'address' => (string) $request->input('address', ''),
            'state' => (string) $request->input('state', ''),
            'district' => (string) $request->input('district', ''),
            'postcode' => (string) $request->input('postcode', ''),
            'phone' => (string) $request->input('phone', ''),
            'email' => (string) $request->input('email', ''),
            'website' => (string) $request->input('website', ''),
        ];
    }
}
