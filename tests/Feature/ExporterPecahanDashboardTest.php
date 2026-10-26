<?php

namespace Tests\Feature;

use App\Domain\ApplicationStatus;
use App\Domain\LotDisposition;
use App\Domain\QrStatus;
use App\Models\ExportApplication;
use App\Models\QrCode;
use App\Models\User;
use App\Services\LotSplitService;
use App\Services\PecahanDashboard;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExporterPecahanDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(CarbonImmutable::parse('2026-10-24 10:00:00', 'Asia/Kuala_Lumpur'));
        $this->seed();
    }

    public function test_seeded_exporter_pecahan_stats_keep_units_and_roles(): void
    {
        $stats = app(PecahanDashboard::class)->forCompany('co_exporter_pisang');

        $this->assertTrue($stats['hasLots']);
        $this->assertSame(0, $stats['waiting']);
        $this->assertSame(2, $stats['holding']);
        $this->assertSame(0, $stats['soldThisMonth']);
        $this->assertSame(2, $stats['statusMix'][0]['count']);
        $this->assertSame('Pengimport', $stats['destinations'][0]['items'][0]['label']);
        $this->assertSame(250, $stats['destinations'][0]['items'][0]['count']);
        $this->assertSame('kg', $stats['destinations'][0]['unit']);
        $this->assertSame('GPL-QR-000203', $stats['balances'][0]['qrCode']);
        $this->assertSame(600, $stats['balances'][0]['remaining']);
        $this->assertSame(150, $stats['balances'][1]['remaining']);
        $this->assertSame(250, $stats['balances'][1]['sent']);
        $this->assertSame('Pisang', $stats['products'][0]['label']);
        $this->assertSame(750, $stats['products'][0]['remaining']);
        $this->assertSame(250, $stats['products'][0]['sent']);
        $this->assertSame(0, $stats['weeks'][0]['total']);
    }

    public function test_recent_split_sale_and_other_unit_stay_separate(): void
    {
        $holder = User::query()->findOrFail('user_eksport_pisang');
        $lots = app(LotSplitService::class);
        $lots->split('qr_pisang_exp_600', $holder, [[
            'quantity' => 10,
            'recipient_company_id' => 'co_contoh_pengimport',
        ]]);
        $lots->sellPortions('qr_pisang_exp_600', $holder, [20]);
        QrCode::query()->whereKey('qr_pisang_exp_400')->update(['accepted_at' => now()]);

        ExportApplication::query()->create([
            'id' => 'app_ekor',
            'application_no' => 'FAMA-TEST-EKOR',
            'company_id' => 'co_exporter_pisang',
            'produce_type_id' => 'pt_pisang',
            'variety' => 'Berangan',
            'grade' => 'A',
            'size' => 'L',
            'quantity' => 5,
            'quantity_unit' => 'ekor',
            'destination_country' => 'Singapura',
            'coc_number' => 'STB-EKOR',
            'farm_name' => 'Ladang Ujian',
            'importer_name' => 'Pembeli Ujian',
            'importer_address' => 'Kuala Lumpur',
            'status' => ApplicationStatus::Approved,
        ]);
        QrCode::query()->create([
            'id' => 'qr_ekor',
            'qr_code' => 'GPL-QR-009999',
            'application_id' => 'app_ekor',
            'root_application_id' => 'app_ekor',
            'holder_company_id' => 'co_exporter_pisang',
            'quantity' => 5,
            'quantity_remaining' => 5,
            'disposition' => LotDisposition::Holding,
            'public_slug' => 'GPL-QR-009999',
            'status' => QrStatus::Active,
            'generated_at' => now(),
            'activated_at' => now(),
        ]);

        $stats = app(PecahanDashboard::class)->forCompany('co_exporter_pisang');
        $roles = array_column($stats['destinations'][0]['items'], 'count', 'label');

        $this->assertSame(1, $stats['soldThisMonth']);
        $this->assertSame(1, $stats['statusMix'][3]['count']);
        $this->assertSame(260, $roles['Pengimport']);
        $this->assertSame(20, $roles['Pengguna']);
        $this->assertSame(['kg'], array_column($stats['destinations'], 'unit'));
        $this->assertSame(10, $stats['weeks'][0]['splitTotal']);
        $this->assertSame(20, $stats['weeks'][0]['soldTotal']);
        $this->assertSame(400, $stats['weeks'][0]['receivedTotal']);
        $currentWeek = collect($stats['weeks'][0]['rows'])->firstWhere('label', '19/10');
        $this->assertSame(400, $currentWeek['received']);
        $this->assertSame(30, $currentWeek['split'] + $currentWeek['sold']);

        $products = collect($stats['products'])->keyBy(fn (array $row) => $row['unit']);
        $this->assertSame(720, $products['kg']['remaining']);
        $this->assertSame(280, $products['kg']['sent']);
        $this->assertSame(5, $products['ekor']['remaining']);
        $this->assertSame(0, $products['ekor']['sent']);
    }

    public function test_exporter_dashboard_renders_pecahan_section(): void
    {
        $this->actingAs(User::query()->findOrFail('user_eksport_pisang'))
            ->get(route('exporter.dashboard'))
            ->assertOk()
            ->assertSee('Jualan lot')
            ->assertSee('Baki setiap lot')
            ->assertSee('Status jualan')
            ->assertSee('Ke mana kuantiti pergi')
            ->assertSee('Kuantiti mengikut produk')
            ->assertSee('Jualan mengikut minggu')
            ->assertSee('Didapat lawan dilepaskan')
            ->assertSee('Tiada kuantiti didapat atau dilepaskan dalam 8 minggu ini.')
            ->assertSee('Pengimport')
            ->assertSee('GPL-QR-000203')
            ->assertSee('Pisang')
            ->assertSee('Tiada jualan dalam 8 minggu ini.');
    }

    public function test_company_without_lots_sees_empty_pecahan_card(): void
    {
        $this->assertFalse(app(PecahanDashboard::class)->forCompany('missing-company')['hasLots']);

        $user = User::query()->findOrFail('user_eksport_pisang');
        $user->company_id = null;
        $user->save();

        $this->actingAs($user)
            ->get(route('exporter.dashboard'))
            ->assertOk()
            ->assertSee('Tiada lot dipegang atau dihantar lagi.')
            ->assertDontSee('Baki setiap lot');
    }
}
