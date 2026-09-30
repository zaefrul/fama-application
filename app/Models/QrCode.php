<?php

namespace App\Models;

use App\Domain\LotDisposition;
use App\Domain\QrStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class QrCode extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'qr_code',
        'application_id',
        'parent_id',
        'root_application_id',
        'holder_company_id',
        'quantity',
        'quantity_remaining',
        'disposition',
        'sold_at',
        'public_slug',
        'status',
        'generated_at',
        'activated_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => QrStatus::class,
            'disposition' => LotDisposition::class,
            'quantity' => 'integer',
            'quantity_remaining' => 'integer',
            'generated_at' => 'datetime',
            'activated_at' => 'datetime',
            'sold_at' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(ExportApplication::class, 'application_id');
    }

    public function rootApplication(): BelongsTo
    {
        return $this->belongsTo(ExportApplication::class, 'root_application_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function holder(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'holder_company_id');
    }

    public function accesses(): HasMany
    {
        return $this->hasMany(QrAccess::class, 'qr_id');
    }

    public function accessibleByCompany(?string $companyId): bool
    {
        if ($companyId === null) {
            return false;
        }
        if ($this->holder_company_id === $companyId) {
            return true;
        }
        if ($this->application?->company_id === $companyId) {
            return true;
        }

        $parentHolder = $this->relationLoaded('parent')
            ? $this->parent?->holder_company_id
            : $this->parent()->value('holder_company_id');

        return $parentHolder === $companyId;
    }

    /**
     * @return Collection<int, self>
     */
    public function lineage(): Collection
    {
        $chain = collect();
        $current = $this;
        $seen = [];

        while ($current && ! isset($seen[$current->id])) {
            $seen[$current->id] = true;
            $current->loadMissing('holder');
            $chain->prepend($current);
            if (! $current->parent_id) {
                break;
            }
            $current = $current->relationLoaded('parent') && $current->parent
                ? $current->parent
                : self::query()->find($current->parent_id);
        }

        return $chain->values();
    }

    /**
     * @return list<array{qrCode: string, quantity: int|null, holder: string|null, party: string|null, sold: bool}>
     */
    public function traceChain(): array
    {
        return $this->lineage()->map(fn (self $node) => [
            'qrCode' => $node->qr_code,
            'quantity' => $node->quantity,
            'holder' => $node->holder?->name,
            'party' => $node->holder?->partyLabel(),
            'sold' => $node->disposition === LotDisposition::Sold,
        ])->all();
    }

    public static function treeForApplication(string $applicationId): ?self
    {
        $nodes = self::query()->with('holder')->where('root_application_id', $applicationId)->get();
        if ($nodes->isEmpty()) {
            return null;
        }

        $grouped = $nodes->groupBy(fn (self $qr) => $qr->parent_id ?? '');
        $attach = function (self $node) use (&$attach, $grouped): void {
            $children = $grouped->get($node->id, collect())->values();
            $node->setRelation('children', $children);
            foreach ($children as $child) {
                $attach($child);
            }
        };

        $root = $nodes->first(fn (self $qr) => $qr->parent_id === null) ?? $nodes->first();
        $attach($root);

        return $root;
    }
}
