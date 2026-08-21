<?php

namespace App\Models;

use App\Domain\ApplicationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExportApplication extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'application_no',
        'company_id',
        'produce_type_id',
        'variety',
        'grade',
        'size',
        'quantity',
        'quantity_unit',
        'destination_country',
        'coc_certificate_id',
        'coc_number',
        'export_date',
        'farm_name',
        'importer_name',
        'importer_address',
        'status',
        'submitted_at',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'export_date' => 'date',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'quantity' => 'integer',
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

    public function qrCode(): HasOne
    {
        return $this->hasOne(QrCode::class, 'application_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class, 'application_id');
    }
}
