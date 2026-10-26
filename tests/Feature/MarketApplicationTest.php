<?php

namespace Tests\Feature;

use App\Domain\ApplicationStatus;
use App\Domain\Market;
use App\Models\ExportApplication;
use App\Models\User;
use App\Services\JejakService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_produce_form_offers_domestic_and_export(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->get(route('exporter.applications.create', ['kind' => 'produce']))
            ->assertOk()
            ->assertSee('Pasaran')
            ->assertSee('Domestik')
            ->assertSee('Eksport')
            ->assertSee('Maklumat Ladang')
            ->assertSee('Maklumat Eksport')
            ->assertSee('Pengimport')
            ->assertSee('Alamat Pengimport');
    }

    public function test_domestic_produce_application_skips_export_fields(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->post('/exporter/applications', [
                'market' => 'DOMESTIC',
                'productKind' => 'PRODUCE',
                'produceTypeId' => 'pt_durian',
                'variety' => 'Kampung',
                'grade' => 'A',
                'quantity' => 40,
                'farmName' => 'Ladang Dalam Negeri',
            ])
            ->assertRedirect();

        $application = ExportApplication::query()->where('variety', 'Kampung')->first();
        $this->assertNotNull($application);
        $this->assertSame(Market::Domestic, $application->market);
        $this->assertSame('', $application->destination_country);
        $this->assertSame('', $application->importer_name);
        $this->assertSame('', $application->importer_address);
        $this->assertNull($application->export_date);
        $this->assertSame('Ladang Dalam Negeri', $application->farm_name);

        $application->update(['status' => ApplicationStatus::Submitted]);

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->get(route('exporter.applications.show', $application))
            ->assertOk()
            ->assertSee('Domestik')
            ->assertSee('Ladang Dalam Negeri')
            ->assertDontSee('Pengimport')
            ->assertDontSee('Alamat pengimport')
            ->assertDontSee('>Destinasi<', false);
    }

    public function test_export_application_still_requires_importer_and_destination(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->from(route('exporter.applications.create', ['kind' => 'produce']))
            ->post('/exporter/applications', [
                'market' => 'EXPORT',
                'productKind' => 'PRODUCE',
                'produceTypeId' => 'pt_durian',
                'variety' => 'Musang King',
                'grade' => 'A',
                'quantity' => 10,
                'farmName' => 'Ladang Uji',
            ])
            ->assertRedirect(route('exporter.applications.create', ['kind' => 'produce']))
            ->assertSessionHas('error', 'Sila isi destinasi');

        $this->assertNull(ExportApplication::query()->where('farm_name', 'Ladang Uji')->first());
    }

    public function test_domestic_lot_is_absent_from_the_public_export_lines(): void
    {
        $this->seed();
        $fama = User::query()->findOrFail('user_fama');
        $result = app(JejakService::class)->createAndActivateQr('co_abc', [
            'market' => Market::Domestic->value,
            'produce_type_id' => 'pt_durian',
            'variety' => 'Lokal',
            'grade' => 'A',
            'size' => 'M',
            'quantity' => 15,
            'quantity_unit' => 'kg',
            'coc_certificate_id' => null,
            'coc_number' => '',
            'farm_name' => 'Kebun Tempatan',
            'farm_location' => 'Raub, Pahang',
        ], $fama);

        $this->assertSame(Market::Domestic, $result['application']->market);

        $this->get('/trace/'.$result['qr']->qr_code)
            ->assertOk()
            ->assertSee('Kebun Tempatan')
            ->assertSee('>Asal</h2>', false)
            ->assertDontSee('Destinasi eksport')
            ->assertDontSee('Asal & eksport');
    }

    public function test_application_lists_are_split_by_market(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->get(route('exporter.applications'))
            ->assertOk()
            ->assertSee('Domestik')
            ->assertSee('Eksport')
            ->assertSee('FAMA-2026-000015');

        $this->actingAs(User::query()->findOrFail('user_fama'))
            ->get(route('fama.applications'))
            ->assertOk()
            ->assertSee('Domestik')
            ->assertSee('Eksport');
    }
}
