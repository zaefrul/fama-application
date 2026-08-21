<?php

namespace Tests\Feature;

use App\Models\QrAccess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_trace_records_an_access(): void
    {
        $this->seed();
        $before = QrAccess::query()->where('qr_code', 'GPL-QR-000109')->count();

        $this->get('/trace/GPL-QR-000109')->assertOk();

        $this->assertSame($before + 1, QrAccess::query()->where('qr_code', 'GPL-QR-000109')->count());
    }

    public function test_invalid_qr_is_not_recorded(): void
    {
        $this->seed();
        $before = QrAccess::query()->count();

        $this->get('/trace/GPL-QR-NOT-REAL')->assertOk()->assertSee('Kod QR tidak sah');

        $this->assertSame($before, QrAccess::query()->count());
    }

    public function test_public_api_does_not_record_access(): void
    {
        $this->seed();
        $before = QrAccess::query()->count();

        $this->get('/api/public/trace/GPL-QR-000109')->assertOk();

        $this->assertSame($before, QrAccess::query()->count());
    }

    public function test_fama_dashboard_compares_weekly_access(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_fama'))
            ->get(route('fama.dashboard'))
            ->assertOk()
            ->assertSee('Imbasan Halaman Awam QR')
            ->assertSee('Bandingan minggu')
            ->assertSee('Minggu ini')
            ->assertSee('Minggu lepas')
            ->assertSee('QR paling kerap diimbas');
    }

    public function test_fama_qr_list_shows_access_count(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_fama'))
            ->get(route('fama.qr'))
            ->assertOk()
            ->assertSee('imbasan');
    }
}
