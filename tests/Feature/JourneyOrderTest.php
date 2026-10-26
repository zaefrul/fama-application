<?php

namespace Tests\Feature;

use App\Domain\JourneyOrder;
use App\Domain\PartyType;
use App\Models\QrCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JourneyOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_seeded_perjalanan_follows_an_allowed_line(): void
    {
        $this->seed();

        $roots = QrCode::query()->whereNull('parent_id')->orderBy('qr_code')->get();
        $this->assertNotEmpty($roots);

        foreach ($roots as $root) {
            $tree = QrCode::treeForApplication($root->root_application_id ?: $root->application_id);
            $this->assertNotNull($tree, $root->qr_code);
            foreach ($tree->trackingPaths() as $path) {
                $roles = [];
                foreach (explode(' → ', $path) as $label) {
                    if ($label === 'Consumer') {
                        continue;
                    }
                    $party = collect(PartyType::cases())->first(fn (PartyType $type) => $type->label() === $label);
                    $this->assertNotNull($party, $path);
                    $roles[] = $party;
                }
                $this->assertTrue(JourneyOrder::allows($roles), $root->qr_code.' '.$path);
            }
        }
    }

    public function test_a_role_sequence_must_match_the_start_of_one_allowed_line(): void
    {
        $this->assertTrue(JourneyOrder::allows([
            PartyType::Supplier,
            PartyType::Exporter,
            PartyType::Importer,
        ]));
        $this->assertTrue(JourneyOrder::allows([
            PartyType::Supplier,
            PartyType::Retailer,
        ]));
        $this->assertFalse(JourneyOrder::allows([
            PartyType::Supplier,
            PartyType::Exporter,
            PartyType::Marketer,
        ]));
        $this->assertFalse(JourneyOrder::allows([
            PartyType::Retailer,
        ]));
    }
}
