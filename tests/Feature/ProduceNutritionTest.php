<?php

namespace Tests\Feature;

use App\Models\ProduceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProduceNutritionTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_fruits_show_nutrition_on_the_public_page_and_the_form(): void
    {
        $this->seed();

        $this->get('/trace/GPL-QR-001130')
            ->assertOk()
            ->assertSee('Nutrisi')
            ->assertSee('30 kcal')
            ->assertSee('Tembikai');

        $this->actingAs(User::query()->findOrFail('user_contoh_pengeluar'))
            ->get('/exporter/company/produce')
            ->assertOk()
            ->assertSee('Nutrisi bagi 100 g')
            ->assertSee('Tembikai')
            ->assertSee('30 kcal')
            ->assertSee('Simpan nutrisi');
    }

    public function test_company_can_update_nutrition_for_its_own_produce(): void
    {
        $this->seed();

        $nutrients = [];
        foreach (\App\Support\ProduceNutrition::names() as $index => $name) {
            $nutrients[$index] = [
                'name' => $name,
                'amount' => $name === 'Kalori' ? '33 kcal' : '1 g',
                'dailyPercent' => '1%',
            ];
        }

        $this->actingAs(User::query()->findOrFail('user_contoh_pengeluar'))
            ->post(route('exporter.produce.nutrition'), [
                'produceTypeId' => 'pt_tembikai',
                'nutrients' => $nutrients,
            ])
            ->assertRedirect(route('exporter.produce'))
            ->assertSessionHas('status', 'Nutrisi disimpan.');

        $this->assertSame('33 kcal', ProduceType::query()->find('pt_tembikai')->nutrition[0]['amount']);

        $this->get('/trace/GPL-QR-001130')
            ->assertOk()
            ->assertSee('33 kcal');
    }

    public function test_other_company_cannot_edit_a_produce_it_does_not_list(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->post(route('exporter.produce.nutrition'), [
                'produceTypeId' => 'pt_tembikai',
                'nutrients' => [
                    ['name' => 'Kalori', 'amount' => '1 kcal', 'dailyPercent' => '1%'],
                ],
            ])
            ->assertRedirect(route('exporter.produce'))
            ->assertSessionHas('error');

        $this->assertSame('30 kcal', ProduceType::query()->find('pt_tembikai')->nutrition[0]['amount']);
    }
}
