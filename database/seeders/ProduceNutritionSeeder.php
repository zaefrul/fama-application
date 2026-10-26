<?php

namespace Database\Seeders;

use App\Domain\ProductKind;
use App\Models\ProduceType;
use App\Support\ProduceNutrition;
use Illuminate\Database\Seeder;

class ProduceNutritionSeeder extends Seeder
{
    public function run(): void
    {
        ProduceType::query()
            ->where('category', ProductKind::Produce->value)
            ->orderBy('name')
            ->each(function (ProduceType $type): void {
                if (is_array($type->nutrition) && $type->nutrition !== []) {
                    return;
                }
                $rows = ProduceNutrition::defaultsFor($type->id, $type->name);
                if ($rows === null) {
                    return;
                }
                $type->nutrition = $rows;
                $type->save();
            });
    }
}
