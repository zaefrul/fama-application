<?php

namespace Database\Seeders;

use App\Domain\ApplicationStatus;
use App\Domain\LotDisposition;
use App\Domain\Market;
use App\Domain\ProductKind;
use App\Domain\QrStatus;
use App\Models\Approval;
use App\Models\CompanyProduce;
use App\Models\ExportApplication;
use App\Models\GalleryItem;
use App\Models\ProduceType;
use App\Models\QrCode;
use Illuminate\Database\Seeder;

/**
 * Six produce photos, each with one straight lot line:
 * Pengeluar → Pengeksport → Pemborong → Pengimport → Peruncit.
 */
class PresentationJourneySeeder extends Seeder
{
    public function run(): void
    {
        $stops = [
            'co_contoh_pengeluar',
            'co_contoh_pengeksport',
            'co_contoh_pemborong',
            'co_contoh_pengimport',
            'co_contoh_peruncit',
        ];

        $products = [
            ['key' => 'nangka', 'produce_id' => 'pt_nangka', 'name' => 'Nangka', 'variety' => 'Tekam', 'grade' => 'A', 'size' => 'L', 'image' => 'nangka.png', 'qty' => [1000, 700, 500, 320, 180], 'lot' => 'LOT-DEMO-01', 'destination' => 'Singapura', 'day' => '2026-09-08'],
            ['key' => 'lada', 'produce_id' => 'pt_lada_benggala', 'name' => 'Lada Benggala', 'variety' => 'Tiga Warna', 'grade' => 'A', 'size' => 'M', 'image' => 'lada-benggala.png', 'qty' => [800, 560, 400, 260, 140], 'lot' => 'LOT-DEMO-02', 'destination' => 'China', 'day' => '2026-09-10'],
            ['key' => 'jambu', 'produce_id' => 'pt_jambu_batu', 'name' => 'Jambu Batu', 'variety' => 'Merah', 'grade' => 'Premium', 'size' => 'L', 'image' => 'jambu-batu.png', 'qty' => [900, 640, 460, 300, 160], 'lot' => 'LOT-DEMO-03', 'destination' => 'Brunei', 'day' => '2026-09-12'],
            ['key' => 'tomato', 'produce_id' => 'pt_tomato', 'name' => 'Tomato', 'variety' => 'Merah', 'grade' => 'A', 'size' => 'M', 'image' => 'tomato.png', 'qty' => [750, 520, 360, 220, 120], 'lot' => 'LOT-DEMO-04', 'destination' => 'Thailand', 'day' => '2026-09-15'],
            ['key' => 'sawi', 'produce_id' => 'pt_sawi', 'name' => 'Sawi', 'variety' => 'Hijau', 'grade' => 'A', 'size' => 'S', 'image' => 'sawi.png', 'qty' => [600, 420, 300, 180, 90], 'lot' => 'LOT-DEMO-05', 'destination' => 'Singapura', 'day' => '2026-09-18'],
            ['key' => 'tembikai', 'produce_id' => 'pt_tembikai', 'name' => 'Tembikai', 'variety' => 'Merah', 'grade' => 'A', 'size' => 'L', 'image' => 'tembikai.png', 'qty' => [1100, 800, 560, 360, 200], 'lot' => 'LOT-DEMO-06', 'destination' => 'Brunei', 'day' => '2026-09-20'],
        ];

        $number = 1101;
        foreach ($products as $product) {
            ProduceType::query()->updateOrCreate(
                ['id' => $product['produce_id']],
                ['name' => $product['name'], 'category' => 'PRODUCE'],
            );
            $produceRow = CompanyProduce::query()
                ->where('company_id', 'co_contoh_pengeluar')
                ->where('produce_type_id', $product['produce_id'])
                ->first();
            if ($produceRow === null) {
                CompanyProduce::query()->create([
                    'id' => 'cp_demo_'.$product['key'],
                    'company_id' => 'co_contoh_pengeluar',
                    'produce_type_id' => $product['produce_id'],
                    'variety' => $product['variety'],
                    'active' => true,
                ]);
            }

            $image = '/products/demo-sample/'.$product['image'];
            $appId = 'app_demo_'.$product['key'];
            ExportApplication::query()->updateOrCreate(
                ['id' => $appId],
                [
                    'application_no' => 'FAMA-2026-'.str_pad((string) $number, 6, '0', STR_PAD_LEFT),
                    'company_id' => 'co_contoh_pengeluar',
                    'produce_type_id' => $product['produce_id'],
                    'product_kind' => ProductKind::Produce,
                    'market' => Market::Export,
                    'variety' => $product['variety'],
                    'grade' => $product['grade'],
                    'size' => $product['size'],
                    'quantity' => $product['qty'][0],
                    'quantity_unit' => 'kg',
                    'destination_country' => $product['destination'],
                    'coc_certificate_id' => 'cert_contoh_coc',
                    'coc_number' => 'COC-CONTOH-2026-01',
                    'export_date' => $product['day'],
                    'lot_no' => $product['lot'],
                    'farm_location' => 'Kuantan, Pahang',
                    'farm_lat' => 3.8168,
                    'farm_lng' => 103.3317,
                    'display_image_path' => $image,
                    'farm_name' => 'Ladang Contoh Hijau',
                    'importer_name' => 'Import Contoh Niaga Sdn. Bhd.',
                    'importer_address' => '24, Jalan Contoh, Petaling Jaya, Selangor',
                    'status' => ApplicationStatus::Approved,
                    'submitted_at' => $product['day'].' 08:00:00',
                    'reviewed_at' => $product['day'].' 11:00:00',
                ],
            );
            Approval::query()->updateOrCreate(
                ['id' => 'appr_demo_'.$product['key']],
                [
                    'application_id' => $appId,
                    'officer_user_id' => 'user_fama',
                    'decision' => 'APPROVED',
                    'remarks' => 'Diluluskan untuk demo perjalanan lot.',
                    'decided_at' => $product['day'].' 11:00:00',
                ],
            );
            GalleryItem::query()->updateOrCreate(
                ['id' => 'gal_demo_'.$product['key']],
                [
                    'company_id' => 'co_contoh_pengeluar',
                    'category' => 'BUAH',
                    'description' => $product['name'],
                    'file_path' => $image,
                    'uploaded_by' => 'user_contoh_pengeluar',
                    'uploaded_at' => $product['day'].' 09:00:00',
                ],
            );

            $parentId = null;
            foreach ($stops as $index => $companyId) {
                $code = 'GPL-QR-'.str_pad((string) ($number + $index), 6, '0', STR_PAD_LEFT);
                $nextQty = $product['qty'][$index + 1] ?? 0;
                $when = \Carbon\Carbon::parse($product['day'])->addDays($index * 4)->format('Y-m-d').' 10:00:00';
                QrCode::query()->updateOrCreate(
                    ['id' => 'qr_demo_'.$product['key'].'_'.$index],
                    [
                        'qr_code' => $code,
                        'application_id' => $index === 0 ? $appId : null,
                        'parent_id' => $parentId,
                        'root_application_id' => $appId,
                        'holder_company_id' => $companyId,
                        'pending_holder_company_id' => $index === 0 ? null : $companyId,
                        'quantity' => $product['qty'][$index],
                        'quantity_remaining' => $product['qty'][$index] - $nextQty,
                        'disposition' => LotDisposition::Holding,
                        'status' => QrStatus::Active,
                        'public_slug' => $code,
                        'generated_at' => $when,
                        'activated_at' => $when,
                        'accepted_at' => $index === 0 ? null : $when,
                        'sold_at' => null,
                    ],
                );
                $parentId = 'qr_demo_'.$product['key'].'_'.$index;
            }

            $number += 5;
        }
    }
}
