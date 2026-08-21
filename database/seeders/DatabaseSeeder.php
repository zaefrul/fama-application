<?php

namespace Database\Seeders;

use App\Domain\ApplicationStatus;
use App\Domain\QrStatus;
use App\Domain\Role;
use App\Models\AppNotification;
use App\Models\Approval;
use App\Models\AuditLog;
use App\Models\Certificate;
use App\Models\Company;
use App\Models\CompanyProduce;
use App\Models\ExportApplication;
use App\Models\GalleryItem;
use App\Models\ProduceType;
use App\Models\QrCode;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $produce = [
            ['id' => 'pt_durian', 'name' => 'Durian'],
            ['id' => 'pt_nangka', 'name' => 'Nangka'],
            ['id' => 'pt_manggis', 'name' => 'Manggis'],
            ['id' => 'pt_tembikai', 'name' => 'Tembikai'],
            ['id' => 'pt_nanas', 'name' => 'Nanas'],
            ['id' => 'pt_pisang', 'name' => 'Pisang'],
            ['id' => 'pt_betik', 'name' => 'Betik'],
            ['id' => 'pt_mangga', 'name' => 'Mangga'],
        ];
        foreach ($produce as $row) {
            ProduceType::query()->create($row);
        }

        Company::query()->create([
            'id' => 'co_abc',
            'registration_no' => 'AB34567',
            'external_account_no' => 'H0B00001',
            'name' => 'ABC Fruits Sdn. Bhd.',
            'email' => 'abcfruits@gmail.com',
            'phone' => '03-3344 5566',
            'address' => 'No. 3, Jalan Sultan, Klang, Selangor',
            'state' => 'Selangor',
            'district' => 'Klang',
            'postcode' => '41000',
            'website' => 'https://abcfruits.example',
            'logo_path' => '/logos/logo-fama.png',
            'external_source' => 'DAGANGNET',
            'external_status' => 'Aktif',
            'created_at' => '2024-01-15 00:00:00',
            'updated_at' => '2024-01-15 00:00:00',
        ]);
        Company::query()->create([
            'id' => 'co_mts',
            'registration_no' => 'MT77821',
            'external_account_no' => 'H0B00002',
            'name' => 'MTS Fruits Sdn. Bhd.',
            'email' => 'hello@mtsfruits.example',
            'phone' => '07-333 2211',
            'address' => '35, Jalan Badik 1, Taman Sri Tebrau, Johor Bahru, Johor',
            'state' => 'Johor',
            'district' => 'Johor Bahru',
            'postcode' => '80050',
            'website' => 'https://mtsfruits.example',
            'logo_path' => '/logos/logo-fama.png',
            'external_source' => 'DAGANGNET',
            'external_status' => 'Aktif',
            'created_at' => '2024-03-02 00:00:00',
            'updated_at' => '2024-03-02 00:00:00',
        ]);

        User::query()->create([
            'id' => 'user_ali',
            'name' => 'Ali bin Abu',
            'email' => 'ali@abcfruits.example',
            'password' => 'Exporter123!',
            'role' => Role::Exporter,
            'identity_reference' => '660113021111',
            'status' => 'ACTIVE',
            'company_id' => 'co_abc',
            'created_at' => '2026-01-10 00:00:00',
            'updated_at' => '2026-01-10 00:00:00',
        ]);
        User::query()->create([
            'id' => 'user_siti',
            'name' => 'Siti Aminah',
            'email' => 'siti@mtsfruits.example',
            'password' => 'Exporter123!',
            'role' => Role::Exporter,
            'identity_reference' => '880214105566',
            'status' => 'ACTIVE',
            'company_id' => 'co_mts',
            'created_at' => '2026-02-01 00:00:00',
            'updated_at' => '2026-02-01 00:00:00',
        ]);
        User::query()->create([
            'id' => 'user_fama',
            'name' => 'Ali bin Abu Ghani',
            'email' => 'aliabu@fama.gov.my',
            'password' => 'Fama123!',
            'role' => Role::FamaOfficer,
            'identity_reference' => '770101145533',
            'status' => 'ACTIVE',
            'company_id' => null,
            'created_at' => '2026-01-05 00:00:00',
            'updated_at' => '2026-01-05 00:00:00',
        ]);

        foreach ([
            ['id' => 'cp_abc_durian', 'company_id' => 'co_abc', 'produce_type_id' => 'pt_durian', 'variety' => 'Musang King', 'active' => true],
            ['id' => 'cp_abc_nangka', 'company_id' => 'co_abc', 'produce_type_id' => 'pt_nangka', 'variety' => null, 'active' => true],
            ['id' => 'cp_abc_manggis', 'company_id' => 'co_abc', 'produce_type_id' => 'pt_manggis', 'variety' => null, 'active' => true],
            ['id' => 'cp_mts_tembikai', 'company_id' => 'co_mts', 'produce_type_id' => 'pt_tembikai', 'variety' => null, 'active' => true],
            ['id' => 'cp_mts_mangga', 'company_id' => 'co_mts', 'produce_type_id' => 'pt_mangga', 'variety' => 'Harumanis', 'active' => true],
        ] as $row) {
            CompanyProduce::query()->create($row);
        }

        foreach ([
            ['id' => 'cert_abc_haccp', 'company_id' => 'co_abc', 'type' => 'HACCP', 'certificate_no' => 'HACCP-ABC-2025-01', 'document_path' => '/placeholders/certificate-haccp.svg', 'issue_date' => '2025-03-01', 'expiry_date' => '2027-03-01', 'status' => 'ACTIVE'],
            ['id' => 'cert_abc_mygap', 'company_id' => 'co_abc', 'type' => 'MyGAP', 'certificate_no' => 'MYGAP-ABC-2025-04', 'document_path' => '/placeholders/certificate-mygap.svg', 'issue_date' => '2025-04-12', 'expiry_date' => '2027-04-12', 'status' => 'ACTIVE'],
            ['id' => 'cert_abc_coc', 'company_id' => 'co_abc', 'type' => 'CoC', 'certificate_no' => 'STB181019EJ100436', 'document_path' => '/placeholders/certificate-coc.svg', 'issue_date' => '2025-06-01', 'expiry_date' => '2026-12-31', 'status' => 'ACTIVE'],
            ['id' => 'cert_abc_fito', 'company_id' => 'co_abc', 'type' => 'FITOSANITASI', 'certificate_no' => 'FITO-ABC-2026-09', 'document_path' => '/placeholders/certificate-fitosanitasi.svg', 'issue_date' => '2026-01-20', 'expiry_date' => '2026-12-31', 'status' => 'ACTIVE'],
            ['id' => 'cert_mts_coc', 'company_id' => 'co_mts', 'type' => 'CoC', 'certificate_no' => 'STB190220EJ200118', 'document_path' => '/placeholders/certificate-coc.svg', 'issue_date' => '2025-08-01', 'expiry_date' => '2026-12-31', 'status' => 'ACTIVE'],
        ] as $row) {
            Certificate::query()->create($row);
        }

        foreach ([
            ['id' => 'gal_abc_kebun', 'company_id' => 'co_abc', 'category' => 'KEBUN', 'description' => 'KEBUN', 'file_path' => '/placeholders/gallery-kebun.svg', 'uploaded_by' => 'user_ali', 'uploaded_at' => '2026-06-03 00:00:00'],
            ['id' => 'gal_abc_lot', 'company_id' => 'co_abc', 'category' => 'LOT_KEBUN', 'description' => 'LOT KEBUN', 'file_path' => '/placeholders/gallery-lot.svg', 'uploaded_by' => 'user_ali', 'uploaded_at' => '2026-06-03 00:00:00'],
            ['id' => 'gal_abc_buah', 'company_id' => 'co_abc', 'category' => 'BUAH', 'description' => 'BUAH', 'file_path' => '/placeholders/gallery-buah.svg', 'uploaded_by' => 'user_ali', 'uploaded_at' => '2026-06-03 00:00:00'],
        ] as $row) {
            GalleryItem::query()->create($row);
        }

        $applications = [
            ['id' => 'app_123', 'application_no' => 'FAMA-2026-000123', 'company_id' => 'co_abc', 'produce_type_id' => 'pt_durian', 'variety' => 'Musang King', 'grade' => 'Premium', 'size' => 'L', 'quantity' => 1000, 'quantity_unit' => 'kg', 'destination_country' => 'China', 'coc_certificate_id' => 'cert_abc_coc', 'coc_number' => 'STB181019EJ100436', 'export_date' => '2026-06-19', 'farm_name' => 'DEF Durian Farm', 'importer_name' => 'XYZ Fruits Sdn. Bhd.', 'importer_address' => 'No. 1, Haidian District, Beijing, China', 'status' => ApplicationStatus::UnderReview, 'submitted_at' => '2026-04-20 00:00:00', 'reviewed_at' => null, 'created_at' => '2026-04-18 00:00:00', 'updated_at' => '2026-04-20 00:00:00'],
            ['id' => 'app_109', 'application_no' => 'FAMA-2026-000109', 'company_id' => 'co_mts', 'produce_type_id' => 'pt_tembikai', 'variety' => 'Red Globe', 'grade' => 'A', 'size' => 'L', 'quantity' => 900, 'quantity_unit' => 'kg', 'destination_country' => 'Singapura', 'coc_certificate_id' => 'cert_mts_coc', 'coc_number' => 'STB190220EJ200118', 'export_date' => '2026-03-17', 'farm_name' => 'MTS Orchard', 'importer_name' => 'Harbour Fresh Pte Ltd', 'importer_address' => '12 Pasir Panjang Road, Singapore', 'status' => ApplicationStatus::Approved, 'submitted_at' => '2026-03-10 00:00:00', 'reviewed_at' => '2026-03-12 00:00:00', 'created_at' => '2026-03-08 00:00:00', 'updated_at' => '2026-03-12 00:00:00'],
            ['id' => 'app_015', 'application_no' => 'FAMA-2026-000015', 'company_id' => 'co_abc', 'produce_type_id' => 'pt_mangga', 'variety' => 'Harumanis', 'grade' => 'A', 'size' => 'M', 'quantity' => 400, 'quantity_unit' => 'kg', 'destination_country' => 'Jepun', 'coc_certificate_id' => 'cert_abc_coc', 'coc_number' => 'STB181019EJ100436', 'export_date' => '2026-03-17', 'farm_name' => 'Ladang Harumanis Perlis', 'importer_name' => 'Tokyo Fresh Co.', 'importer_address' => '2-1 Chiyoda, Tokyo, Japan', 'status' => ApplicationStatus::Approved, 'submitted_at' => '2026-03-05 00:00:00', 'reviewed_at' => '2026-03-08 00:00:00', 'created_at' => '2026-03-04 00:00:00', 'updated_at' => '2026-03-08 00:00:00'],
            ['id' => 'app_011', 'application_no' => 'FAMA-2026-000011', 'company_id' => 'co_abc', 'produce_type_id' => 'pt_mangga', 'variety' => 'Chokanan', 'grade' => 'B', 'size' => 'S', 'quantity' => 220, 'quantity_unit' => 'kg', 'destination_country' => 'China', 'coc_certificate_id' => 'cert_abc_coc', 'coc_number' => 'STB181019EJ100436', 'export_date' => '2026-03-15', 'farm_name' => 'Ladang Harumanis Perlis', 'importer_name' => 'XYZ Fruits Sdn. Bhd.', 'importer_address' => 'No. 1, Haidian District, Beijing, China', 'status' => ApplicationStatus::Rejected, 'submitted_at' => '2026-03-02 00:00:00', 'reviewed_at' => '2026-03-04 00:00:00', 'created_at' => '2026-03-01 00:00:00', 'updated_at' => '2026-03-04 00:00:00'],
            ['id' => 'app_124', 'application_no' => 'FAMA-2026-000124', 'company_id' => 'co_abc', 'produce_type_id' => 'pt_nangka', 'variety' => 'Tekam Yellow', 'grade' => 'Premium', 'size' => 'L', 'quantity' => 550, 'quantity_unit' => 'kg', 'destination_country' => 'Emiriah Arab Bersatu', 'coc_certificate_id' => 'cert_abc_coc', 'coc_number' => 'STB181019EJ100436', 'export_date' => '2026-07-01', 'farm_name' => 'Ladang Nangka Pahang', 'importer_name' => 'Gulf Produce LLC', 'importer_address' => 'Dubai Investment Park, UAE', 'status' => ApplicationStatus::Draft, 'submitted_at' => null, 'reviewed_at' => null, 'created_at' => '2026-05-01 00:00:00', 'updated_at' => '2026-05-01 00:00:00'],
        ];
        foreach ($applications as $row) {
            ExportApplication::query()->create($row);
        }

        foreach ([
            ['id' => 'qr_123', 'qr_code' => 'GPL-QR-000123', 'application_id' => 'app_123', 'public_slug' => 'GPL-QR-000123', 'status' => QrStatus::GeneratedInactive, 'generated_at' => '2026-04-18 00:00:00', 'activated_at' => null],
            ['id' => 'qr_109', 'qr_code' => 'GPL-QR-000109', 'application_id' => 'app_109', 'public_slug' => 'GPL-QR-000109', 'status' => QrStatus::Active, 'generated_at' => '2026-03-08 00:00:00', 'activated_at' => '2026-03-12 00:00:00'],
            ['id' => 'qr_015', 'qr_code' => 'GPL-QR-000015', 'application_id' => 'app_015', 'public_slug' => 'GPL-QR-000015', 'status' => QrStatus::Active, 'generated_at' => '2026-03-04 00:00:00', 'activated_at' => '2026-03-08 00:00:00'],
            ['id' => 'qr_011', 'qr_code' => 'GPL-QR-000011', 'application_id' => 'app_011', 'public_slug' => 'GPL-QR-000011', 'status' => QrStatus::GeneratedInactive, 'generated_at' => '2026-03-01 00:00:00', 'activated_at' => null],
        ] as $row) {
            QrCode::query()->create($row);
        }

        foreach ([
            ['id' => 'appr_109', 'application_id' => 'app_109', 'officer_user_id' => 'user_fama', 'decision' => 'APPROVED', 'remarks' => 'Maklumat lengkap dan sijil sah.', 'decided_at' => '2026-03-12 00:00:00'],
            ['id' => 'appr_015', 'application_id' => 'app_015', 'officer_user_id' => 'user_fama', 'decision' => 'APPROVED', 'remarks' => 'Diluluskan.', 'decided_at' => '2026-03-08 00:00:00'],
            ['id' => 'appr_011', 'application_id' => 'app_011', 'officer_user_id' => 'user_fama', 'decision' => 'REJECTED', 'remarks' => 'Maklumat gred tidak konsisten dengan sijil CoC.', 'decided_at' => '2026-03-04 00:00:00'],
        ] as $row) {
            Approval::query()->create($row);
        }

        AuditLog::query()->create([
            'id' => 'audit_1',
            'actor_user_id' => 'user_ali',
            'actor_role' => 'EXPORTER',
            'action' => 'APPLICATION_SUBMITTED',
            'object_type' => 'ExportApplication',
            'object_id' => 'app_123',
            'before_json' => json_encode(['status' => 'DRAFT']),
            'after_json' => json_encode(['status' => 'SUBMITTED']),
            'remarks' => null,
            'created_at' => '2026-04-20 00:00:00',
        ]);
        AuditLog::query()->create([
            'id' => 'audit_2',
            'actor_user_id' => 'user_fama',
            'actor_role' => 'FAMA_OFFICER',
            'action' => 'APPLICATION_APPROVED',
            'object_type' => 'ExportApplication',
            'object_id' => 'app_109',
            'before_json' => json_encode(['status' => 'UNDER_REVIEW']),
            'after_json' => json_encode(['status' => 'APPROVED']),
            'remarks' => 'Maklumat lengkap dan sijil sah.',
            'created_at' => '2026-03-12 00:00:00',
        ]);

        AppNotification::query()->create([
            'id' => 'nt_1',
            'user_id' => 'user_fama',
            'title' => 'Permohonan menunggu semakan',
            'body' => 'FAMA-2026-000123 Durian Musang King menunggu pengesahan.',
            'read' => false,
            'created_at' => '2026-04-20 00:00:00',
        ]);
        AppNotification::query()->create([
            'id' => 'nt_2',
            'user_id' => 'user_ali',
            'title' => 'Permohonan ditolak',
            'body' => 'FAMA-2026-000011 telah ditolak.',
            'read' => false,
            'created_at' => '2026-03-04 00:00:00',
        ]);
        AppNotification::query()->create([
            'id' => 'nt_3',
            'user_id' => 'user_siti',
            'title' => 'QR diaktifkan',
            'body' => 'GPL-QR-000109 kini aktif.',
            'read' => true,
            'created_at' => '2026-03-12 00:00:00',
        ]);
    }
}
