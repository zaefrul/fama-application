<?php

namespace Database\Seeders;

use App\Models\QrAccess;
use App\Models\QrCode;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QrAccessSeeder extends Seeder
{
    public function run(): void
    {
        if (QrAccess::query()->exists()) {
            return;
        }

        $qrs = QrCode::query()->get()->keyBy('id');
        if ($qrs->isEmpty()) {
            return;
        }

        $weights = [
            'qr_109' => [18, 21, 16, 24, 19, 11, 9, 17, 22, 20, 26, 18, 14, 12],
            'qr_015' => [9, 11, 8, 13, 10, 6, 5, 8, 12, 11, 14, 9, 7, 6],
            'qr_123' => [2, 1, 3, 2, 1, 0, 1, 2, 1, 3, 2, 1, 0, 1],
            'qr_011' => [1, 0, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1, 0, 0],
        ];

        $today = CarbonImmutable::now('Asia/Kuala_Lumpur')->startOfDay();
        $rows = [];
        $n = 1;

        foreach ($weights as $qrId => $dailyCounts) {
            $qr = $qrs->get($qrId);
            if (! $qr) {
                continue;
            }
            foreach ($dailyCounts as $offsetFromOldest => $count) {
                $day = $today->subDays(13 - $offsetFromOldest);
                for ($i = 0; $i < $count; $i++) {
                    $rows[] = [
                        'id' => 'acc_seed_'.str_pad((string) $n, 4, '0', STR_PAD_LEFT),
                        'qr_id' => $qr->id,
                        'qr_code' => $qr->qr_code,
                        'accessed_at' => $day->setTime(8 + ($i % 10), ($i * 7) % 60)->utc()->format('Y-m-d H:i:s'),
                    ];
                    $n++;
                }
            }
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('qr_accesses')->insert($chunk);
        }
    }
}
