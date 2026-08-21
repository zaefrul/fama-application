<?php

namespace App\Http\Controllers\Exporter;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Company;
use App\Models\CompanyProduce;
use App\Models\GalleryItem;
use App\Models\ProduceType;
use App\Services\JejakService;
use App\Services\UploadService;
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

    public function show(Request $request): View
    {
        $company = Company::query()->find($request->user()->company_id);

        return view('exporter.company.show', [
            'company' => $company,
            'error' => $request->query('error'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $companyId = $request->user()->company_id;
        try {
            $logoPath = $this->uploads->save($request->file('logo'), 'logos');
        } catch (RuntimeException $e) {
            return redirect()->route('exporter.company', ['error' => $e->getMessage()]);
        }

        $this->jejak->updateCompany($companyId, [
            'address' => (string) $request->input('address', ''),
            'state' => (string) $request->input('state', ''),
            'district' => (string) $request->input('district', ''),
            'postcode' => (string) $request->input('postcode', ''),
            'phone' => (string) $request->input('phone', ''),
            'email' => (string) $request->input('email', ''),
            'website' => (string) $request->input('website', ''),
            'logo_path' => $logoPath,
        ]);

        return redirect()->route('exporter.company');
    }

    public function produce(Request $request): View
    {
        $companyId = $request->user()->company_id;

        return view('exporter.company.produce', [
            'types' => ProduceType::query()->orderBy('name')->get(),
            'produce' => CompanyProduce::query()->with('produceType')->where('company_id', $companyId)->get(),
        ]);
    }

    public function addProduce(Request $request): RedirectResponse
    {
        $this->jejak->addCompanyProduce($request->user()->company_id, (string) $request->input('produceTypeId'));

        return redirect()->route('exporter.produce');
    }

    public function removeProduce(Request $request): RedirectResponse
    {
        CompanyProduce::query()->where('id', $request->input('id'))->delete();

        return redirect()->route('exporter.produce');
    }

    public function certificates(Request $request): View
    {
        return view('exporter.company.certificates', [
            'certificates' => Certificate::query()->where('company_id', $request->user()->company_id)->get(),
            'error' => $request->query('error'),
        ]);
    }

    public function addCertificate(Request $request): RedirectResponse
    {
        try {
            $documentPath = $this->uploads->save($request->file('document'), 'certificates', true, true);
        } catch (RuntimeException $e) {
            return redirect()->route('exporter.certificates', ['error' => $e->getMessage()]);
        }

        $this->jejak->addCertificate([
            'company_id' => $request->user()->company_id,
            'type' => (string) $request->input('type', 'CoC'),
            'certificate_no' => (string) $request->input('certificateNo', ''),
            'document_path' => $documentPath ?? '/placeholders/certificate-coc.svg',
            'issue_date' => (string) $request->input('issueDate', now()->toDateString()),
            'expiry_date' => (string) $request->input('expiryDate', ''),
            'status' => 'ACTIVE',
        ]);

        return redirect()->route('exporter.certificates');
    }

    public function deleteCertificate(Request $request): RedirectResponse
    {
        Certificate::query()->where('id', $request->input('id'))->delete();

        return redirect()->route('exporter.certificates');
    }

    public function gallery(Request $request): View
    {
        return view('exporter.company.gallery', [
            'items' => GalleryItem::query()->where('company_id', $request->user()->company_id)->get(),
            'error' => $request->query('error'),
        ]);
    }

    public function addGallery(Request $request): RedirectResponse
    {
        try {
            $filePath = $this->uploads->save($request->file('image'), 'gallery', false, true);
        } catch (RuntimeException $e) {
            return redirect()->route('exporter.gallery', ['error' => $e->getMessage()]);
        }

        $this->jejak->addGalleryItem([
            'company_id' => $request->user()->company_id,
            'category' => (string) $request->input('category', 'BUAH'),
            'description' => (string) $request->input('description', ''),
            'file_path' => $filePath ?? '/placeholders/gallery-buah.svg',
            'uploaded_by' => $request->user()->id,
        ]);

        return redirect()->route('exporter.gallery');
    }

    public function deleteGallery(Request $request): RedirectResponse
    {
        GalleryItem::query()->where('id', $request->input('id'))->delete();

        return redirect()->route('exporter.gallery');
    }
}
