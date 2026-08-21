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
        $application = $qr ? ExportApplication::query()->with(['company.gallery', 'company.certificates', 'produceType'])->find($qr->application_id) : null;
        $certificates = $application ? $application->company->certificates : collect();
        $nutrition = $application ? (JejakService::nutritionByProduce()[$application->produce_type_id] ?? []) : [];
        $gallery = $application?->company?->gallery ?? collect();
        $heroImage = JejakService::produceImagePath($application?->produce_type_id)
            ?? $gallery->firstWhere('category', 'BUAH')?->file_path
            ?? $gallery->first()?->file_path
            ?? asset('placeholders/gallery-buah.svg');

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
            'active' => $qr?->status === QrStatus::Active,
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

        $application = ExportApplication::query()->with(['company', 'produceType'])->find($qr->application_id);
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
            'quantity' => $application->quantity,
            'quantityUnit' => $application->quantity_unit,
            'destinationCountry' => $application->destination_country,
            'exportDate' => $application->export_date?->toDateString(),
            'exporter' => $application->company?->name,
            'exporterAddress' => $application->company?->address,
            'farmName' => $application->farm_name,
            'importerName' => $application->importer_name,
            'importerAddress' => $application->importer_address,
            'cocNumber' => $application->coc_number,
        ]);
    }
}
