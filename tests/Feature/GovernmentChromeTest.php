<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\JejakService;
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

    public function test_active_public_trace_shows_sa_public_chrome(): void
    {
        $this->seed();

        $this->get('/trace/GPL-QR-000109')
            ->assertOk()
            ->assertDontSee('LAMAN RASMI FAMA')
            ->assertDontSee('Produk Disahkan Tulen')
            ->assertDontSee('Disahkan oleh FAMA')
            ->assertDontSee('Agensi Kerajaan')
            ->assertDontSee('/logos/logo-maha-2026.webp')
            ->assertSee('Maklumat Keluaran Pertanian')
            ->assertSee('Jenis Keluaran Pertanian')
            ->assertSee('Nama Syarikat')
            ->assertSee('Hubungi Kami')
            ->assertSee('www.fama.gov.my')
            ->assertSee('LOT-B07')
            ->assertSee('Kuala Selangor, Selangor')
            ->assertSee('/products/produce-tembikai-demo-01.jpg')
            ->assertSee('openstreetmap.org')
            ->assertSee('Profil Keluaran Pertanian')
            ->assertSee('trace-produce-hero')
            ->assertSee('trace-profile')
            ->assertSee('trace-produce-portrait')
            ->assertSee('/certificates/sijil-haccp-demo.svg')
            ->assertSee('/certificates/sijil-coc-demo.svg')
            ->assertSee('CONTOH')
            ->assertSee('/logos/logo-jata-negara.png')
            ->assertSee('/logos/logo-fama.png')
            ->assertSee('/logos/fama-jejak-gpl-logo-hd-1kpx.png')
            ->assertSee('Hak cipta terpelihara');
    }

    public function test_public_trace_hides_certificate_section_when_empty(): void
    {
        $this->seed();
        $service = app(JejakService::class);
        $fama = User::query()->findOrFail('user_fama');
        $company = $service->createCompany([
            'name' => 'Tiada Sijil Stall',
            'registration_no' => 'MH100099',
            'email' => '',
            'phone' => '',
            'address' => 'Serdang',
            'state' => 'Selangor',
            'district' => 'Serdang',
            'postcode' => '43400',
            'website' => '',
        ], $fama);
        $result = $service->createAndActivateQr($company->id, [
            'produce_type_id' => 'pt_durian',
            'variety' => 'Musang King',
            'grade' => 'A',
            'size' => 'L',
            'quantity' => 10,
            'quantity_unit' => 'kg',
            'destination_country' => 'MAHA',
            'coc_certificate_id' => null,
            'coc_number' => '',
            'export_date' => null,
            'farm_name' => 'Ladang Tanpa Sijil',
            'importer_name' => 'Pengunjung',
            'importer_address' => 'MAEPS',
        ], $fama);

        $this->get('/trace/'.$result['qr']->qr_code)
            ->assertOk()
            ->assertDontSee('>Sijil</h2>', false)
            ->assertDontSee('Contoh sijil yang biasa dipamer');
    }

    public function test_public_trace_hides_map_when_coordinates_are_incomplete(): void
    {
        $this->seed();
        $service = app(JejakService::class);
        $fama = User::query()->findOrFail('user_fama');
        $company = $service->createCompany([
            'name' => 'Koordinat Tidak Lengkap',
            'registration_no' => 'MH100098',
            'email' => '',
            'phone' => '',
            'address' => 'Serdang',
            'state' => 'Selangor',
            'district' => 'Serdang',
            'postcode' => '43400',
            'website' => '',
        ], $fama);
        $result = $service->createAndActivateQr($company->id, [
            'produce_type_id' => 'pt_durian',
            'variety' => 'Musang King',
            'grade' => 'A',
            'size' => 'L',
            'quantity' => 10,
            'quantity_unit' => 'kg',
            'destination_country' => 'MAHA',
            'coc_certificate_id' => null,
            'coc_number' => '',
            'export_date' => null,
            'farm_name' => 'Ladang Tanpa Peta',
            'farm_lat' => 3.1400000,
            'farm_lng' => null,
            'importer_name' => 'Pengunjung',
            'importer_address' => 'MAEPS',
        ], $fama);

        $this->get('/trace/'.$result['qr']->qr_code)
            ->assertOk()
            ->assertDontSee('openstreetmap.org')
            ->assertDontSee('farm-map');
    }

    public function test_inactive_public_trace_does_not_claim_verification(): void
    {
        $this->seed();

        $this->get('/trace/GPL-QR-000123')
            ->assertOk()
            ->assertDontSee('LAMAN RASMI FAMA')
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
            ->assertSee('Usahawan')
            ->assertSee('Maklumat Keluaran Pertanian')
            ->assertSee('brand-logo-header')
            ->assertSee('Hak cipta terpelihara');
    }
}
