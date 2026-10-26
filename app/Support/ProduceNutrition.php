<?php

namespace App\Support;

/**
 * Demo nutrition per 100 g for seeded keluaran. Not an official FAMA laboratory record.
 */
class ProduceNutrition
{
    /**
     * @return list<string>
     */
    public static function names(): array
    {
        return [
            'Kalori',
            'Lemak jumlah',
            'Kolesterol',
            'Natrium',
            'Kalium',
            'Karbohidrat',
            'Serat',
            'Protein',
            'Vitamin C',
        ];
    }

    /**
     * @param  list<array{name?: string, amount?: string, dailyPercent?: string}>|null  $stored
     * @return list<array{name: string, amount: string, dailyPercent: string}>
     */
    public static function formRows(?array $stored): array
    {
        $byName = [];
        foreach ($stored ?? [] as $row) {
            $name = (string) ($row['name'] ?? '');
            if ($name !== '') {
                $byName[$name] = $row;
            }
        }

        return array_map(function (string $name) use ($byName) {
            return [
                'name' => $name,
                'amount' => (string) ($byName[$name]['amount'] ?? ''),
                'dailyPercent' => (string) ($byName[$name]['dailyPercent'] ?? ''),
            ];
        }, self::names());
    }

    /**
     * @return list<array{name: string, amount: string, dailyPercent: string}>|null
     */
    public static function defaultsFor(string $id, string $name): ?array
    {
        $catalog = self::catalog();
        if (isset($catalog[$id])) {
            return $catalog[$id];
        }

        $key = mb_strtolower(trim($name));
        $byName = [
            'durian' => 'pt_durian',
            'nangka' => 'pt_nangka',
            'manggis' => 'pt_manggis',
            'tembikai' => 'pt_tembikai',
            'nanas' => 'pt_nanas',
            'pisang' => 'pt_pisang',
            'betik' => 'pt_betik',
            'mangga' => 'pt_mangga',
            'lada benggala' => 'pt_lada_benggala',
            'jambu batu' => 'pt_jambu_batu',
            'tomato' => 'pt_tomato',
            'sawi' => 'pt_sawi',
        ];

        $match = $byName[$key] ?? null;

        return $match ? $catalog[$match] : null;
    }

    /**
     * @return array<string, list<array{name: string, amount: string, dailyPercent: string}>>
     */
    private static function catalog(): array
    {
        return [
            'pt_durian' => self::pack(['147 kcal', '7%'], ['5.3 g', '8%'], ['0 mg', '0%'], ['1 mg', '0%'], ['436 mg', '12%'], ['27.1 g', '9%'], ['3.8 g', '15%'], ['1.5 g', '3%'], ['19.7 mg', '33%']),
            'pt_nangka' => self::pack(['95 kcal', '5%'], ['0.6 g', '1%'], ['0 mg', '0%'], ['2 mg', '0%'], ['448 mg', '13%'], ['23.2 g', '8%'], ['1.5 g', '6%'], ['1.7 g', '3%'], ['13.7 mg', '23%']),
            'pt_manggis' => self::pack(['73 kcal', '4%'], ['0.6 g', '1%'], ['0 mg', '0%'], ['7 mg', '0%'], ['48 mg', '1%'], ['17.9 g', '6%'], ['1.8 g', '7%'], ['0.4 g', '1%'], ['2.9 mg', '5%']),
            'pt_tembikai' => self::pack(['30 kcal', '2%'], ['0.2 g', '0%'], ['0 mg', '0%'], ['1 mg', '0%'], ['112 mg', '3%'], ['7.6 g', '3%'], ['0.4 g', '2%'], ['0.6 g', '1%'], ['8.1 mg', '14%']),
            'pt_nanas' => self::pack(['50 kcal', '3%'], ['0.1 g', '0%'], ['0 mg', '0%'], ['1 mg', '0%'], ['109 mg', '3%'], ['13.1 g', '4%'], ['1.4 g', '6%'], ['0.5 g', '1%'], ['47.8 mg', '80%']),
            'pt_pisang' => self::pack(['89 kcal', '4%'], ['0.3 g', '0%'], ['0 mg', '0%'], ['1 mg', '0%'], ['358 mg', '10%'], ['22.8 g', '8%'], ['2.6 g', '10%'], ['1.1 g', '2%'], ['8.7 mg', '15%']),
            'pt_betik' => self::pack(['43 kcal', '2%'], ['0.3 g', '0%'], ['0 mg', '0%'], ['8 mg', '0%'], ['182 mg', '5%'], ['10.8 g', '4%'], ['1.7 g', '7%'], ['0.5 g', '1%'], ['60.9 mg', '102%']),
            'pt_mangga' => self::pack(['60 kcal', '3%'], ['0.4 g', '1%'], ['0 mg', '0%'], ['1 mg', '0%'], ['168 mg', '5%'], ['15 g', '5%'], ['1.6 g', '6%'], ['0.8 g', '2%'], ['36.4 mg', '61%']),
            'pt_lada_benggala' => self::pack(['31 kcal', '2%'], ['0.3 g', '0%'], ['0 mg', '0%'], ['4 mg', '0%'], ['211 mg', '6%'], ['6 g', '2%'], ['2.1 g', '8%'], ['1 g', '2%'], ['127.7 mg', '213%']),
            'pt_jambu_batu' => self::pack(['68 kcal', '3%'], ['1 g', '2%'], ['0 mg', '0%'], ['2 mg', '0%'], ['417 mg', '12%'], ['14.3 g', '5%'], ['5.4 g', '22%'], ['2.6 g', '5%'], ['228.3 mg', '381%']),
            'pt_tomato' => self::pack(['18 kcal', '1%'], ['0.2 g', '0%'], ['0 mg', '0%'], ['5 mg', '0%'], ['237 mg', '7%'], ['3.9 g', '1%'], ['1.2 g', '5%'], ['0.9 g', '2%'], ['13.7 mg', '23%']),
            'pt_sawi' => self::pack(['27 kcal', '1%'], ['0.4 g', '1%'], ['0 mg', '0%'], ['20 mg', '1%'], ['384 mg', '11%'], ['4.7 g', '2%'], ['3.2 g', '13%'], ['2.9 g', '6%'], ['70 mg', '117%']),
        ];
    }

    /**
     * @param  array{0: string, 1: string}  ...$pairs
     * @return list<array{name: string, amount: string, dailyPercent: string}>
     */
    private static function pack(array ...$pairs): array
    {
        $rows = [];
        foreach (self::names() as $index => $name) {
            $rows[] = [
                'name' => $name,
                'amount' => $pairs[$index][0],
                'dailyPercent' => $pairs[$index][1],
            ];
        }

        return $rows;
    }
}
