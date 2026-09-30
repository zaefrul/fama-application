<?php

namespace App\Services;

use App\Domain\Ids;
use App\Domain\LotDisposition;
use App\Domain\QrStatus;
use App\Models\Company;
use App\Models\QrCode;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LotSplitService
{
    public function __construct(private readonly JejakService $jejak) {}

    /**
     * @param  list<array{quantity: int, recipient_company_id: string}>  $chunks
     * @return list<QrCode>
     */
    public function split(string $lotId, User $actor, array $chunks): array
    {
        return DB::transaction(function () use ($lotId, $actor, $chunks) {
            $parent = $this->lockedLot($lotId);
            $this->assertHolder($parent, $actor);
            $this->assertOpen($parent);

            if ($chunks === []) {
                throw new RuntimeException('Masukkan sekurang-kurangnya satu pecahan');
            }

            $sum = 0;
            foreach ($chunks as $chunk) {
                $quantity = (int) ($chunk['quantity'] ?? 0);
                $recipientId = trim((string) ($chunk['recipient_company_id'] ?? ''));
                if ($quantity < 1) {
                    throw new RuntimeException('Kuantiti pecahan mestilah sekurang-kurangnya 1');
                }
                if (! Company::query()->whereKey($recipientId)->exists()) {
                    throw new RuntimeException('Syarikat penerima tidak dijumpai');
                }
                $sum += $quantity;
            }

            if ($sum > (int) $parent->quantity_remaining) {
                throw new RuntimeException('Jumlah pecahan melebihi baki kuantiti');
            }

            $before = (int) $parent->quantity_remaining;
            $created = [];
            foreach ($chunks as $chunk) {
                $created[] = $this->createChild(
                    $parent,
                    (int) $chunk['quantity'],
                    trim((string) $chunk['recipient_company_id']),
                    LotDisposition::Holding,
                );
            }

            $parent->quantity_remaining = $before - $sum;
            $parent->save();

            $this->jejak->writeAudit($actor, 'LOT_SPLIT', 'QRCode', $parent->id, null, [
                'quantity_remaining' => $before,
            ], [
                'quantity_remaining' => $parent->quantity_remaining,
                'children' => array_map(fn (QrCode $child) => [
                    'qr_code' => $child->qr_code,
                    'quantity' => $child->quantity,
                    'holder_company_id' => $child->holder_company_id,
                ], $created),
            ]);

            return $created;
        });
    }

    public function sellEntire(string $lotId, User $actor): QrCode
    {
        return DB::transaction(function () use ($lotId, $actor) {
            $parent = $this->lockedLot($lotId);
            $this->assertHolder($parent, $actor);
            $this->assertOpen($parent);

            $before = (int) $parent->quantity_remaining;
            $parent->disposition = LotDisposition::Sold;
            $parent->sold_at = now();
            $parent->quantity_remaining = 0;
            $parent->save();

            $this->jejak->writeAudit($actor, 'LOT_SOLD', 'QRCode', $parent->id, null, [
                'quantity_remaining' => $before,
                'disposition' => LotDisposition::Holding->value,
            ], [
                'quantity_remaining' => 0,
                'disposition' => LotDisposition::Sold->value,
            ]);

            return $parent;
        });
    }

    /**
     * @param  list<int>  $quantities
     * @return list<QrCode>
     */
    public function sellPortions(string $lotId, User $actor, array $quantities): array
    {
        return DB::transaction(function () use ($lotId, $actor, $quantities) {
            $parent = $this->lockedLot($lotId);
            $this->assertHolder($parent, $actor);
            $this->assertOpen($parent);

            if ($quantities === []) {
                throw new RuntimeException('Masukkan kuantiti yang dijual');
            }

            $sum = 0;
            foreach ($quantities as $quantity) {
                if ((int) $quantity < 1) {
                    throw new RuntimeException('Kuantiti jualan mestilah sekurang-kurangnya 1');
                }
                $sum += (int) $quantity;
            }

            if ($sum > (int) $parent->quantity_remaining) {
                throw new RuntimeException('Jumlah jualan melebihi baki kuantiti');
            }

            $before = (int) $parent->quantity_remaining;
            $created = [];
            foreach ($quantities as $quantity) {
                $created[] = $this->createChild($parent, (int) $quantity, null, LotDisposition::Sold);
            }

            $parent->quantity_remaining = $before - $sum;
            $parent->save();

            $this->jejak->writeAudit($actor, 'LOT_SOLD', 'QRCode', $parent->id, null, [
                'quantity_remaining' => $before,
            ], [
                'quantity_remaining' => $parent->quantity_remaining,
                'children' => array_map(fn (QrCode $child) => [
                    'qr_code' => $child->qr_code,
                    'quantity' => $child->quantity,
                ], $created),
            ]);

            return $created;
        });
    }

    private function lockedLot(string $lotId): QrCode
    {
        $lot = QrCode::query()->whereKey($lotId)->lockForUpdate()->first();
        if (! $lot) {
            throw new RuntimeException('Lot tidak dijumpai');
        }

        return $lot;
    }

    private function assertHolder(QrCode $lot, User $actor): void
    {
        if ($actor->company_id === null || $lot->holder_company_id !== $actor->company_id) {
            throw new RuntimeException('Hanya pemegang lot semasa boleh memecahkan atau menjual lot ini');
        }
    }

    private function assertOpen(QrCode $lot): void
    {
        if ($lot->status !== QrStatus::Active) {
            throw new RuntimeException('Lot belum aktif');
        }
        if ($lot->disposition === LotDisposition::Sold) {
            throw new RuntimeException('Lot yang telah dijual tidak boleh dipecahkan');
        }
        if ((int) $lot->quantity_remaining < 1) {
            throw new RuntimeException('Tiada baki kuantiti untuk dipecahkan');
        }
    }

    private function createChild(QrCode $parent, int $quantity, ?string $holderCompanyId, LotDisposition $disposition): QrCode
    {
        $code = Ids::nextQrCode(QrCode::query()->pluck('qr_code')->all());
        $sold = $disposition === LotDisposition::Sold;

        return QrCode::query()->create([
            'id' => Ids::create('qr'),
            'qr_code' => $code,
            'application_id' => null,
            'parent_id' => $parent->id,
            'root_application_id' => $parent->root_application_id ?: $parent->application_id,
            'holder_company_id' => $sold ? null : $holderCompanyId,
            'quantity' => $quantity,
            'quantity_remaining' => $sold ? 0 : $quantity,
            'disposition' => $disposition,
            'sold_at' => $sold ? now() : null,
            'public_slug' => $code,
            'status' => QrStatus::Active,
            'generated_at' => now(),
            'activated_at' => now(),
        ]);
    }
}
