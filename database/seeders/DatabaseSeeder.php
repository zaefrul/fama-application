<?php

namespace Database\Seeders;

use App\Domain\ApplicationStatus;
use App\Domain\LotDisposition;
use App\Domain\PartyType;
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
            ['id' => 'pt_mangga', 'name' => 'Mangga', 'category' => 'PRODUCE'],
        ];
        foreach ($produce as $row) {
            ProduceType::query()->create($row);
        }
        foreach ([
            ['id' => 'pt_lembu', 'name' => 'Lembu'],
            ['id' => 'pt_kambing', 'name' => 'Kambing'],
            ['id' => 'pt_ayam', 'name' => 'Ayam'],
            ['id' => 'pt_kerbau', 'name' => 'Kerbau'],
        ] as $row) {
            ProduceType::query()->firstOrCreate(
                ['id' => $row['id']],
                ['name' => $row['name'], 'category' => 'LIVESTOCK'],
            );
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
        Company::query()->firstOrCreate(
            ['external_account_no' => 'H0B00003'],
            [
                'id' => 'co_demo_utara',
                'registration_no' => 'DU30003',
                'name' => 'Ladang Demo Utara Sdn. Bhd.',
                'email' => 'demo.utara@example.com',
                'phone' => '04-111 2233',
                'address' => 'Lot 12, Jalan Ladang, Alor Setar, Kedah',
                'state' => 'Kedah',
                'district' => 'Kota Setar',
                'postcode' => '05000',
                'website' => 'https://demoutara.example',
                'logo_path' => '/logos/logo-fama.png',
                'external_source' => 'DAGANGNET',
                'external_status' => 'Aktif',
            ],
        );
        Company::query()->firstOrCreate(
            ['external_account_no' => 'H0B00004'],
            [
                'id' => 'co_demo_selatan',
                'registration_no' => 'SF40004',
                'name' => 'Selatan Fresh Sdn. Bhd.',
                'email' => 'selatan.fresh@example.com',
                'phone' => '07-444 5566',
                'address' => '88, Jalan Segget, Johor Bahru, Johor',
                'state' => 'Johor',
                'district' => 'Johor Bahru',
                'postcode' => '80000',
                'website' => 'https://selatanfresh.example',
                'logo_path' => '/logos/logo-fama.png',
                'external_source' => 'DAGANGNET',
                'external_status' => 'Aktif',
            ],
        );

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

        User::query()->findOrFail('user_ali')->companies()->sync(['co_abc']);
        User::query()->findOrFail('user_siti')->companies()->sync(['co_mts']);

        foreach ([
            ['id' => 'cp_abc_durian', 'company_id' => 'co_abc', 'produce_type_id' => 'pt_durian', 'variety' => 'Musang King', 'active' => true],
            ['id' => 'cp_abc_nangka', 'company_id' => 'co_abc', 'produce_type_id' => 'pt_nangka', 'variety' => null, 'active' => true],
            ['id' => 'cp_abc_manggis', 'company_id' => 'co_abc', 'produce_type_id' => 'pt_manggis', 'variety' => null, 'active' => true],
            ['id' => 'cp_mts_tembikai', 'company_id' => 'co_mts', 'produce_type_id' => 'pt_tembikai', 'variety' => null, 'active' => true],
            ['id' => 'cp_mts_mangga', 'company_id' => 'co_mts', 'produce_type_id' => 'pt_mangga', 'variety' => 'Harumanis', 'active' => true],
        ] as $row) {
            CompanyProduce::query()->create($row);
        }

        $this->call(CertificateDemoSeeder::class);

        foreach ([
            ['id' => 'gal_abc_kebun', 'company_id' => 'co_abc', 'category' => 'KEBUN', 'description' => 'KEBUN', 'file_path' => '/farms/farm-demo-01.jpg', 'uploaded_by' => 'user_ali', 'uploaded_at' => '2026-06-03 00:00:00'],
            ['id' => 'gal_abc_lot', 'company_id' => 'co_abc', 'category' => 'LOT_KEBUN', 'description' => 'LOT KEBUN', 'file_path' => '/farms/farm-lot-demo-01.jpg', 'uploaded_by' => 'user_ali', 'uploaded_at' => '2026-06-03 00:00:00'],
            ['id' => 'gal_abc_buah', 'company_id' => 'co_abc', 'category' => 'BUAH', 'description' => 'BUAH', 'file_path' => '/products/produce-durian-demo-01.jpg', 'uploaded_by' => 'user_ali', 'uploaded_at' => '2026-06-03 00:00:00'],
            ['id' => 'gal_mts_buah', 'company_id' => 'co_mts', 'category' => 'BUAH', 'description' => 'BUAH', 'file_path' => '/products/produce-tembikai-demo-01.jpg', 'uploaded_by' => 'user_siti', 'uploaded_at' => '2026-06-03 00:00:00'],
        ] as $row) {
            GalleryItem::query()->create($row);
        }

        $applications = [
            ['id' => 'app_123', 'application_no' => 'FAMA-2026-000123', 'company_id' => 'co_abc', 'produce_type_id' => 'pt_durian', 'variety' => 'Musang King', 'grade' => 'Premium', 'size' => 'L', 'quantity' => 1000, 'quantity_unit' => 'kg', 'destination_country' => 'China', 'coc_certificate_id' => 'cert_abc_coc', 'coc_number' => 'STB181019EJ100436', 'export_date' => '2026-06-19', 'lot_no' => 'LOT-A12', 'farm_location' => 'Raub, Pahang', 'farm_lat' => 3.7930000, 'farm_lng' => 101.8570000, 'display_image_path' => '/products/produce-durian-demo-01.jpg', 'farm_name' => 'DEF Durian Farm', 'importer_name' => 'XYZ Fruits Sdn. Bhd.', 'importer_address' => 'No. 1, Haidian District, Beijing, China', 'status' => ApplicationStatus::UnderReview, 'submitted_at' => '2026-04-20 00:00:00', 'reviewed_at' => null, 'created_at' => '2026-04-18 00:00:00', 'updated_at' => '2026-04-20 00:00:00'],
            ['id' => 'app_109', 'application_no' => 'FAMA-2026-000109', 'company_id' => 'co_mts', 'produce_type_id' => 'pt_tembikai', 'variety' => 'Red Globe', 'grade' => 'A', 'size' => 'L', 'quantity' => 900, 'quantity_unit' => 'kg', 'destination_country' => 'Singapura', 'coc_certificate_id' => 'cert_mts_coc', 'coc_number' => 'STB190220EJ200118', 'export_date' => '2026-03-17', 'lot_no' => 'LOT-B07', 'farm_location' => 'Kuala Selangor, Selangor', 'farm_lat' => 3.3400000, 'farm_lng' => 101.2500000, 'display_image_path' => '/products/produce-tembikai-demo-01.jpg', 'farm_name' => 'MTS Orchard', 'importer_name' => 'Harbour Fresh Pte Ltd', 'importer_address' => '12 Pasir Panjang Road, Singapore', 'status' => ApplicationStatus::Approved, 'submitted_at' => '2026-03-10 00:00:00', 'reviewed_at' => '2026-03-12 00:00:00', 'created_at' => '2026-03-08 00:00:00', 'updated_at' => '2026-03-12 00:00:00'],
            ['id' => 'app_015', 'application_no' => 'FAMA-2026-000015', 'company_id' => 'co_abc', 'produce_type_id' => 'pt_mangga', 'variety' => 'Harumanis', 'grade' => 'A', 'size' => 'M', 'quantity' => 400, 'quantity_unit' => 'kg', 'destination_country' => 'Jepun', 'coc_certificate_id' => 'cert_abc_coc', 'coc_number' => 'STB181019EJ100436', 'export_date' => '2026-03-17', 'lot_no' => 'LOT-H03', 'farm_location' => 'Pauh, Perlis', 'farm_lat' => 6.4410000, 'farm_lng' => 100.1980000, 'display_image_path' => '/products/produce-mangga-demo-01.jpg', 'farm_name' => 'Ladang Harumanis Perlis', 'importer_name' => 'Tokyo Fresh Co.', 'importer_address' => '2-1 Chiyoda, Tokyo, Japan', 'status' => ApplicationStatus::Approved, 'submitted_at' => '2026-03-05 00:00:00', 'reviewed_at' => '2026-03-08 00:00:00', 'created_at' => '2026-03-04 00:00:00', 'updated_at' => '2026-03-08 00:00:00'],
            ['id' => 'app_011', 'application_no' => 'FAMA-2026-000011', 'company_id' => 'co_abc', 'produce_type_id' => 'pt_mangga', 'variety' => 'Chokanan', 'grade' => 'B', 'size' => 'S', 'quantity' => 220, 'quantity_unit' => 'kg', 'destination_country' => 'China', 'coc_certificate_id' => 'cert_abc_coc', 'coc_number' => 'STB181019EJ100436', 'export_date' => '2026-03-15', 'lot_no' => 'LOT-H03', 'farm_location' => 'Pauh, Perlis', 'farm_lat' => 6.4410000, 'farm_lng' => 100.1980000, 'display_image_path' => '/products/produce-mangga-demo-01.jpg', 'farm_name' => 'Ladang Harumanis Perlis', 'importer_name' => 'XYZ Fruits Sdn. Bhd.', 'importer_address' => 'No. 1, Haidian District, Beijing, China', 'status' => ApplicationStatus::Rejected, 'submitted_at' => '2026-03-02 00:00:00', 'reviewed_at' => '2026-03-04 00:00:00', 'created_at' => '2026-03-01 00:00:00', 'updated_at' => '2026-03-04 00:00:00'],
            ['id' => 'app_124', 'application_no' => 'FAMA-2026-000124', 'company_id' => 'co_abc', 'produce_type_id' => 'pt_nangka', 'variety' => 'Tekam Yellow', 'grade' => 'Premium', 'size' => 'L', 'quantity' => 550, 'quantity_unit' => 'kg', 'destination_country' => 'Emiriah Arab Bersatu', 'coc_certificate_id' => 'cert_abc_coc', 'coc_number' => 'STB181019EJ100436', 'export_date' => null, 'lot_no' => 'LOT-N21', 'farm_location' => 'Temerloh, Pahang', 'farm_lat' => 3.4500000, 'farm_lng' => 102.4160000, 'display_image_path' => '/products/produce-nangka-demo-01.jpg', 'farm_name' => 'Ladang Nangka Pahang', 'importer_name' => 'Gulf Produce LLC', 'importer_address' => 'Dubai Investment Park, UAE', 'status' => ApplicationStatus::Draft, 'submitted_at' => null, 'reviewed_at' => null, 'created_at' => '2026-05-01 00:00:00', 'updated_at' => '2026-05-01 00:00:00'],
        ];
        foreach ($applications as $row) {
            ExportApplication::query()->create($row);
        }

        foreach ([
            ['id' => 'qr_123', 'qr_code' => 'GPL-QR-000123', 'application_id' => 'app_123', 'root_application_id' => 'app_123', 'holder_company_id' => 'co_abc', 'quantity' => 1000, 'quantity_remaining' => 1000, 'disposition' => LotDisposition::Holding, 'public_slug' => 'GPL-QR-000123', 'status' => QrStatus::GeneratedInactive, 'generated_at' => '2026-04-18 00:00:00', 'activated_at' => null],
            ['id' => 'qr_109', 'qr_code' => 'GPL-QR-000109', 'application_id' => 'app_109', 'root_application_id' => 'app_109', 'holder_company_id' => 'co_mts', 'quantity' => 900, 'quantity_remaining' => 900, 'disposition' => LotDisposition::Holding, 'public_slug' => 'GPL-QR-000109', 'status' => QrStatus::Active, 'generated_at' => '2026-03-08 00:00:00', 'activated_at' => '2026-03-12 00:00:00'],
            ['id' => 'qr_015', 'qr_code' => 'GPL-QR-000015', 'application_id' => 'app_015', 'root_application_id' => 'app_015', 'holder_company_id' => 'co_abc', 'quantity' => 400, 'quantity_remaining' => 400, 'disposition' => LotDisposition::Holding, 'public_slug' => 'GPL-QR-000015', 'status' => QrStatus::Active, 'generated_at' => '2026-03-04 00:00:00', 'activated_at' => '2026-03-08 00:00:00'],
            ['id' => 'qr_011', 'qr_code' => 'GPL-QR-000011', 'application_id' => 'app_011', 'root_application_id' => 'app_011', 'holder_company_id' => 'co_abc', 'quantity' => 220, 'quantity_remaining' => 220, 'disposition' => LotDisposition::Holding, 'public_slug' => 'GPL-QR-000011', 'status' => QrStatus::GeneratedInactive, 'generated_at' => '2026-03-01 00:00:00', 'activated_at' => null],
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

        $this->seedBananaLots();
        $this->seedContohAccounts();
        $this->seedContohSample();
        $this->call(PresentationJourneySeeder::class);
        $this->call(ProduceNutritionSeeder::class);

        $this->call(QrAccessSeeder::class);
    }

    private function seedBananaLots(): void
    {
        $companies = [
            [
                'id' => 'co_supplier_pisang',
                'registration_no' => 'PS11011',
                'external_account_no' => 'H0B00011',
                'name' => 'Ladang Pisang Pontian',
                'email' => 'supplier.pisang@example.com',
                'phone' => '07-111 2200',
                'address' => 'Lot 8, Jalan Pisang, Pontian, Johor',
                'state' => 'Johor',
                'district' => 'Pontian',
                'postcode' => '82000',
                'website' => 'https://pisangpontian.example',
                'logo_path' => '/logos/logo-fama.png',
                'external_source' => 'DAGANGNET',
                'external_status' => 'Aktif',
                'party_type' => PartyType::Supplier,
            ],
            [
                'id' => 'co_exporter_pisang',
                'registration_no' => 'EP12012',
                'external_account_no' => 'H0B00012',
                'name' => 'Eksport Pisang Sdn. Bhd.',
                'email' => 'eksport.pisang@example.com',
                'phone' => '07-222 3300',
                'address' => '12, Jalan Pelabuhan, Pasir Gudang, Johor',
                'state' => 'Johor',
                'district' => 'Pasir Gudang',
                'postcode' => '81700',
                'website' => 'https://eksportpisang.example',
                'logo_path' => '/logos/logo-fama.png',
                'external_source' => 'DAGANGNET',
                'external_status' => 'Aktif',
                'party_type' => PartyType::Exporter,
            ],
            [
                'id' => 'co_marketer_pisang',
                'registration_no' => 'MP13013',
                'external_account_no' => 'H0B00013',
                'name' => 'Pemasar Buah Sdn. Bhd.',
                'email' => 'pemasar.pisang@example.com',
                'phone' => '03-555 4400',
                'address' => '40, Jalan Tun Razak, Kuala Lumpur',
                'state' => 'Kuala Lumpur',
                'district' => 'Kuala Lumpur',
                'postcode' => '50400',
                'website' => 'https://pemasarbuah.example',
                'logo_path' => '/logos/logo-fama.png',
                'external_source' => 'DAGANGNET',
                'external_status' => 'Aktif',
                'party_type' => PartyType::Marketer,
            ],
            [
                'id' => 'co_retailer_pisang',
                'registration_no' => 'RP14014',
                'external_account_no' => 'H0B00014',
                'name' => 'Runcit Segar Sdn. Bhd.',
                'email' => 'peruncit.pisang@example.com',
                'phone' => '03-666 5500',
                'address' => '8, Jalan Imbi, Kuala Lumpur',
                'state' => 'Kuala Lumpur',
                'district' => 'Kuala Lumpur',
                'postcode' => '55100',
                'website' => 'https://runcitsegar.example',
                'logo_path' => '/logos/logo-fama.png',
                'external_source' => 'DAGANGNET',
                'external_status' => 'Aktif',
                'party_type' => PartyType::Retailer,
            ],
        ];
        foreach ($companies as $row) {
            Company::query()->create($row);
        }

        $users = [
            ['id' => 'user_supplier_pisang', 'name' => 'Hassan Pisang', 'email' => 'supplier.pisang@example.com', 'company_id' => 'co_supplier_pisang', 'identity_reference' => '700101011111'],
            ['id' => 'user_eksport_pisang', 'name' => 'Nora Eksport', 'email' => 'eksport.pisang@example.com', 'company_id' => 'co_exporter_pisang', 'identity_reference' => '800202022222'],
            ['id' => 'user_pemasar_pisang', 'name' => 'Daniel Pemasar', 'email' => 'pemasar.pisang@example.com', 'company_id' => 'co_marketer_pisang', 'identity_reference' => '810303033333'],
            ['id' => 'user_peruncit_pisang', 'name' => 'Aina Runcit', 'email' => 'peruncit.pisang@example.com', 'company_id' => 'co_retailer_pisang', 'identity_reference' => '820404044444'],
        ];
        foreach ($users as $row) {
            $user = User::query()->create([
                ...$row,
                'password' => 'Exporter123!',
                'role' => Role::Exporter,
                'status' => 'ACTIVE',
            ]);
            $user->companies()->sync([$row['company_id']]);
        }

        ExportApplication::query()->create([
            'id' => 'app_pisang',
            'application_no' => 'FAMA-2026-000201',
            'company_id' => 'co_supplier_pisang',
            'produce_type_id' => 'pt_pisang',
            'variety' => 'Berangan',
            'grade' => 'A',
            'size' => 'L',
            'quantity' => 1000,
            'quantity_unit' => 'kg',
            'destination_country' => 'Singapura',
            'coc_certificate_id' => null,
            'coc_number' => 'STB-PISANG-DEMO',
            'export_date' => '2026-09-01',
            'lot_no' => 'LOT-PISANG-01',
            'farm_location' => 'Pontian, Johor',
            'farm_lat' => 1.4860000,
            'farm_lng' => 103.3890000,
            'display_image_path' => null,
            'farm_name' => 'Ladang Pisang Pontian',
            'importer_name' => 'Harbour Fresh Pte Ltd',
            'importer_address' => '12 Pasir Panjang Road, Singapore',
            'status' => ApplicationStatus::Approved,
            'submitted_at' => '2026-08-20 00:00:00',
            'reviewed_at' => '2026-08-22 00:00:00',
            'created_at' => '2026-08-18 00:00:00',
            'updated_at' => '2026-08-22 00:00:00',
        ]);

        foreach ([
            ['id' => 'qr_pisang_root', 'qr_code' => 'GPL-QR-000201', 'application_id' => 'app_pisang', 'parent_id' => null, 'holder_company_id' => 'co_supplier_pisang', 'quantity' => 1000, 'quantity_remaining' => 0],
            ['id' => 'qr_pisang_exp_400', 'qr_code' => 'GPL-QR-000202', 'application_id' => null, 'parent_id' => 'qr_pisang_root', 'holder_company_id' => 'co_exporter_pisang', 'quantity' => 400, 'quantity_remaining' => 150],
            ['id' => 'qr_pisang_exp_600', 'qr_code' => 'GPL-QR-000203', 'application_id' => null, 'parent_id' => 'qr_pisang_root', 'holder_company_id' => 'co_exporter_pisang', 'quantity' => 600, 'quantity_remaining' => 600],
            ['id' => 'qr_pisang_mkt_250', 'qr_code' => 'GPL-QR-000204', 'application_id' => null, 'parent_id' => 'qr_pisang_exp_400', 'holder_company_id' => 'co_marketer_pisang', 'quantity' => 250, 'quantity_remaining' => 250],
        ] as $row) {
            QrCode::query()->create([
                ...$row,
                'root_application_id' => 'app_pisang',
                'disposition' => LotDisposition::Holding,
                'public_slug' => $row['qr_code'],
                'status' => QrStatus::Active,
                'generated_at' => '2026-08-22 00:00:00',
                'activated_at' => '2026-08-22 00:00:00',
            ]);
        }

        Approval::query()->create([
            'id' => 'appr_pisang',
            'application_id' => 'app_pisang',
            'officer_user_id' => 'user_fama',
            'decision' => 'APPROVED',
            'remarks' => 'Lot pisang diluluskan untuk dipecahkan.',
            'decided_at' => '2026-08-22 00:00:00',
        ]);
    }

    /**
     * Fictional companies for demonstrations. Real business names are not used.
     */
    public function seedContohAccounts(): void
    {
        $companies = [
            [
                'id' => 'co_contoh_pengeluar',
                'registration_no' => 'CH21021',
                'external_account_no' => 'H0B00021',
                'name' => 'Ladang Contoh Hijau Sdn. Bhd.',
                'email' => 'ladang.contoh@example.com',
                'phone' => '09-511 2100',
                'address' => 'Lot 21, Jalan Contoh, Kuantan, Pahang',
                'state' => 'Pahang',
                'district' => 'Kuantan',
                'postcode' => '25000',
                'website' => 'https://ladangcontoh.example',
                'logo_path' => '/logos/contoh-pengeluar.svg',
                'party_type' => PartyType::Supplier,
            ],
            [
                'id' => 'co_contoh_pengeksport',
                'registration_no' => 'NE22022',
                'external_account_no' => 'H0B00022',
                'name' => 'Niaga Contoh Eksport Sdn. Bhd.',
                'email' => 'eksport.contoh@example.com',
                'phone' => '07-522 2200',
                'address' => '22, Jalan Contoh, Pasir Gudang, Johor',
                'state' => 'Johor',
                'district' => 'Pasir Gudang',
                'postcode' => '81700',
                'website' => 'https://eksportcontoh.example',
                'logo_path' => '/logos/contoh-pengeksport.svg',
                'party_type' => PartyType::Exporter,
            ],
            [
                'id' => 'co_contoh_pemborong',
                'registration_no' => 'BS23023',
                'external_account_no' => 'H0B00023',
                'name' => 'Borong Contoh Segar Sdn. Bhd.',
                'email' => 'borong.contoh@example.com',
                'phone' => '03-533 2300',
                'address' => '23, Jalan Contoh, Kuala Lumpur',
                'state' => 'Kuala Lumpur',
                'district' => 'Kuala Lumpur',
                'postcode' => '50400',
                'website' => 'https://borongcontoh.example',
                'logo_path' => '/logos/contoh-pemborong.svg',
                'party_type' => PartyType::Marketer,
            ],
            [
                'id' => 'co_contoh_pengimport',
                'registration_no' => 'IN24024',
                'external_account_no' => 'H0B00024',
                'name' => 'Import Contoh Niaga Sdn. Bhd.',
                'email' => 'import.contoh@example.com',
                'phone' => '03-544 2400',
                'address' => '24, Jalan Contoh, Petaling Jaya, Selangor',
                'state' => 'Selangor',
                'district' => 'Petaling',
                'postcode' => '46200',
                'website' => 'https://importcontoh.example',
                'logo_path' => '/logos/contoh-pengimport.svg',
                'party_type' => PartyType::Importer,
            ],
            [
                'id' => 'co_contoh_peruncit',
                'registration_no' => 'KR25025',
                'external_account_no' => 'H0B00025',
                'name' => 'Kedai Contoh Runcit Sdn. Bhd.',
                'email' => 'runcit.contoh@example.com',
                'phone' => '03-555 2500',
                'address' => '25, Jalan Contoh, Kuala Lumpur',
                'state' => 'Kuala Lumpur',
                'district' => 'Kuala Lumpur',
                'postcode' => '55100',
                'website' => 'https://runcitcontoh.example',
                'logo_path' => '/logos/contoh-peruncit.svg',
                'party_type' => PartyType::Retailer,
            ],
        ];
        foreach ($companies as $row) {
            Company::query()->firstOrCreate(
                ['id' => $row['id']],
                $row + [
                    'external_source' => 'DAGANGNET',
                    'external_status' => 'Aktif',
                ],
            );
        }

        $users = [
            ['id' => 'user_contoh_pengeluar', 'name' => 'Aminah Contoh', 'email' => 'aminah.contoh@example.com', 'company_id' => 'co_contoh_pengeluar', 'identity_reference' => '900101015551'],
            ['id' => 'user_contoh_pengeksport', 'name' => 'Farid Contoh', 'email' => 'farid.contoh@example.com', 'company_id' => 'co_contoh_pengeksport', 'identity_reference' => '900101015552'],
            ['id' => 'user_contoh_pemborong', 'name' => 'Lina Contoh', 'email' => 'lina.contoh@example.com', 'company_id' => 'co_contoh_pemborong', 'identity_reference' => '900101015553'],
            ['id' => 'user_contoh_pengimport', 'name' => 'Hafiz Contoh', 'email' => 'hafiz.contoh@example.com', 'company_id' => 'co_contoh_pengimport', 'identity_reference' => '900101015554'],
            ['id' => 'user_contoh_peruncit', 'name' => 'Suri Contoh', 'email' => 'suri.contoh@example.com', 'company_id' => 'co_contoh_peruncit', 'identity_reference' => '900101015555'],
        ];
        foreach ($users as $row) {
            $user = User::query()->firstOrCreate(
                ['id' => $row['id']],
                [
                    ...$row,
                    'password' => 'Exporter123!',
                    'role' => Role::Exporter,
                    'status' => 'ACTIVE',
                ],
            );
            $user->companies()->sync([$row['company_id']]);
        }
    }

    /**
     * One nangka lot moving through the Contoh companies, with logos and sijil marks.
     */
    public function seedContohSample(): void
    {
        $logos = [
            'co_contoh_pengeluar' => '/logos/contoh-pengeluar.svg',
            'co_contoh_pengeksport' => '/logos/contoh-pengeksport.svg',
            'co_contoh_pemborong' => '/logos/contoh-pemborong.svg',
            'co_contoh_pengimport' => '/logos/contoh-pengimport.svg',
            'co_contoh_peruncit' => '/logos/contoh-peruncit.svg',
        ];
        foreach ($logos as $id => $path) {
            Company::query()->whereKey($id)->update(['logo_path' => $path]);
        }

        foreach ([
            ['id' => 'cp_contoh_nangka', 'produce_type_id' => 'pt_nangka', 'variety' => 'Tekam'],
            ['id' => 'cp_contoh_tembikai', 'produce_type_id' => 'pt_tembikai', 'variety' => 'Black Beauty'],
            ['id' => 'cp_contoh_mangga', 'produce_type_id' => 'pt_mangga', 'variety' => 'Harumanis'],
        ] as $row) {
            CompanyProduce::query()->firstOrCreate(
                ['id' => $row['id']],
                $row + ['company_id' => 'co_contoh_pengeluar', 'active' => true],
            );
        }

        foreach ([
            ['id' => 'cert_contoh_mygap', 'company_id' => 'co_contoh_pengeluar', 'type' => 'MyGAP', 'certificate_no' => 'MYGAP-CONTOH-2026-01', 'document_path' => '/certificates/sijil-mygap-demo.svg', 'issue_date' => '2026-01-15', 'expiry_date' => '2028-01-15'],
            ['id' => 'cert_contoh_coc', 'company_id' => 'co_contoh_pengeluar', 'type' => 'CoC', 'certificate_no' => 'COC-CONTOH-2026-01', 'document_path' => '/certificates/sijil-coc-demo.svg', 'issue_date' => '2026-02-01', 'expiry_date' => '2027-02-01'],
            ['id' => 'cert_contoh_haccp', 'company_id' => 'co_contoh_pengeksport', 'type' => 'HACCP', 'certificate_no' => 'HACCP-CONTOH-2026-02', 'document_path' => '/certificates/sijil-haccp-demo.svg', 'issue_date' => '2026-03-01', 'expiry_date' => '2028-03-01'],
            ['id' => 'cert_contoh_halal', 'company_id' => 'co_contoh_pemborong', 'type' => 'HALAL', 'certificate_no' => 'HALAL-CONTOH-2026-03', 'document_path' => '/certificates/sijil-halal-demo.svg', 'issue_date' => '2026-04-01', 'expiry_date' => '2028-04-01'],
            ['id' => 'cert_contoh_iso', 'company_id' => 'co_contoh_pengimport', 'type' => 'ISO 22000', 'certificate_no' => 'ISO-CONTOH-2026-04', 'document_path' => '/certificates/sijil-iso22000-demo.svg', 'issue_date' => '2026-05-01', 'expiry_date' => '2028-05-01'],
            ['id' => 'cert_contoh_fito', 'company_id' => 'co_contoh_peruncit', 'type' => 'FITOSANITASI', 'certificate_no' => 'FITO-CONTOH-2026-05', 'document_path' => '/certificates/sijil-fitosanitasi-demo.jpg', 'issue_date' => '2026-06-01', 'expiry_date' => '2026-12-31'],
        ] as $row) {
            Certificate::query()->firstOrCreate(
                ['id' => $row['id']],
                $row + ['status' => 'ACTIVE'],
            );
        }

        GalleryItem::query()->firstOrCreate(
            ['id' => 'gal_contoh_buah'],
            [
                'company_id' => 'co_contoh_pengeluar',
                'category' => 'BUAH',
                'description' => 'BUAH',
                'file_path' => '/products/produce-nangka-demo-01.jpg',
                'uploaded_by' => 'user_contoh_pengeluar',
                'uploaded_at' => '2026-09-08 00:00:00',
            ],
        );

        ExportApplication::query()->firstOrCreate(
            ['id' => 'app_contoh_nangka'],
            [
                'application_no' => 'FAMA-2026-000901',
                'company_id' => 'co_contoh_pengeluar',
                'produce_type_id' => 'pt_nangka',
                'variety' => 'Tekam',
                'grade' => 'A',
                'size' => 'L',
                'quantity' => 1000,
                'quantity_unit' => 'kg',
                'destination_country' => 'Singapura',
                'coc_certificate_id' => 'cert_contoh_coc',
                'coc_number' => 'COC-CONTOH-2026-01',
                'export_date' => '2026-09-20',
                'lot_no' => 'LOT-CONTOH-01',
                'farm_location' => 'Kuantan, Pahang',
                'farm_lat' => 3.8160000,
                'farm_lng' => 103.3320000,
                'display_image_path' => '/products/produce-nangka-demo-01.jpg',
                'farm_name' => 'Ladang Contoh Hijau',
                'importer_name' => 'Import Contoh Niaga Sdn. Bhd.',
                'importer_address' => '24, Jalan Contoh, Petaling Jaya, Selangor',
                'status' => ApplicationStatus::Approved,
                'submitted_at' => '2026-09-05 00:00:00',
                'reviewed_at' => '2026-09-08 00:00:00',
            ],
        );

        Approval::query()->firstOrCreate(
            ['id' => 'appr_contoh_nangka'],
            [
                'application_id' => 'app_contoh_nangka',
                'officer_user_id' => 'user_fama',
                'decision' => 'APPROVED',
                'remarks' => 'Lot contoh diluluskan untuk aliran jualan.',
                'decided_at' => '2026-09-08 00:00:00',
            ],
        );

        $lots = [
            [
                'id' => 'qr_contoh_root',
                'qr_code' => 'GPL-QR-000901',
                'application_id' => 'app_contoh_nangka',
                'parent_id' => null,
                'holder_company_id' => 'co_contoh_pengeluar',
                'pending_holder_company_id' => null,
                'quantity' => 1000,
                'quantity_remaining' => 200,
                'disposition' => LotDisposition::Holding,
                'status' => QrStatus::Active,
                'generated_at' => '2026-09-08 09:00:00',
                'activated_at' => '2026-09-08 09:00:00',
                'accepted_at' => null,
                'sold_at' => null,
            ],
            [
                'id' => 'qr_contoh_eksport',
                'qr_code' => 'GPL-QR-000902',
                'application_id' => null,
                'parent_id' => 'qr_contoh_root',
                'holder_company_id' => 'co_contoh_pengeksport',
                'pending_holder_company_id' => 'co_contoh_pengeksport',
                'quantity' => 500,
                'quantity_remaining' => 200,
                'disposition' => LotDisposition::Holding,
                'status' => QrStatus::Active,
                'generated_at' => '2026-09-08 10:00:00',
                'activated_at' => '2026-09-10 10:00:00',
                'accepted_at' => '2026-09-10 10:00:00',
                'sold_at' => null,
            ],
            [
                'id' => 'qr_contoh_eksport_wait',
                'qr_code' => 'GPL-QR-000903',
                'application_id' => null,
                'parent_id' => 'qr_contoh_root',
                'holder_company_id' => null,
                'pending_holder_company_id' => 'co_contoh_pengeksport',
                'quantity' => 300,
                'quantity_remaining' => 300,
                'disposition' => LotDisposition::Holding,
                'status' => QrStatus::GeneratedInactive,
                'generated_at' => '2026-10-20 09:00:00',
                'activated_at' => null,
                'accepted_at' => null,
                'sold_at' => null,
            ],
            [
                'id' => 'qr_contoh_borong',
                'qr_code' => 'GPL-QR-000904',
                'application_id' => null,
                'parent_id' => 'qr_contoh_eksport',
                'holder_company_id' => 'co_contoh_pengimport',
                'pending_holder_company_id' => 'co_contoh_pengimport',
                'quantity' => 180,
                'quantity_remaining' => 100,
                'disposition' => LotDisposition::Holding,
                'status' => QrStatus::Active,
                'generated_at' => '2026-09-22 09:00:00',
                'activated_at' => '2026-09-24 09:00:00',
                'accepted_at' => '2026-09-24 09:00:00',
                'sold_at' => null,
            ],
            [
                'id' => 'qr_contoh_sold',
                'qr_code' => 'GPL-QR-000905',
                'application_id' => null,
                'parent_id' => 'qr_contoh_eksport',
                'holder_company_id' => null,
                'pending_holder_company_id' => null,
                'quantity' => 120,
                'quantity_remaining' => 0,
                'disposition' => LotDisposition::Sold,
                'status' => QrStatus::Active,
                'generated_at' => '2026-10-08 11:00:00',
                'activated_at' => '2026-10-08 11:00:00',
                'accepted_at' => null,
                'sold_at' => '2026-10-08 11:00:00',
            ],
            [
                'id' => 'qr_contoh_import',
                'qr_code' => 'GPL-QR-000906',
                'application_id' => null,
                'parent_id' => 'qr_contoh_borong',
                'holder_company_id' => 'co_contoh_pemborong',
                'pending_holder_company_id' => 'co_contoh_pemborong',
                'quantity' => 80,
                'quantity_remaining' => 30,
                'disposition' => LotDisposition::Holding,
                'status' => QrStatus::Active,
                'generated_at' => '2026-10-06 09:00:00',
                'activated_at' => '2026-10-08 09:00:00',
                'accepted_at' => '2026-10-08 09:00:00',
                'sold_at' => null,
            ],
            [
                'id' => 'qr_contoh_runcit_wait',
                'qr_code' => 'GPL-QR-000907',
                'application_id' => null,
                'parent_id' => 'qr_contoh_import',
                'holder_company_id' => null,
                'pending_holder_company_id' => 'co_contoh_peruncit',
                'quantity' => 50,
                'quantity_remaining' => 50,
                'disposition' => LotDisposition::Holding,
                'status' => QrStatus::GeneratedInactive,
                'generated_at' => '2026-10-20 15:00:00',
                'activated_at' => null,
                'accepted_at' => null,
                'sold_at' => null,
            ],
        ];
        $this->saveContohLots('app_contoh_nangka', $lots);

        $this->saveContohApplication('app_contoh_tembikai', 'appr_contoh_tembikai', [
            'application_no' => 'FAMA-2026-000911',
            'produce_type_id' => 'pt_tembikai',
            'variety' => 'Black Beauty',
            'grade' => 'A',
            'size' => 'M',
            'quantity' => 800,
            'destination_country' => 'Brunei',
            'export_date' => '2026-09-28',
            'lot_no' => 'LOT-CONTOH-02',
            'farm_lat' => 2.8030000,
            'farm_lng' => 103.4880000,
            'farm_location' => 'Rompin, Pahang',
            'display_image_path' => '/products/produce-tembikai-demo-01.jpg',
            'submitted_at' => '2026-09-12 00:00:00',
            'reviewed_at' => '2026-09-15 00:00:00',
            'decided_at' => '2026-09-15 00:00:00',
        ]);
        $this->saveContohLots('app_contoh_tembikai', [
            $this->contohLot('qr_contoh_tembikai_root', 'GPL-QR-000911', 'app_contoh_tembikai', null, 'co_contoh_pengeluar', null, 800, 100, '2026-09-15 09:00:00', '2026-09-15 09:00:00'),
            $this->contohLot('qr_contoh_tembikai_eksport', 'GPL-QR-000912', null, 'qr_contoh_tembikai_root', 'co_contoh_pengeksport', 'co_contoh_pengeksport', 400, 150, '2026-09-15 10:00:00', '2026-09-17 10:00:00', '2026-09-17 10:00:00'),
            $this->contohLot('qr_contoh_tembikai_borong', 'GPL-QR-000913', null, 'qr_contoh_tembikai_root', 'co_contoh_pemborong', 'co_contoh_pemborong', 300, 40, '2026-09-28 09:00:00', '2026-09-29 09:00:00', '2026-09-29 09:00:00'),
            $this->contohLot('qr_contoh_tembikai_sold', 'GPL-QR-000914', null, 'qr_contoh_tembikai_eksport', null, null, 250, 0, '2026-10-13 11:00:00', '2026-10-13 11:00:00', null, '2026-10-13 11:00:00', LotDisposition::Sold),
            $this->contohLot('qr_contoh_tembikai_runcit', 'GPL-QR-000915', null, 'qr_contoh_tembikai_borong', 'co_contoh_peruncit', 'co_contoh_peruncit', 180, 180, '2026-10-14 09:00:00', '2026-10-15 09:00:00', '2026-10-15 09:00:00'),
            $this->contohLot('qr_contoh_tembikai_import_wait', 'GPL-QR-000916', null, 'qr_contoh_tembikai_borong', null, 'co_contoh_peruncit', 80, 80, '2026-10-22 09:00:00', null, null, null, LotDisposition::Holding, QrStatus::GeneratedInactive),
        ]);

        GalleryItem::query()->firstOrCreate(
            ['id' => 'gal_contoh_tembikai'],
            [
                'company_id' => 'co_contoh_pengeluar',
                'category' => 'BUAH',
                'description' => 'BUAH',
                'file_path' => '/products/produce-tembikai-demo-01.jpg',
                'uploaded_by' => 'user_contoh_pengeluar',
                'uploaded_at' => '2026-09-15 00:00:00',
            ],
        );

        $this->saveContohApplication('app_contoh_mangga', 'appr_contoh_mangga', [
            'application_no' => 'FAMA-2026-000921',
            'produce_type_id' => 'pt_mangga',
            'variety' => 'Harumanis',
            'grade' => 'Premium',
            'size' => 'L',
            'quantity' => 600,
            'destination_country' => 'Thailand',
            'export_date' => '2026-10-02',
            'lot_no' => 'LOT-CONTOH-03',
            'farm_lat' => 3.7630000,
            'farm_lng' => 103.2200000,
            'farm_location' => 'Pekan, Pahang',
            'display_image_path' => '/products/produce-mangga-demo-01.jpg',
            'submitted_at' => '2026-09-14 00:00:00',
            'reviewed_at' => '2026-09-16 00:00:00',
            'decided_at' => '2026-09-16 00:00:00',
        ]);
        $this->saveContohLots('app_contoh_mangga', [
            $this->contohLot('qr_contoh_mangga_root', 'GPL-QR-000921', 'app_contoh_mangga', null, 'co_contoh_pengeluar', null, 600, 0, '2026-09-16 09:00:00', '2026-09-16 09:00:00'),
            $this->contohLot('qr_contoh_mangga_eksport', 'GPL-QR-000922', null, 'qr_contoh_mangga_root', 'co_contoh_pengeksport', 'co_contoh_pengeksport', 600, 100, '2026-09-16 10:00:00', '2026-09-18 10:00:00', '2026-09-18 10:00:00'),
            $this->contohLot('qr_contoh_mangga_borong', 'GPL-QR-000923', null, 'qr_contoh_mangga_eksport', 'co_contoh_pengimport', 'co_contoh_pengimport', 350, 100, '2026-09-30 09:00:00', '2026-10-01 09:00:00', '2026-10-01 09:00:00'),
            $this->contohLot('qr_contoh_mangga_sold', 'GPL-QR-000924', null, 'qr_contoh_mangga_eksport', null, null, 150, 0, '2026-10-18 11:00:00', '2026-10-18 11:00:00', null, '2026-10-18 11:00:00', LotDisposition::Sold),
            $this->contohLot('qr_contoh_mangga_import_wait', 'GPL-QR-000927', null, 'qr_contoh_mangga_borong', 'co_contoh_pemborong', 'co_contoh_pemborong', 250, 50, '2026-10-22 15:00:00', '2026-10-22 15:00:00', '2026-10-22 15:00:00'),
            $this->contohLot('qr_contoh_mangga_runcit', 'GPL-QR-000925', null, 'qr_contoh_mangga_import_wait', 'co_contoh_peruncit', 'co_contoh_peruncit', 200, 120, '2026-10-15 09:00:00', '2026-10-16 09:00:00', '2026-10-16 09:00:00'),
            $this->contohLot('qr_contoh_mangga_runcit_sold', 'GPL-QR-000926', null, 'qr_contoh_mangga_runcit', null, null, 80, 0, '2026-10-21 11:00:00', '2026-10-21 11:00:00', null, '2026-10-21 11:00:00', LotDisposition::Sold),
        ]);

        GalleryItem::query()->firstOrCreate(
            ['id' => 'gal_contoh_mangga'],
            [
                'company_id' => 'co_contoh_pengeluar',
                'category' => 'BUAH',
                'description' => 'BUAH',
                'file_path' => '/products/produce-mangga-demo-01.jpg',
                'uploaded_by' => 'user_contoh_pengeluar',
                'uploaded_at' => '2026-09-16 00:00:00',
            ],
        );

        QrCode::query()->where('id', 'qr_pisang_mkt_250')->update([
            'holder_company_id' => 'co_contoh_pengimport',
            'pending_holder_company_id' => 'co_contoh_pengimport',
        ]);
    }

    /**
     * @param  array<string, mixed>  $fields
     */
    private function saveContohApplication(string $id, string $approvalId, array $fields): void
    {
        ExportApplication::query()->firstOrCreate(
            ['id' => $id],
            [
                'application_no' => $fields['application_no'],
                'company_id' => 'co_contoh_pengeluar',
                'produce_type_id' => $fields['produce_type_id'],
                'variety' => $fields['variety'],
                'grade' => $fields['grade'],
                'size' => $fields['size'],
                'quantity' => $fields['quantity'],
                'quantity_unit' => 'kg',
                'destination_country' => $fields['destination_country'],
                'coc_certificate_id' => 'cert_contoh_coc',
                'coc_number' => 'COC-CONTOH-2026-01',
                'export_date' => $fields['export_date'],
                'lot_no' => $fields['lot_no'],
                'farm_location' => $fields['farm_location'],
                'farm_lat' => $fields['farm_lat'],
                'farm_lng' => $fields['farm_lng'],
                'display_image_path' => $fields['display_image_path'],
                'farm_name' => 'Ladang Contoh Hijau',
                'importer_name' => 'Import Contoh Niaga Sdn. Bhd.',
                'importer_address' => '24, Jalan Contoh, Petaling Jaya, Selangor',
                'status' => ApplicationStatus::Approved,
                'submitted_at' => $fields['submitted_at'],
                'reviewed_at' => $fields['reviewed_at'],
            ],
        );
        Approval::query()->firstOrCreate(
            ['id' => $approvalId],
            [
                'application_id' => $id,
                'officer_user_id' => 'user_fama',
                'decision' => 'APPROVED',
                'remarks' => 'Lot contoh diluluskan untuk aliran jualan.',
                'decided_at' => $fields['decided_at'],
            ],
        );
    }

    /**
     * @param  list<array<string, mixed>>  $lots
     */
    private function saveContohLots(string $rootApplicationId, array $lots): void
    {
        foreach ($lots as $row) {
            QrCode::query()->firstOrCreate(
                ['id' => $row['id']],
                $row + [
                    'root_application_id' => $rootApplicationId,
                    'public_slug' => $row['qr_code'],
                ],
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function contohLot(
        string $id,
        string $code,
        ?string $applicationId,
        ?string $parentId,
        ?string $holderId,
        ?string $pendingId,
        int $quantity,
        int $remaining,
        string $generatedAt,
        ?string $activatedAt,
        ?string $acceptedAt = null,
        ?string $soldAt = null,
        LotDisposition $disposition = LotDisposition::Holding,
        QrStatus $status = QrStatus::Active,
    ): array {
        return [
            'id' => $id,
            'qr_code' => $code,
            'application_id' => $applicationId,
            'parent_id' => $parentId,
            'holder_company_id' => $holderId,
            'pending_holder_company_id' => $pendingId,
            'quantity' => $quantity,
            'quantity_remaining' => $remaining,
            'disposition' => $disposition,
            'status' => $status,
            'generated_at' => $generatedAt,
            'activated_at' => $activatedAt,
            'accepted_at' => $acceptedAt,
            'sold_at' => $soldAt,
        ];
    }
}
