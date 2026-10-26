<?php

namespace Tests\Feature;

use App\Domain\PartyType;
use App\Models\Company;
use App\Models\User;
use App\Services\PecahanDashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ContohDemoAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_contoh_accounts_are_fictional_and_cover_the_chain(): void
    {
        $this->seed();

        $expected = [
            'co_contoh_pengeluar' => [PartyType::Supplier, 'aminah.contoh@example.com', 'Ladang Contoh Hijau Sdn. Bhd.'],
            'co_contoh_pengeksport' => [PartyType::Exporter, 'farid.contoh@example.com', 'Niaga Contoh Eksport Sdn. Bhd.'],
            'co_contoh_pemborong' => [PartyType::Marketer, 'lina.contoh@example.com', 'Borong Contoh Segar Sdn. Bhd.'],
            'co_contoh_pengimport' => [PartyType::Importer, 'hafiz.contoh@example.com', 'Import Contoh Niaga Sdn. Bhd.'],
            'co_contoh_peruncit' => [PartyType::Retailer, 'suri.contoh@example.com', 'Kedai Contoh Runcit Sdn. Bhd.'],
        ];

        foreach ($expected as $companyId => [$party, $email, $name]) {
            $company = Company::query()->findOrFail($companyId);
            $this->assertSame($party, $company->party_type);
            $this->assertSame($name, $company->name);

            $user = User::query()->where('email', $email)->firstOrFail();
            $this->assertSame($companyId, $user->company_id);
            $this->assertTrue(Hash::check('Exporter123!', $user->password));
            $this->assertTrue($user->companies()->where('companies.id', $companyId)->exists());
            $this->assertStringStartsWith('/logos/contoh-', (string) $company->logo_path);
        }
    }

    public function test_contoh_nangka_lot_has_a_chain_flow_without_public_certificates(): void
    {
        $this->seed();

        $stats = app(PecahanDashboard::class)->forCompany('co_contoh_pengeksport');
        $this->assertTrue($stats['hasLots']);
        $this->assertSame(1, $stats['waiting']);
        $this->assertGreaterThan(0, $stats['soldThisMonth']);
        $labels = collect($stats['destinations'])->flatMap(fn (array $group) => collect($group['items'])->pluck('label'));
        $this->assertTrue($labels->contains('Pemborong'));
        $this->assertTrue($labels->contains('Pengguna'));

        $this->actingAs(User::query()->where('email', 'aminah.contoh@example.com')->firstOrFail())
            ->get(route('exporter.company'))
            ->assertOk()
            ->assertSee('/logos/contoh-pengeluar.svg', false);

        $this->get('/trace/GPL-QR-000906')
            ->assertOk()
            ->assertSee('Nangka')
            ->assertSee('Ladang Contoh Hijau Sdn. Bhd.')
            ->assertSee('Niaga Contoh Eksport Sdn. Bhd.')
            ->assertSee('Borong Contoh Segar Sdn. Bhd.')
            ->assertSee('Import Contoh Niaga Sdn. Bhd.')
            ->assertSee('Pengeluar')
            ->assertDontSee('Dokumen & sijil')
            ->assertDontSee('/certificates/', false);

        $this->get('/trace/GPL-QR-000915')
            ->assertOk()
            ->assertSee('Tembikai')
            ->assertSee('Ladang Contoh Hijau Sdn. Bhd.')
            ->assertSee('Borong Contoh Segar Sdn. Bhd.')
            ->assertSee('Kedai Contoh Runcit Sdn. Bhd.')
            ->assertDontSee('Dokumen & sijil')
            ->assertDontSee('/certificates/', false);

        $this->get('/trace/GPL-QR-000925')
            ->assertOk()
            ->assertSee('Mangga')
            ->assertSee('Niaga Contoh Eksport Sdn. Bhd.')
            ->assertSee('Kedai Contoh Runcit Sdn. Bhd.')
            ->assertDontSee('Dokumen & sijil')
            ->assertDontSee('/certificates/', false);
    }
}
