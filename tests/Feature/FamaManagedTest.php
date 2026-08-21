<?php

namespace Tests\Feature;

use App\Domain\ApplicationStatus;
use App\Domain\QrStatus;
use App\Models\User;
use App\Services\JejakService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class FamaManagedTest extends TestCase
{
    use RefreshDatabase;

    private function fama(): User
    {
        return User::query()->find('user_fama');
    }

    /**
     * @return array<string, mixed>
     */
    private function applicationInput(string $companyId = ''): array
    {
        return [
            'company_id' => $companyId,
            'produce_type_id' => 'pt_durian',
            'variety' => 'Musang King',
            'grade' => 'A',
            'size' => 'L',
            'quantity' => 120,
            'quantity_unit' => 'kg',
            'destination_country' => 'MAHA',
            'coc_certificate_id' => null,
            'coc_number' => '',
            'export_date' => '2026-08-21',
            'farm_name' => 'Ladang MAHA',
            'importer_name' => 'Pengunjung MAHA',
            'importer_address' => 'MAEPS Serdang',
        ];
    }

    public function test_fama_creates_a_company_without_a_user_row(): void
    {
        $this->seed();
        $usersBefore = User::query()->count();
        $company = app(JejakService::class)->createCompany([
            'name' => 'MAHA Durian Stall',
            'registration_no' => 'MH100001',
            'email' => 'maha@example.com',
            'phone' => '03-1111 2222',
            'address' => 'Dewan MAHA, Serdang',
            'state' => 'Selangor',
            'district' => 'Serdang',
            'postcode' => '43400',
            'website' => '',
        ], $this->fama());

        $this->assertSame('FAMA', $company->external_source);
        $this->assertSame('FAMA-MH100001', $company->external_account_no);
        $this->assertSame($usersBefore, User::query()->count());
        $this->assertFalse(User::query()->where('company_id', $company->id)->exists());
        $this->assertDatabaseHas('audit_logs', ['action' => 'COMPANY_CREATED', 'object_id' => $company->id]);
    }

    public function test_duplicate_registration_is_rejected(): void
    {
        $this->seed();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('telah wujud');
        app(JejakService::class)->createCompany([
            'name' => 'Copy',
            'registration_no' => 'AB34567',
            'email' => '',
            'phone' => '',
            'address' => '',
            'state' => '',
            'district' => '',
            'postcode' => '',
            'website' => '',
        ], $this->fama());
    }

    public function test_fama_create_qr_activates_application_and_public_qr(): void
    {
        $this->seed();
        $service = app(JejakService::class);
        $company = $service->createCompany([
            'name' => 'MAHA Nanas',
            'registration_no' => 'MH100002',
            'email' => '',
            'phone' => '',
            'address' => 'Serdang',
            'state' => 'Selangor',
            'district' => 'Serdang',
            'postcode' => '43400',
            'website' => '',
        ], $this->fama());
        $result = $service->createAndActivateQr($company->id, $this->applicationInput(), $this->fama());

        $this->assertSame(ApplicationStatus::Approved, $result['application']->status);
        $this->assertSame(QrStatus::Active, $result['qr']->status);
        $this->assertSame($result['application']->id, $result['qr']->application_id);
        $this->assertMatchesRegularExpression('/^GPL-QR-\d+$/', $result['qr']->qr_code);
        $this->assertSame($result['qr']->qr_code, $result['qr']->public_slug);
    }

    public function test_fama_can_edit_approved_public_fields_without_changing_qr_identity(): void
    {
        $this->seed();
        $service = app(JejakService::class);
        $company = $service->createCompany([
            'name' => 'MAHA Betik',
            'registration_no' => 'MH100003',
            'email' => '',
            'phone' => '',
            'address' => 'Serdang',
            'state' => 'Selangor',
            'district' => 'Serdang',
            'postcode' => '43400',
            'website' => '',
        ], $this->fama());
        $result = $service->createAndActivateQr($company->id, $this->applicationInput(), $this->fama());
        $updated = $service->updateManagedApplication(
            $result['application']->id,
            [...$this->applicationInput(), 'farm_name' => 'Ladang MAHA Kemaskini', 'variety' => 'Black Thorn'],
            $this->fama(),
        );
        $sameQr = $result['qr']->fresh();

        $this->assertSame('Ladang MAHA Kemaskini', $updated->farm_name);
        $this->assertSame('Black Thorn', $updated->variety);
        $this->assertSame($result['qr']->qr_code, $sameQr->qr_code);
        $this->assertSame($result['qr']->public_slug, $sameQr->public_slug);
        $this->assertSame(QrStatus::Active, $sameQr->status);
    }

    public function test_exporter_update_application_stays_draft_only(): void
    {
        $this->seed();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Hanya draf');
        app(JejakService::class)->updateApplication('app_109', ['variety' => 'Tidak dibenarkan']);
    }

    public function test_dagangnet_company_name_stays_locked_for_fama_edits(): void
    {
        $this->seed();
        $updated = app(JejakService::class)->updateManagedCompany(
            'co_abc',
            ['name' => 'Should Not Change', 'address' => 'Alamat baru FAMA'],
            $this->fama(),
        );
        $this->assertSame('ABC Fruits Sdn. Bhd.', $updated->name);
        $this->assertSame('Alamat baru FAMA', $updated->address);
        $this->assertSame('DAGANGNET', $updated->external_source);
    }

    public function test_login_and_public_trace(): void
    {
        $this->seed();
        $this->post('/auth/login', [
            'email' => 'ali@abcfruits.example',
            'password' => 'Exporter123!',
            'role' => 'EXPORTER',
        ])->assertRedirect('/exporter');

        $this->get('/trace/GPL-QR-000109')->assertOk()->assertSee('Tembikai');
        $this->get('/trace/GPL-QR-000123')->assertOk()->assertSee('QR Belum Diaktifkan');
        $this->get('/api/public/trace/GPL-QR-000109')->assertOk()->assertJsonPath('active', true);
    }
}
