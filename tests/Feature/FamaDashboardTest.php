<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\JejakService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FamaDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_fama_dashboard_aggregates_existing_records(): void
    {
        $this->seed();

        $stats = app(JejakService::class)->dashboardFama();

        $this->assertSame(2, $stats['activeCompanies']);
        $this->assertSame(2, $stats['exporters']);
        $this->assertSame(5, $stats['qrRequests']);
        $this->assertSame(2, $stats['qrActive']);
        $this->assertSame(2, $stats['qrInactive']);
        $this->assertSame(5, $stats['uniqueFruits']);
        $this->assertSame(4, $stats['uniqueDestinations']);
        $this->assertSame('Mangga', $stats['topFruits'][0]['label']);
        $this->assertSame(2, $stats['topFruits'][0]['count']);
        $this->assertSame('China', $stats['topDestinations'][0]['label']);
        $this->assertContains('Selangor', array_column($stats['companiesByState'], 'label'));
        $this->assertCount(7, $stats['dailyQr']);
    }

    public function test_fama_dashboard_renders_operational_sections(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_fama'))
            ->get(route('fama.dashboard'))
            ->assertOk()
            ->assertSee('Permohonan QR')
            ->assertSee('Pengeksport')
            ->assertSee('Buah unik')
            ->assertSee('10 buah paling kerap')
            ->assertSee('Destinasi eksport')
            ->assertSee('Status permohonan')
            ->assertSee('Syarikat mengikut negeri')
            ->assertSee('Mangga')
            ->assertSee('China')
            ->assertSee('Tiada QR dijana dalam 7 hari lepas');
    }
}
