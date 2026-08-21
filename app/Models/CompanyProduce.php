<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyProduce extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $table = 'company_produce';

    protected $fillable = [
        'id',
        'company_id',
        'produce_type_id',
        'variety',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function produceType(): BelongsTo
    {
        return $this->belongsTo(ProduceType::class);
    }
}
