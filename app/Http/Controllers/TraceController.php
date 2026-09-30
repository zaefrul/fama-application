<?php

namespace App\Http\Controllers;

use App\Domain\QrStatus;
use App\Models\ExportApplication;
use App\Services\JejakService;
use App\Services\QrImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TraceController extends Controller
{
    public function show(string $qrCode, Request $request, JejakService $jejak, QrImageService $qrImage): View
    {
        $lang = in_array($request->query('lang'), ['en', 'zh'], true) ? $request->query('lang') : 'bm';
        $qr = $jejak->getQrByCode($qrCode);
        if ($qr) {
            $jejak->recordQrAccess($qr);
        }
        $applicationId = $qr?->root_application_id ?: $qr?->application_id;
        $application = $applicationId
            ? ExportApplication::query()->with(['company.gallery', 'company.certificates', 'produceType'])->find($applicationId)
            : null;
        $certificates = $application ? $application->company->certificates : collect();
        $nutrition = $application ? (JejakService::nutritionByProduce()[$application->produce_type_id] ?? []) : [];
        $gallery = $application?->company?->gallery ?? collect();
        $heroImage = $application?->display_image_path
            ?: $gallery->firstWhere('category', 'BUAH')?->file_path
            ?: $gallery->first()?->file_path
            ?: asset('placeholders/gallery-buah.svg');
        $chain = $qr ? $qr->lineage() : collect();

        return view('trace.show', [
            'qrCode' => $qrCode,
            'lang' => $lang,
            'qr' => $qr,
            'application' => $application,
            'certificates' => $certificates,
            'nutrition' => $nutrition,
            'heroImage' => $heroImage,
            'accessCount' => $qr ? $qr->accesses()->count() : 0,
            'publicUrl' => $qr ? $qrImage->traceUrl($qr->qr_code) : '',
            'active' => $qr?->status === QrStatus::Active && $application !== null,
            'chain' => $chain,
        ]);
    }

    public function api(string $qrCode, JejakService $jejak): JsonResponse
    {
        $qr = $jejak->getQrByCode($qrCode);
        if (! $qr) {
            return response()->json(['error' => 'invalid'], 404);
        }
        if ($qr->status !== QrStatus::Active) {
            return response()->json([
                'qrCode' => $qr->qr_code,
                'status' => $qr->status->value,
                'active' => false,
            ]);
        }

        $application = ExportApplication::query()->with(['company', 'produceType'])->find($qr->root_application_id ?: $qr->application_id);
        if (! $application) {
            return response()->json(['error' => 'invalid'], 404);
        }

        return response()->json([
            'qrCode' => $qr->qr_code,
            'status' => $qr->status->value,
            'active' => true,
            'produce' => $application->produceType?->name,
            'grade' => $application->grade,
            'size' => $application->size,
            'quantity' => $qr->quantity ?? $application->quantity,
            'quantityUnit' => $application->quantity_unit,
            'trunkQuantity' => $application->quantity,
            'disposition' => $qr->disposition?->value,
            'chain' => $qr->traceChain(),
            'destinationCountry' => $application->destination_country,
            'exportDate' => $application->export_date?->toDateString(),
            'companyName' => $application->company?->name,
            'exporter' => $application->company?->name,
            'exporterAddress' => $application->company?->address,
            'lotNo' => $application->lot_no,
            'farmLocation' => $application->farm_location,
            'farmLat' => $application->farm_lat,
            'farmLng' => $application->farm_lng,
            'displayImage' => $application->display_image_path,
            'farmName' => $application->farm_name,
            'productKind' => $application->product_kind->value,
            'breed' => $application->variety,
            'headCount' => $application->head_count,
            'slaughterDate' => $application->slaughter_date?->toDateString(),
            'abattoirName' => $application->abattoir_name,
            'vetCertificateNo' => $application->vet_certificate_no,
            'importerName' => $application->importer_name,
            'importerAddress' => $application->importer_address,
            'cocNumber' => $application->coc_number,
        ]);
    }
}
