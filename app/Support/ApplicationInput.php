<?php

namespace App\Support;

use Illuminate\Http\Request;

final class ApplicationInput
{
    /**
     * @return array<string, mixed>
     */
    public static function from(Request $request, string $companyId): array
    {
        $cocId = (string) $request->input('cocCertificateId', '');

        return [
            'company_id' => $companyId,
            'produce_type_id' => (string) $request->input('produceTypeId', ''),
            'variety' => (string) $request->input('variety', ''),
            'grade' => (string) $request->input('grade', ''),
            'size' => (string) $request->input('size', ''),
            'quantity' => (int) $request->input('quantity', 0),
            'quantity_unit' => 'kg',
            'destination_country' => (string) $request->input('destinationCountry', ''),
            'coc_certificate_id' => $cocId !== '' ? $cocId : null,
            'coc_number' => (string) $request->input('cocNumber', ''),
            'export_date' => (string) $request->input('exportDate', ''),
            'farm_name' => (string) $request->input('farmName', ''),
            'importer_name' => (string) $request->input('importerName', ''),
            'importer_address' => (string) $request->input('importerAddress', ''),
        ];
    }
}
