<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExporterRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_links_several_dagangnet_accounts(): void
    {
        $this->seed();

        $this->post('/auth/register/exporter', [
            'identifiers' => ['H0B00003', 'h0b00004'],
            'name' => 'Demo Usahawan',
            'identityReference' => '900101011111',
            'password' => 'Exporter123!',
            'confirmPassword' => 'Exporter123!',
        ])->assertRedirect(route('exporter.dashboard'));

        $user = User::query()->where('email', 'demo.utara@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('co_demo_utara', $user->company_id);
        $this->assertEqualsCanonicalizing(
            ['co_demo_utara', 'co_demo_selatan'],
            $user->companies()->pluck('companies.id')->all(),
        );

        $this->get('/exporter')
            ->assertOk()
            ->assertSee('Syarikat aktif')
            ->assertSee('Ladang Demo Utara Sdn. Bhd.');

        $this->post('/exporter/company/switch', ['company_id' => 'co_demo_selatan'])
            ->assertRedirect(route('exporter.dashboard'));

        $this->assertSame('co_demo_selatan', $user->fresh()->company_id);
        $this->get('/exporter')
            ->assertOk()
            ->assertSee('Selatan Fresh Sdn. Bhd.');
    }

    public function test_registration_rejects_an_account_already_linked(): void
    {
        $this->seed();

        $this->post('/auth/register/exporter', [
            'identifiers' => ['H0B00001', 'H0B00003'],
            'name' => 'Demo Usahawan',
            'identityReference' => '900101011111',
            'password' => 'Exporter123!',
            'confirmPassword' => 'Exporter123!',
        ])->assertRedirect(route('register.exporter', ['error' => 'taken', 'account' => 'H0B00001']));

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'demo.utara@example.com']);
    }

    public function test_single_company_login_hides_the_switcher(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->get('/exporter')
            ->assertOk()
            ->assertDontSee('Syarikat aktif');
    }

    public function test_usahawan_cannot_switch_to_an_unlinked_company(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->post('/exporter/company/switch', ['company_id' => 'co_mts'])
            ->assertForbidden();

        $this->assertSame('co_abc', User::query()->findOrFail('user_ali')->company_id);
    }
}
