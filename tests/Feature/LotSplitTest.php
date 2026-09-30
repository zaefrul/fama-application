<?php

namespace Tests\Feature;

use App\Domain\LotDisposition;
use App\Domain\PartyType;
use App\Domain\QrStatus;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\QrCode;
use App\Models\User;
use App\Services\LotSplitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class LotSplitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_split_under_and_equal_to_remaining_quantity(): void
    {
        $service = app(LotSplitService::class);
        $holder = User::query()->findOrFail('user_eksport_pisang');
        $lot = QrCode::query()->findOrFail('qr_pisang_exp_600');

        $under = $service->split($lot->id, $holder, [[
            'quantity' => 100,
            'recipient_company_id' => 'co_retailer_pisang',
        ]]);

        $this->assertCount(1, $under);
        $this->assertSame(500, $lot->fresh()->quantity_remaining);
        $this->assertSame(QrStatus::Active, $under[0]->status);
        $this->assertSame('co_retailer_pisang', $under[0]->holder_company_id);
        $this->assertNull($under[0]->application_id);
        $this->assertSame('app_pisang', $under[0]->root_application_id);

        $service->split($lot->id, $holder, [[
            'quantity' => 500,
            'recipient_company_id' => 'co_marketer_pisang',
        ]]);

        $this->assertSame(0, $lot->fresh()->quantity_remaining);
        $this->assertSame(LotDisposition::Holding, $lot->fresh()->disposition);
    }

    public function test_split_over_remaining_quantity_is_rejected(): void
    {
        $lot = QrCode::query()->findOrFail('qr_pisang_exp_600');

        try {
            app(LotSplitService::class)->split($lot->id, User::query()->findOrFail('user_eksport_pisang'), [[
                'quantity' => 601,
                'recipient_company_id' => 'co_retailer_pisang',
            ]]);
            $this->fail('Expected the split to be rejected.');
        } catch (RuntimeException $e) {
            $this->assertSame('Jumlah pecahan melebihi baki kuantiti', $e->getMessage());
        }

        $this->assertSame(600, $lot->fresh()->quantity_remaining);
        $this->assertSame(0, QrCode::query()->where('parent_id', $lot->id)->count());
    }

    public function test_a_different_company_cannot_split(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Hanya pemegang lot semasa boleh memecahkan atau menjual lot ini');

        app(LotSplitService::class)->split('qr_pisang_root', User::query()->findOrFail('user_ali'), [[
            'quantity' => 1,
            'recipient_company_id' => 'co_abc',
        ]]);
    }

    public function test_inactive_root_cannot_split(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Lot belum aktif');

        app(LotSplitService::class)->split('qr_123', User::query()->findOrFail('user_ali'), [[
            'quantity' => 1,
            'recipient_company_id' => 'co_mts',
        ]]);
    }

    public function test_child_public_payload_includes_root_product_and_ancestor_chain(): void
    {
        $this->get('/trace/GPL-QR-000204')
            ->assertOk()
            ->assertSee('Pisang')
            ->assertSee('Jejak pecahan')
            ->assertSee('trace-checkpoint', false)
            ->assertSee('GPL-QR-000201')
            ->assertSee('Ladang Pisang Pontian')
            ->assertSee('Pontian, Johor');

        $this->getJson('/api/public/trace/GPL-QR-000204')
            ->assertOk()
            ->assertJsonPath('produce', 'Pisang')
            ->assertJsonPath('quantity', 250)
            ->assertJsonPath('trunkQuantity', 1000)
            ->assertJsonPath('chain.0.qrCode', 'GPL-QR-000201')
            ->assertJsonPath('chain.1.qrCode', 'GPL-QR-000202')
            ->assertJsonPath('chain.2.qrCode', 'GPL-QR-000204');
    }

    public function test_existing_single_qr_still_renders(): void
    {
        $this->get('/trace/GPL-QR-000109')
            ->assertOk()
            ->assertSee('Tembikai')
            ->assertSee('Jejak pecahan')
            ->assertSee('GPL-QR-000109');

        $this->getJson('/api/public/trace/GPL-QR-000109')
            ->assertOk()
            ->assertJsonPath('active', true)
            ->assertJsonPath('produce', 'Tembikai')
            ->assertJsonCount(1, 'chain');
    }

    public function test_sold_lot_cannot_be_split_again(): void
    {
        $service = app(LotSplitService::class);
        $holder = User::query()->findOrFail('user_pemasar_pisang');
        $service->sellEntire('qr_pisang_mkt_250', $holder);

        $sold = QrCode::query()->findOrFail('qr_pisang_mkt_250');
        $this->assertSame(LotDisposition::Sold, $sold->disposition);
        $this->assertSame(0, $sold->quantity_remaining);
        $this->assertNotNull($sold->sold_at);

        $audit = AuditLog::query()->where('action', 'LOT_SOLD')->where('object_id', $sold->id)->first();
        $this->assertNotNull($audit);
        $this->assertStringContainsString('250', (string) $audit->before_json);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Lot yang telah dijual tidak boleh dipecahkan');
        $service->split($sold->id, $holder, [[
            'quantity' => 1,
            'recipient_company_id' => 'co_retailer_pisang',
        ]]);
    }

    public function test_partial_sale_creates_a_terminal_child_qr(): void
    {
        $children = app(LotSplitService::class)->sellPortions(
            'qr_pisang_exp_600',
            User::query()->findOrFail('user_eksport_pisang'),
            [40],
        );

        $this->assertCount(1, $children);
        $this->assertSame(LotDisposition::Sold, $children[0]->disposition);
        $this->assertNull($children[0]->holder_company_id);
        $this->assertSame(0, $children[0]->quantity_remaining);
        $this->assertSame(560, QrCode::query()->findOrFail('qr_pisang_exp_600')->quantity_remaining);

        $this->getJson('/api/public/trace/'.$children[0]->qr_code)
            ->assertOk()
            ->assertJsonPath('produce', 'Pisang')
            ->assertJsonPath('chain.0.qrCode', 'GPL-QR-000201')
            ->assertJsonPath('disposition', 'SOLD');
    }

    public function test_holder_can_open_the_lot_screen_and_fama_sees_the_tree(): void
    {
        $this->actingAs(User::query()->findOrFail('user_supplier_pisang'))
            ->get(route('exporter.lots'))
            ->assertOk()
            ->assertSee('GPL-QR-000201')
            ->assertSee('Lot asal');

        $this->actingAs(User::query()->findOrFail('user_eksport_pisang'))
            ->get(route('exporter.lots.show', 'qr_pisang_exp_400'))
            ->assertOk()
            ->assertSee('GPL-QR-000204')
            ->assertSee('Jejak pecahan')
            ->assertSee('trace-checkpoint', false)
            ->assertSee('Baki');

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->get(route('exporter.lots.show', 'qr_pisang_root'))
            ->assertNotFound();

        $this->actingAs(User::query()->findOrFail('user_fama'))
            ->get(route('fama.applications.show', 'app_pisang'))
            ->assertOk()
            ->assertSee('Pecahan lot')
            ->assertSee('trace-checkpoint', false)
            ->assertSee('GPL-QR-000201')
            ->assertSee('GPL-QR-000204')
            ->assertSee('Pemasar Buah Sdn. Bhd.');
    }

    public function test_registration_stores_party_type_on_the_company(): void
    {
        $this->post('/auth/register/exporter', [
            'identifiers' => ['H0B00003'],
            'name' => 'Demo Pemasar',
            'identityReference' => '900101011111',
            'password' => 'Exporter123!',
            'confirmPassword' => 'Exporter123!',
            'party_type' => 'OTHER',
            'party_label' => 'Pengumpul',
        ])->assertRedirect(route('exporter.dashboard'));

        $company = Company::query()->findOrFail('co_demo_utara');
        $this->assertSame(PartyType::Other, $company->party_type);
        $this->assertSame('Pengumpul', $company->party_label);
        $this->assertSame('Pengumpul', $company->partyLabel());
    }
}
