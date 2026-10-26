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
        $this->assertSame(QrStatus::GeneratedInactive, $under[0]->status);
        $this->assertNull($under[0]->holder_company_id);
        $this->assertSame('co_retailer_pisang', $under[0]->pending_holder_company_id);
        $this->assertNull($under[0]->application_id);
        $this->assertSame('app_pisang', $under[0]->root_application_id);

        $service->split($lot->id, $holder, [[
            'quantity' => 500,
            'recipient_company_id' => 'co_marketer_pisang',
        ]]);

        $this->assertSame(0, $lot->fresh()->quantity_remaining);
        $this->assertSame(LotDisposition::Holding, $lot->fresh()->disposition);
    }

    public function test_recipient_scan_activates_the_lot_for_that_company(): void
    {
        $child = app(LotSplitService::class)->split('qr_pisang_exp_600', User::query()->findOrFail('user_eksport_pisang'), [[
            'quantity' => 80,
            'recipient_company_id' => 'co_retailer_pisang',
        ]])[0];

        $this->get('/trace/'.$child->qr_code)
            ->assertOk()
            ->assertSee('QR Menunggu Penerimaan')
            ->assertDontSee('Jejak jualan');

        $this->assertSame(QrStatus::GeneratedInactive, $child->fresh()->status);

        $this->actingAs(User::query()->findOrFail('user_eksport_pisang'))
            ->get('/trace/'.$child->qr_code)
            ->assertOk()
            ->assertSee('QR Menunggu Penerimaan');

        $this->assertNull($child->fresh()->holder_company_id);

        $this->actingAs(User::query()->findOrFail('user_peruncit_pisang'))
            ->get(route('exporter.qr'))
            ->assertOk()
            ->assertSee('QR jualan')
            ->assertSee('Menunggu penerimaan')
            ->assertSee($child->qr_code);

        $this->actingAs(User::query()->findOrFail('user_peruncit_pisang'))
            ->get('/trace/'.$child->qr_code)
            ->assertOk()
            ->assertSee('QR Menunggu Penerimaan')
            ->assertDontSee('Kod ini');

        $this->assertSame(QrStatus::GeneratedInactive, $child->fresh()->status);
        $this->assertNull($child->fresh()->holder_company_id);

        $this->actingAs(User::query()->findOrFail('user_peruncit_pisang'))
            ->get(route('exporter.lots'))
            ->assertOk()
            ->assertSee('Menunggu penerimaan')
            ->assertSee('Diterima')
            ->assertSee($child->qr_code);

        $this->actingAs(User::query()->findOrFail('user_peruncit_pisang'))
            ->post(route('exporter.lots.accept', $child))
            ->assertRedirect(route('exporter.lots.show', $child));

        $accepted = $child->fresh();
        $this->assertSame(QrStatus::Active, $accepted->status);
        $this->assertSame('co_retailer_pisang', $accepted->holder_company_id);
        $this->assertNotNull($accepted->accepted_at);

        $this->actingAs(User::query()->findOrFail('user_peruncit_pisang'))
            ->get('/trace/'.$child->qr_code)
            ->assertOk()
            ->assertSee('Pisang')
            ->assertSee('Runcit Segar Sdn. Bhd.')
            ->assertSee('Peruncit')
            ->assertDontSee('Kod ini')
            ->assertDontSee('QR Menunggu Penerimaan');

        $this->actingAs(User::query()->findOrFail('user_peruncit_pisang'))
            ->get(route('exporter.qr'))
            ->assertOk()
            ->assertSee('QR jualan')
            ->assertDontSee('Menunggu penerimaan');

        $this->assertNotNull(AuditLog::query()->where('action', 'LOT_ACCEPTED')->where('object_id', $child->id)->first());
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
            $this->assertSame('Jumlah jualan melebihi baki kuantiti', $e->getMessage());
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
            ->assertSee('Dari sumber ke tangan anda')
            ->assertSee('Pengeluar')
            ->assertSee('Pengeksport')
            ->assertSee('Pemborong')
            ->assertSee('Ladang Pisang Pontian')
            ->assertSee('Eksport Pisang Sdn. Bhd.')
            ->assertSee('Pemasar Buah Sdn. Bhd.')
            ->assertDontSee('GPL-QR-000201')
            ->assertDontSee('GPL-QR-000202')
            ->assertSee('GPL-QR-000204')
            ->assertDontSee('1,000')
            ->assertSee('400')
            ->assertSee('250')
            ->assertSee('Pontian, Johor')
            ->assertDontSee('trace-checkpoint')
            ->assertDontSee('Dokumen & sijil');

        $this->get('/trace/GPL-QR-000201')
            ->assertOk()
            ->assertSee('GPL-QR-000201')
            ->assertSee('Pengeluar')
            ->assertSee('Ladang Pisang Pontian')
            ->assertDontSee('Kod ini')
            ->assertSee('1,000')
            ->assertDontSee('Jualan kod ini');

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
            ->assertSee('Jejak jualan')
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
            ->get(route('exporter.lots.show', 'qr_pisang_root'))
            ->assertOk()
            ->assertSee('GPL-QR-000201')
            ->assertSee('GPL-QR-000204')
            ->assertSee('Pengeluar → Pengeksport → Pemborong');

        $this->actingAs(User::query()->findOrFail('user_eksport_pisang'))
            ->get(route('exporter.lots.show', 'qr_pisang_exp_400'))
            ->assertOk()
            ->assertSee('GPL-QR-000204')
            ->assertSee('Jejak jualan')
            ->assertSee('Pemborong · Pemasar Buah Sdn. Bhd.')
            ->assertSee('Peruncit · Runcit Segar Sdn. Bhd.')
            ->assertSee('Pengeksport · MTS Fruits Sdn. Bhd.')
            ->assertDontSee('Pengeluar · Ladang Pisang Pontian')
            ->assertSee('trace-checkpoint', false)
            ->assertSee('Baki');

        $this->actingAs(User::query()->findOrFail('user_ali'))
            ->get(route('exporter.lots.show', 'qr_pisang_root'))
            ->assertNotFound();

        $this->actingAs(User::query()->findOrFail('user_fama'))
            ->get(route('fama.applications.show', 'app_pisang'))
            ->assertOk()
            ->assertSee('Jualan lot')
            ->assertSee('trace-checkpoint', false)
            ->assertSee('GPL-QR-000201')
            ->assertSee('GPL-QR-000204')
            ->assertSee('Pemasar Buah Sdn. Bhd.');
    }

    public function test_split_rejects_an_upstream_or_unranked_recipient(): void
    {
        $service = app(LotSplitService::class);
        $exporter = User::query()->findOrFail('user_eksport_pisang');
        $before = QrCode::query()->findOrFail('qr_pisang_exp_600')->quantity_remaining;

        try {
            $service->split('qr_pisang_exp_600', $exporter, [[
                'quantity' => 10,
                'recipient_company_id' => 'co_supplier_pisang',
            ]]);
            $this->fail('Expected an upstream recipient to be rejected.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('ke hilir', $e->getMessage());
            $this->assertStringContainsString('Pengeluar', $e->getMessage());
        }

        Company::query()->findOrFail('co_demo_utara')->update([
            'party_type' => PartyType::Other,
            'party_label' => 'Pengumpul',
        ]);

        try {
            $service->split('qr_pisang_exp_600', $exporter, [[
                'quantity' => 10,
                'recipient_company_id' => 'co_demo_utara',
            ]]);
            $this->fail('Expected an unranked recipient to be rejected.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('ke hilir', $e->getMessage());
        }

        $this->assertSame($before, QrCode::query()->findOrFail('qr_pisang_exp_600')->quantity_remaining);

        $sameRank = $service->split('qr_pisang_exp_600', $exporter, [[
            'quantity' => 10,
            'recipient_company_id' => 'co_mts',
        ]]);
        $this->assertSame('co_mts', $sameRank[0]->pending_holder_company_id);

        $this->actingAs(User::query()->findOrFail('user_pemasar_pisang'))
            ->get(route('exporter.lots.show', 'qr_pisang_mkt_250'))
            ->assertOk()
            ->assertSee('Peruncit · Runcit Segar Sdn. Bhd.')
            ->assertDontSee('Pengeksport · Eksport Pisang Sdn. Bhd.')
            ->assertDontSee('Pengeluar · Ladang Pisang Pontian');
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

    public function test_splitter_sees_each_sale_as_its_own_chain_and_receiver_sees_one_source(): void
    {
        $this->actingAs(User::query()->findOrFail('user_contoh_pemborong'))
            ->get(route('exporter.lots.show', 'qr_contoh_mangga_borong'))
            ->assertOk()
            ->assertSee('GPL-QR-000923')
            ->assertSee('GPL-QR-000925')
            ->assertSee('GPL-QR-000927')
            ->assertSee('trace-fork', false)
            ->assertDontSee('GPL-QR-000924');

        $this->actingAs(User::query()->findOrFail('user_contoh_peruncit'))
            ->get(route('exporter.lots.show', 'qr_contoh_mangga_runcit'))
            ->assertOk()
            ->assertSee('GPL-QR-000921')
            ->assertSee('GPL-QR-000925')
            ->assertSee('GPL-QR-000926')
            ->assertDontSee('GPL-QR-000927')
            ->assertDontSee('GPL-QR-000924')
            ->assertDontSee('Import Contoh Niaga')
            ->assertDontSee('trace-fork', false);

        $this->get('/trace/GPL-QR-000925')
            ->assertOk()
            ->assertSee('Kedai Contoh Runcit')
            ->assertSee('Borong Contoh Segar')
            ->assertDontSee('Import Contoh Niaga')
            ->assertDontSee('trace-fork', false);
    }
}
