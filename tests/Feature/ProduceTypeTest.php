<?php

namespace Tests\Feature;

use App\Models\CompanyProduce;
use App\Models\ExportApplication;
use App\Models\ProduceType;
use App\Models\User;
use App\Services\JejakService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class ProduceTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_keluaran_form_shows_add_control(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->get('/exporter/company/produce')
            ->assertOk()
            ->assertSee('Jenis Keluaran Pertanian')
            ->assertSee('Tambah Jenis Keluaran Pertanian')
            ->assertSee('Jenis Keluaran Pertanian baharu');
    }

    public function test_exporter_can_add_a_new_produce_type_from_keluaran_form(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->post('/exporter/company/produce', [
                'newProduceName' => 'Rambutan',
            ])
            ->assertRedirect(route('exporter.produce'));

        $type = ProduceType::query()->where('name', 'Rambutan')->first();
        $this->assertNotNull($type);
        $this->assertDatabaseHas('company_produce', [
            'company_id' => 'co_abc',
            'produce_type_id' => $type->id,
        ]);
    }

    public function test_new_produce_name_reuses_existing_type_case_insensitively(): void
    {
        $this->seed();
        $before = ProduceType::query()->count();

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->post('/exporter/company/produce', [
                'newProduceName' => 'durian',
            ])
            ->assertRedirect(route('exporter.produce'));

        $this->assertSame($before, ProduceType::query()->count());
        $this->assertSame(1, CompanyProduce::query()->where('company_id', 'co_abc')->where('produce_type_id', 'pt_durian')->count());
    }

    public function test_empty_new_produce_name_is_rejected_when_no_selection(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->from('/exporter/company/produce')
            ->post('/exporter/company/produce', [
                'produceTypeId' => '',
                'newProduceName' => '   ',
            ])
            ->assertRedirect(route('exporter.produce'))
            ->assertSessionHas('error');
    }

    public function test_application_can_be_created_with_a_new_produce_name(): void
    {
        $this->seed();

        $application = app(JejakService::class)->createApplication([
            'company_id' => 'co_abc',
            'produce_type_id' => '',
            'new_produce_name' => 'Cempedak',
            'variety' => 'Biasa',
            'grade' => 'A',
            'size' => 'L',
            'quantity' => 10,
            'quantity_unit' => 'kg',
            'destination_country' => 'Singapura',
            'coc_certificate_id' => null,
            'coc_number' => '',
            'export_date' => null,
            'farm_name' => 'Ladang Uji',
            'importer_name' => 'Import Sdn Bhd',
            'importer_address' => 'Singapore',
        ]);

        $this->assertInstanceOf(ExportApplication::class, $application);
        $this->assertSame('Cempedak', $application->produceType?->name);
        $this->assertDatabaseHas('company_produce', [
            'company_id' => 'co_abc',
            'produce_type_id' => $application->produce_type_id,
        ]);
    }

    public function test_find_or_create_rejects_blank_name(): void
    {
        $this->seed();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Jenis Keluaran Pertanian diperlukan');
        app(JejakService::class)->findOrCreateProduceType('   ');
    }
}
