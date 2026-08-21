<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Services\JejakService;
use Illuminate\Database\Seeder;

class CertificateDemoSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['id' => 'cert_abc_mygap', 'company_id' => 'co_abc', 'type' => 'MyGAP', 'certificate_no' => 'MYGAP-ABC-2025-04', 'issue_date' => '2025-04-12', 'expiry_date' => '2027-04-12'],
            ['id' => 'cert_abc_haccp', 'company_id' => 'co_abc', 'type' => 'HACCP', 'certificate_no' => 'HACCP-ABC-2025-01', 'issue_date' => '2025-03-01', 'expiry_date' => '2027-03-01'],
            ['id' => 'cert_abc_halal', 'company_id' => 'co_abc', 'type' => 'HALAL', 'certificate_no' => 'JAKIM-HALAL-DEMO-2026', 'issue_date' => '2025-06-01', 'expiry_date' => '2027-06-01'],
            ['id' => 'cert_abc_fito', 'company_id' => 'co_abc', 'type' => 'FITOSANITASI', 'certificate_no' => 'FITO-ABC-2026-09', 'issue_date' => '2026-01-20', 'expiry_date' => '2026-12-31'],
            ['id' => 'cert_abc_iso', 'company_id' => 'co_abc', 'type' => 'ISO 22000', 'certificate_no' => 'ISO22000-DEMO-2025', 'issue_date' => '2025-02-01', 'expiry_date' => '2028-02-01'],
            ['id' => 'cert_abc_coc', 'company_id' => 'co_abc', 'type' => 'CoC', 'certificate_no' => 'STB181019EJ100436', 'issue_date' => '2025-06-01', 'expiry_date' => '2026-12-31'],
            ['id' => 'cert_mts_coc', 'company_id' => 'co_mts', 'type' => 'CoC', 'certificate_no' => 'STB190220EJ200118', 'issue_date' => '2025-08-01', 'expiry_date' => '2026-12-31'],
            ['id' => 'cert_mts_haccp', 'company_id' => 'co_mts', 'type' => 'HACCP', 'certificate_no' => 'HACCP-MTS-2025-08', 'issue_date' => '2025-08-01', 'expiry_date' => '2027-08-01'],
        ] as $row) {
            Certificate::query()->updateOrCreate(
                ['id' => $row['id']],
                $row + [
                    'document_path' => JejakService::certificatePreviewPath($row['type']),
                    'status' => 'ACTIVE',
                ]
            );
        }
    }
}
