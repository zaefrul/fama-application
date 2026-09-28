<?php

namespace App\Support;

use App\Domain\ProductKind;
use Illuminate\Http\Request;

final class ApplicationInput
{
    /**
     * @return array<string, mixed>
     */
    public static function from(Request $request, string $companyId): array
    {
        $cocId = (string) $request->input('cocCertificateId', '');
        $exportDate = trim((string) $request->input('exportDate', ''));
        $lotNo = trim((string) $request->input('lotNo', ''));
        $farmLocation = trim((string) $request->input('farmLocation', ''));
        $farmLat = $request->input('farmLat');
        $farmLng = $request->input('farmLng');
        $kind = ProductKind::tryFrom((string) $request->input('productKind', '')) ?? ProductKind::Produce;
        $slaughterDate = trim((string) $request->input('slaughterDate', ''));
        $headCount = $request->input('headCount');

        return [
            'company_id' => $companyId,
            'product_kind' => $kind->value,
            'produce_type_id' => (string) $request->input('produceTypeId', ''),
            'new_produce_name' => trim((string) $request->input('newProduceName', '')),
            'variety' => (string) $request->input('variety', ''),
            'grade' => (string) $request->input('grade', ''),
            'size' => (string) $request->input('size', ''),
            'quantity' => (int) $request->input('quantity', 0),
            'quantity_unit' => 'kg',
            'head_count' => $headCount !== null && $headCount !== '' ? (int) $headCount : null,
            'destination_country' => (string) $request->input('destinationCountry', ''),
            'coc_certificate_id' => $cocId !== '' ? $cocId : null,
            'coc_number' => (string) $request->input('cocNumber', ''),
            'vet_certificate_no' => trim((string) $request->input('vetCertificateNo', '')),
            'export_date' => $exportDate !== '' ? $exportDate : null,
            'slaughter_date' => $slaughterDate !== '' ? $slaughterDate : null,
            'abattoir_name' => trim((string) $request->input('abattoirName', '')),
            'lot_no' => $lotNo !== '' ? $lotNo : null,
            'farm_location' => $farmLocation !== '' ? $farmLocation : null,
            'farm_lat' => $farmLat !== null && $farmLat !== '' ? (float) $farmLat : null,
            'farm_lng' => $farmLng !== null && $farmLng !== '' ? (float) $farmLng : null,
            'farm_name' => (string) $request->input('farmName', ''),
            'importer_name' => (string) $request->input('importerName', ''),
            'importer_address' => (string) $request->input('importerAddress', ''),
        ];
    }
}
