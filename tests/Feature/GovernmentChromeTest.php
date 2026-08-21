<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GovernmentChromeTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_shows_official_government_chrome(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('LAMAN RASMI FAMA')
            ->assertSee('Kementerian Pertanian dan Keterjaminan Makanan')
            ->assertSee('Hak cipta terpelihara')
            ->assertSee('Portal rasmi jejak eksport hasil pertanian')
            ->assertSee('brand-logo-auth');
    }

    public function test_active_public_trace_shows_fama_verification(): void
    {
        $this->seed();

        $this->get('/trace/GPL-QR-000109')
            ->assertOk()
            ->assertSee('LAMAN RASMI FAMA')
            ->assertSee('Produk Disahkan Tulen')
            ->assertSee('Disahkan oleh FAMA')
            ->assertSee('/products/produce-tembikai-demo-01.jpg')
            ->assertSee('Sijil Jejak Produk Eksport')
            ->assertSee('trace-produce-hero')
            ->assertSee('/certificates/sijil-haccp-demo.svg')
            ->assertSee('/certificates/sijil-coc-demo.svg')
            ->assertSee('CONTOH')
            ->assertSee('Agensi Kerajaan')
            ->assertSee('/logos/logo-jata-negara.png')
            ->assertSee('/logos/logo-fama.png')
            ->assertSee('/logos/fama-jejak-gpl-logo-hd-1kpx.png')
            ->assertSee('Hak cipta terpelihara');
    }

    public function test_inactive_public_trace_does_not_claim_verification(): void
    {
        $this->seed();

        $this->get('/trace/GPL-QR-000123')
            ->assertOk()
            ->assertSee('LAMAN RASMI FAMA')
            ->assertSee('QR Belum Diaktifkan')
            ->assertDontSee('Produk Disahkan Tulen')
            ->assertDontSee('Disahkan oleh FAMA');
    }

    public function test_exporter_dashboard_uses_portal_shell(): void
    {
        $this->seed();

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->get(route('exporter.dashboard'))
            ->assertOk()
            ->assertSee('LAMAN RASMI FAMA')
            ->assertSee('Sistem Jejak GPL')
            ->assertSee('brand-logo-header')
            ->assertSee('Hak cipta terpelihara');
    }
}
