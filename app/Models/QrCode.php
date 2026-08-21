<?php

namespace App\Models;

use App\Domain\QrStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QrCode extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'qr_code',
        'application_id',
        'public_slug',
        'status',
        'generated_at',
        'activated_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => QrStatus::class,
            'generated_at' => 'datetime',
            'activated_at' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(ExportApplication::class, 'application_id');
    }

    public function accesses(): HasMany
    {
        return $this->hasMany(QrAccess::class, 'qr_id');
    }
}
