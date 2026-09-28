<?php

namespace Tests\Feature;

use App\Domain\ProductKind;
use App\Models\ExportApplication;
use App\Models\ProduceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivestockApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_qr_asks_for_a_kind_before_the_form(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->get(route('exporter.applications.create'))
            ->assertOk()
            ->assertSee('Keluaran Pertanian')
            ->assertSee('Haiwan Ternakan')
            ->assertDontSee('name="grade"', false);

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->get(route('exporter.applications.create', ['kind' => 'produce']))
            ->assertOk()
            ->assertSee('Jenis Keluaran Pertanian')
            ->assertSee('Gred')
            ->assertDontSee('Rumah Sembelih');

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->get(route('exporter.applications.create', ['kind' => 'livestock']))
            ->assertOk()
            ->assertSee('Jenis Ternakan')
            ->assertSee('Baka')
            ->assertSee('Rumah Sembelih')
            ->assertSee('Lembu')
            ->assertDontSee('No Sijil CoC');
    }

    public function test_fama_qr_create_also_asks_for_a_kind_first(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_fama'))
            ->get(route('fama.companies.qr.create', ['id' => 'co_abc']))
            ->assertOk()
            ->assertSee('Pilih jenis QR dahulu')
            ->assertSee('Haiwan Ternakan');
    }

    public function test_exporter_can_save_a_livestock_qr_draft(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->post('/exporter/applications', [
                'productKind' => 'LIVESTOCK',
                'produceTypeId' => 'pt_lembu',
                'variety' => 'Brahman',
                'headCount' => 12,
                'quantity' => 2400,
                'destinationCountry' => 'Singapura',
                'vetCertificateNo' => 'DVS-2026-001',
                'farmName' => 'Ladang Lembu Selangor',
                'abattoirName' => 'Rumah Sembelih Shah Alam',
                'slaughterDate' => '2026-06-01',
                'importerName' => 'Harbour Fresh Pte Ltd',
                'importerAddress' => '12 Pasir Panjang Road, Singapore',
            ])
            ->assertRedirect();

        $application = ExportApplication::query()->where('variety', 'Brahman')->first();
        $this->assertNotNull($application);
        $this->assertSame(ProductKind::Livestock, $application->product_kind);
        $this->assertSame('pt_lembu', $application->produce_type_id);
        $this->assertSame(12, $application->head_count);
        $this->assertSame('DVS-2026-001', $application->vet_certificate_no);
        $this->assertSame('Rumah Sembelih Shah Alam', $application->abattoir_name);
        $this->assertNull($application->coc_certificate_id);
        $this->assertNotNull($application->qrCode);

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->get(route('exporter.applications.show', $application))
            ->assertOk()
            ->assertSee('Brahman')
            ->assertSee('Rumah Sembelih');
    }

    public function test_livestock_form_rejects_a_produce_type(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->from(route('exporter.applications.create', ['kind' => 'livestock']))
            ->post('/exporter/applications', [
                'productKind' => 'LIVESTOCK',
                'produceTypeId' => 'pt_durian',
                'variety' => 'Musang King',
                'headCount' => 1,
                'quantity' => 10,
                'destinationCountry' => 'China',
                'vetCertificateNo' => 'DVS-1',
                'farmName' => 'Premis',
                'abattoirName' => 'Rumah',
                'importerName' => 'Import',
                'importerAddress' => 'Beijing',
            ])
            ->assertRedirect(route('exporter.applications.create', ['kind' => 'livestock']))
            ->assertSessionHas('error', 'Jenis ternakan tidak sah');
    }

    public function test_new_livestock_name_is_stored_as_livestock(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->post('/exporter/applications', [
                'productKind' => 'LIVESTOCK',
                'newProduceName' => 'Itik',
                'variety' => 'Pekin',
                'headCount' => 40,
                'quantity' => 80,
                'destinationCountry' => 'Brunei',
                'vetCertificateNo' => 'DVS-ITIK-1',
                'farmName' => 'Premis Itik',
                'abattoirName' => 'Rumah Sembelih Klang',
                'importerName' => 'Import',
                'importerAddress' => 'Brunei',
            ])
            ->assertRedirect();

        $type = ProduceType::query()->where('name', 'Itik')->first();
        $this->assertNotNull($type);
        $this->assertSame(ProductKind::Livestock->value, $type->category);
    }
}
