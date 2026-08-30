<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\JejakService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FamaCompanyListTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_list_is_sorted_by_name(): void
    {
        $this->seed();
        $fama = User::query()->findOrFail('user_fama');
        $jejak = app(JejakService::class);
        $jejak->createCompany([
            'name' => 'Zahid Orchard Sdn. Bhd.',
            'registration_no' => 'ZH900001',
            'email' => '',
            'phone' => '',
            'address' => '',
            'state' => '',
            'district' => '',
            'postcode' => '',
            'website' => '',
        ], $fama);
        $jejak->createCompany([
            'name' => '9 (Beta Farm Resources)',
            'registration_no' => 'BF900002',
            'email' => '',
            'phone' => '',
            'address' => '',
            'state' => '',
            'district' => '',
            'postcode' => '',
            'website' => '',
        ], $fama);

        $this->actingAs($fama)
            ->get(route('fama.companies'))
            ->assertOk()
            ->assertSeeInOrder([
                'ABC Fruits Sdn. Bhd.',
                'Beta Farm Resources',
                'MTS Fruits Sdn. Bhd.',
                'Zahid Orchard Sdn. Bhd.',
            ])
            ->assertSee('data-role="company-search"', false)
            ->assertSee('data-debounce="300"', false);
    }

    public function test_company_search_matches_name_inside_numbered_label(): void
    {
        $this->seed();
        app(JejakService::class)->createCompany([
            'name' => '5 (ZBM AGROTECH)',
            'registration_no' => 'ZB900003',
            'email' => '',
            'phone' => '',
            'address' => '',
            'state' => '',
            'district' => '',
            'postcode' => '',
            'website' => '',
        ], User::query()->findOrFail('user_fama'));

        $this->actingAs(User::query()->findOrFail('user_fama'))
            ->get(route('fama.companies', ['q' => 'ZBM']))
            ->assertOk()
            ->assertSee('ZBM AGROTECH')
            ->assertDontSee('ABC Fruits Sdn. Bhd.');
    }

    public function test_company_search_filters_by_name(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_fama'))
            ->get(route('fama.companies', ['q' => 'MTS']))
            ->assertOk()
            ->assertSee('MTS Fruits Sdn. Bhd.')
            ->assertDontSee('ABC Fruits Sdn. Bhd.')
            ->assertSee('value="MTS"', false);
    }

    public function test_company_search_filters_by_registration_no(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_fama'))
            ->get(route('fama.companies', ['q' => 'AB34567']))
            ->assertOk()
            ->assertSee('ABC Fruits Sdn. Bhd.')
            ->assertDontSee('MTS Fruits Sdn. Bhd.');
    }

    public function test_company_search_empty_state(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_fama'))
            ->get(route('fama.companies', ['q' => 'tiada-syarikat-xyz']))
            ->assertOk()
            ->assertSee('Tiada syarikat dijumpai untuk carian ini.')
            ->assertDontSee('ABC Fruits Sdn. Bhd.');
    }
}
