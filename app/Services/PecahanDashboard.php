<?php

namespace App\Services;

use App\Domain\LotDisposition;
use App\Domain\PartyType;
use App\Domain\QrStatus;
use App\Models\QrCode;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Aggregates for the usahawan dashboard. Quantities are never added across units.
 */
class PecahanDashboard
{
    private const ZONE = 'Asia/Kuala_Lumpur';

    private const BALANCE_LIMIT = 6;

    private const WEEK_COUNT = 8;

    /**
     * @return array{
     *     hasLots: bool,
     *     waiting: int,
     *     holding: int,
     *     soldThisMonth: int,
     *     statusMix: list<array{label: string, count: int, tone: string}>,
     *     destinations: list<array{unit: string, items: list<array{label: string, count: int, percent: int, color: int}>}>,
     *     balances: list<array{id: string, qrCode: string, produce: string, remaining: int, sent: int, quantity: int, unit: string}>,
     *     balanceMore: int,
     *     products: list<array{label: string, unit: string, remaining: int, sent: int, total: int, percent: int}>,
     *     weeks: list<array{unit: string|null, total: int, splitTotal: int, soldTotal: int, receivedTotal: int, rows: list<array{label: string, split: int, sold: int, received: int, splitPercent: int, soldPercent: int}>}>
     * }
     */
    public function forCompany(?string $companyId): array
    {
        if ($companyId === null || $companyId === '') {
            return $this->empty();
        }

        $held = QrCode::query()
            ->with(['rootApplication.produceType', 'application.produceType'])
            ->where('holder_company_id', $companyId)
            ->get();
        $incoming = QrCode::query()
            ->where('pending_holder_company_id', $companyId)
            ->whereNull('holder_company_id')
            ->where('status', QrStatus::GeneratedInactive)
            ->get();
        $outgoing = QrCode::query()
            ->with(['holder', 'pendingHolder', 'rootApplication.produceType', 'application.produceType'])
            ->whereHas('parent', fn ($query) => $query->where('holder_company_id', $companyId))
            ->get();

        if ($held->isEmpty() && $incoming->isEmpty() && $outgoing->isEmpty()) {
            return $this->empty();
        }

        $childQuantity = [];
        foreach ($outgoing as $child) {
            $childQuantity[$child->parent_id] = ($childQuantity[$child->parent_id] ?? 0) + (int) $child->quantity;
        }

        $holding = 0;
        $splitEmpty = 0;
        $soldLots = 0;
        foreach ($held as $lot) {
            if ($lot->disposition === LotDisposition::Sold) {
                $soldLots++;
            } elseif ((int) $lot->quantity_remaining > 0) {
                $holding++;
            } else {
                $splitEmpty++;
            }
        }
        foreach ($outgoing as $child) {
            if ($child->disposition === LotDisposition::Sold) {
                $soldLots++;
            }
        }

        $monthStart = CarbonImmutable::now(self::ZONE)->startOfMonth()->utc();
        $soldThisMonth = 0;
        foreach ($held as $lot) {
            if ($this->soldInMonth($lot, $monthStart) && $lot->holder_company_id === $companyId) {
                $soldThisMonth++;
            }
        }
        foreach ($outgoing as $child) {
            if ($this->soldInMonth($child, $monthStart)) {
                $soldThisMonth++;
            }
        }

        $balances = $this->balances($held);
        $byRole = [];
        $weeks = $this->emptyWeeks();
        foreach ($outgoing as $child) {
            $quantity = (int) $child->quantity;
            if ($quantity < 1) {
                continue;
            }
            $unit = $this->unit($child);
            $role = $this->destinationRole($child);
            $byRole[$unit][$role] = ($byRole[$unit][$role] ?? 0) + $quantity;
            $when = $child->disposition === LotDisposition::Sold
                ? ($child->sold_at ?? $child->generated_at)
                : $child->generated_at;
            $this->addWeek($weeks, $when, $unit, $child->disposition === LotDisposition::Sold ? 'sold' : 'split', $quantity);
        }

        foreach ($held as $lot) {
            if ($lot->disposition !== LotDisposition::Sold) {
                continue;
            }
            $remainder = max(0, (int) $lot->quantity - ($childQuantity[$lot->id] ?? 0));
            if ($remainder < 1) {
                continue;
            }
            $unit = $this->unit($lot);
            $byRole[$unit]['Pengguna'] = ($byRole[$unit]['Pengguna'] ?? 0) + $remainder;
            $this->addWeek($weeks, $lot->sold_at, $unit, 'sold', $remainder);
        }

        foreach ($held as $lot) {
            if ($lot->parent_id === null) {
                continue;
            }
            $this->addWeek(
                $weeks,
                $lot->accepted_at ?? $lot->activated_at,
                $this->unit($lot),
                'received',
                (int) $lot->quantity,
            );
        }

        return [
            'hasLots' => true,
            'waiting' => $incoming->count(),
            'holding' => $holding,
            'soldThisMonth' => $soldThisMonth,
            'statusMix' => [
                ['label' => 'Dipegang', 'count' => $holding, 'tone' => 'success'],
                ['label' => 'Menunggu penerimaan', 'count' => $incoming->count(), 'tone' => 'warning'],
                ['label' => 'Habis dipecah', 'count' => $splitEmpty, 'tone' => 'info'],
                ['label' => 'Dijual', 'count' => $soldLots, 'tone' => 'sold'],
            ],
            'destinations' => $this->destinationGroups($byRole),
            'balances' => $balances['rows'],
            'balanceMore' => $balances['more'],
            'products' => $this->products($held),
            'weeks' => $this->weekSeries($weeks),
        ];
    }

    /**
     * @param  Collection<int, QrCode>  $held
     * @return array{rows: list<array{id: string, qrCode: string, produce: string, remaining: int, sent: int, quantity: int, unit: string}>, more: int}
     */
    private function balances(Collection $held): array
    {
        $rows = $held
            ->filter(fn (QrCode $lot) => (int) $lot->quantity > 0)
            ->sort(function (QrCode $a, QrCode $b) {
                $remaining = (int) $b->quantity_remaining <=> (int) $a->quantity_remaining;

                return $remaining !== 0 ? $remaining : strcmp((string) $a->qr_code, (string) $b->qr_code);
            })
            ->values();

        $visible = $rows->take(self::BALANCE_LIMIT)->map(fn (QrCode $lot) => [
            'id' => $lot->id,
            'qrCode' => $lot->qr_code,
            'produce' => $this->produceName($lot),
            'remaining' => (int) $lot->quantity_remaining,
            'sent' => max(0, (int) $lot->quantity - (int) $lot->quantity_remaining),
            'quantity' => (int) $lot->quantity,
            'unit' => $this->unit($lot),
        ])->all();

        return [
            'rows' => $visible,
            'more' => max(0, $rows->count() - self::BALANCE_LIMIT),
        ];
    }

    /**
     * @param  Collection<int, QrCode>  $held
     * @return list<array{label: string, unit: string, remaining: int, sent: int, total: int, percent: int}>
     */
    private function products(Collection $held): array
    {
        $groups = [];
        foreach ($held as $lot) {
            $quantity = (int) $lot->quantity;
            if ($quantity < 1) {
                continue;
            }
            $unit = $this->unit($lot);
            $label = $this->produceName($lot);
            $key = $label.'|'.$unit;
            $remaining = (int) $lot->quantity_remaining;
            $groups[$key] ??= ['label' => $label, 'unit' => $unit, 'remaining' => 0, 'sent' => 0];
            $groups[$key]['remaining'] += $remaining;
            $groups[$key]['sent'] += max(0, $quantity - $remaining);
        }

        $rows = [];
        foreach ($groups as $group) {
            $total = $group['remaining'] + $group['sent'];
            if ($total < 1) {
                continue;
            }
            $rows[] = [
                'label' => $group['label'],
                'unit' => $group['unit'],
                'remaining' => $group['remaining'],
                'sent' => $group['sent'],
                'total' => $total,
                'percent' => 0,
            ];
        }

        usort($rows, fn (array $a, array $b) => $b['total'] <=> $a['total'] ?: strcmp($a['label'], $b['label']));
        $rows = array_slice($rows, 0, 6);
        $max = max([1, ...array_column($rows, 'total')]);
        foreach ($rows as $index => $row) {
            $rows[$index]['percent'] = (int) round(($row['total'] / $max) * 100);
        }

        return $rows;
    }

    /**
     * @param  array<string, array<string, int>>  $byRole
     * @return list<array{unit: string, items: list<array{label: string, count: int, percent: int, color: int}>}>
     */
    private function destinationGroups(array $byRole): array
    {
        ksort($byRole);
        $groups = [];
        foreach ($byRole as $unit => $roles) {
            uksort($roles, fn (string $a, string $b) => $this->roleOrder($a) <=> $this->roleOrder($b) ?: strcmp($a, $b));
            $max = max([1, ...array_values($roles)]);
            $items = [];
            $color = 1;
            foreach ($roles as $label => $count) {
                if ($count < 1) {
                    continue;
                }
                $items[] = [
                    'label' => $label,
                    'count' => $count,
                    'percent' => (int) round(($count / $max) * 100),
                    'color' => $color,
                ];
                $color = $color === 10 ? 1 : $color + 1;
            }
            if ($items !== []) {
                $groups[] = ['unit' => $unit, 'items' => $items];
            }
        }

        return $groups;
    }

    /**
     * @return array<string, array{split: array<string, int>, sold: array<string, int>}>
     */
    private function emptyWeeks(): array
    {
        $start = CarbonImmutable::now(self::ZONE)->startOfDay()->startOfWeek(CarbonImmutable::MONDAY)->subWeeks(self::WEEK_COUNT - 1);
        $weeks = [];
        for ($i = 0; $i < self::WEEK_COUNT; $i++) {
            $weeks[$start->addWeeks($i)->toDateString()] = ['split' => [], 'sold' => [], 'received' => []];
        }

        return $weeks;
    }

    /**
     * @param  array<string, array{split: array<string, int>, sold: array<string, int>}>  $weeks
     */
    private function addWeek(array &$weeks, mixed $at, string $unit, string $kind, int $quantity): void
    {
        if ($quantity < 1 || ! $at instanceof CarbonInterface) {
            return;
        }

        $key = CarbonImmutable::parse($at)->timezone(self::ZONE)->startOfWeek(CarbonImmutable::MONDAY)->toDateString();
        if (! isset($weeks[$key])) {
            return;
        }

        $weeks[$key][$kind][$unit] = ($weeks[$key][$kind][$unit] ?? 0) + $quantity;
    }

    /**
     * @param  array<string, array{split: array<string, int>, sold: array<string, int>, received: array<string, int>}>  $weeks
     * @return list<array{unit: string|null, total: int, splitTotal: int, soldTotal: int, receivedTotal: int, rows: list<array{label: string, split: int, sold: int, received: int, splitPercent: int, soldPercent: int}>}>
     */
    private function weekSeries(array $weeks): array
    {
        $units = [];
        foreach ($weeks as $bucket) {
            foreach (['split', 'sold', 'received'] as $kind) {
                foreach ($bucket[$kind] ?? [] as $unit => $quantity) {
                    $units[$unit] = true;
                }
            }
        }

        if ($units === []) {
            return [[
                'unit' => null,
                'total' => 0,
                'splitTotal' => 0,
                'soldTotal' => 0,
                'receivedTotal' => 0,
                'rows' => [],
            ]];
        }

        ksort($units);
        $series = [];
        foreach (array_keys($units) as $unit) {
            $rows = [];
            $splitTotal = 0;
            $soldTotal = 0;
            $receivedTotal = 0;
            foreach ($weeks as $date => $bucket) {
                $split = (int) ($bucket['split'][$unit] ?? 0);
                $sold = (int) ($bucket['sold'][$unit] ?? 0);
                $received = (int) ($bucket['received'][$unit] ?? 0);
                $splitTotal += $split;
                $soldTotal += $sold;
                $receivedTotal += $received;
                $rows[] = [
                    'label' => CarbonImmutable::parse($date, self::ZONE)->format('j/n'),
                    'split' => $split,
                    'sold' => $sold,
                    'received' => $received,
                    'splitPercent' => 0,
                    'soldPercent' => 0,
                ];
            }
            $max = max([1, ...array_map(fn (array $row) => $row['split'] + $row['sold'], $rows)]);
            foreach ($rows as $index => $row) {
                $rows[$index]['splitPercent'] = (int) round(($row['split'] / $max) * 100);
                $rows[$index]['soldPercent'] = (int) round(($row['sold'] / $max) * 100);
            }
            $series[] = [
                'unit' => $unit,
                'total' => $splitTotal + $soldTotal,
                'splitTotal' => $splitTotal,
                'soldTotal' => $soldTotal,
                'receivedTotal' => $receivedTotal,
                'rows' => $rows,
            ];
        }

        return $series;
    }

    private function soldInMonth(QrCode $lot, CarbonImmutable $monthStartUtc): bool
    {
        return $lot->disposition === LotDisposition::Sold
            && $lot->sold_at instanceof CarbonInterface
            && $lot->sold_at->greaterThanOrEqualTo($monthStartUtc);
    }

    private function destinationRole(QrCode $child): string
    {
        if ($child->disposition === LotDisposition::Sold) {
            return 'Pengguna';
        }

        $company = $child->holder ?? $child->pendingHolder;

        return $company?->partyLabel() ?? 'Pengguna';
    }

    private function roleOrder(string $label): int
    {
        if ($label === 'Pengguna') {
            return 90;
        }

        foreach (PartyType::cases() as $type) {
            if ($type->label() === $label && $type->rank() !== null) {
                return (int) $type->rank();
            }
        }

        return 50;
    }

    private function unit(QrCode $lot): string
    {
        $unit = $lot->rootApplication?->quantity_unit ?? $lot->application?->quantity_unit;
        $unit = trim((string) $unit);

        return $unit !== '' ? $unit : 'unit';
    }

    private function produceName(QrCode $lot): string
    {
        return $lot->rootApplication?->produceType?->name
            ?? $lot->application?->produceType?->name
            ?? '—';
    }

    /**
     * @return array{
     *     hasLots: bool,
     *     waiting: int,
     *     holding: int,
     *     soldThisMonth: int,
     *     statusMix: list<array{label: string, count: int, tone: string}>,
     *     destinations: list<array{unit: string, items: list<array{label: string, count: int, percent: int, color: int}>}>,
     *     balances: list<array{id: string, qrCode: string, produce: string, remaining: int, sent: int, quantity: int, unit: string}>,
     *     balanceMore: int,
     *     products: list<array{label: string, unit: string, remaining: int, sent: int, total: int, percent: int}>,
     *     weeks: list<array{unit: string|null, total: int, splitTotal: int, soldTotal: int, receivedTotal: int, rows: list<array{label: string, split: int, sold: int, received: int, splitPercent: int, soldPercent: int}>}>
     * }
     */
    private function empty(): array
    {
        return [
            'hasLots' => false,
            'waiting' => 0,
            'holding' => 0,
            'soldThisMonth' => 0,
            'statusMix' => [],
            'destinations' => [],
            'balances' => [],
            'balanceMore' => 0,
            'products' => [],
            'weeks' => [[
                'unit' => null,
                'total' => 0,
                'splitTotal' => 0,
                'soldTotal' => 0,
                'receivedTotal' => 0,
                'rows' => [],
            ]],
        ];
    }
}
