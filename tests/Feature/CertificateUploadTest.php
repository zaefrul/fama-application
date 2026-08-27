<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_exporter_can_upload_certificate_without_issue_date(): void
    {
        $this->seed();
        Storage::fake('public');

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->post('/exporter/company/certificates', [
                'type' => 'HACCP',
                'certificateNo' => 'HACCP-TEST-500',
                'issueDate' => '',
                'expiryDate' => '',
                'document' => UploadedFile::fake()->image('sijil.jpg'),
            ])
            ->assertRedirect(route('exporter.certificates'));

        $this->assertDatabaseHas('certificates', [
            'company_id' => 'co_abc',
            'certificate_no' => 'HACCP-TEST-500',
            'type' => 'HACCP',
        ]);

        $certificate = Certificate::query()->where('certificate_no', 'HACCP-TEST-500')->first();
        $this->assertNotNull($certificate?->issue_date);
        $this->assertNull($certificate?->expiry_date);
    }
}
