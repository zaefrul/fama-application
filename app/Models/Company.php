<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'registration_no',
        'external_account_no',
        'name',
        'email',
        'phone',
        'address',
        'state',
        'district',
        'postcode',
        'website',
        'logo_path',
        'external_source',
        'external_status',
    ];

    public function isFamaSourced(): bool
    {
        return $this->external_source === 'FAMA';
    }

    public function displayName(): string
    {
        $name = trim((string) $this->name);
        if (preg_match('/^\d+\s*\((.+)\)\s*$/u', $name, $matches) === 1) {
            $inner = trim($matches[1]);
            if ($inner !== '') {
                return $inner;
            }
        }

        return $name !== '' ? $name : 'Tanpa nama syarikat';
    }

    public function nameSortKey(): string
    {
        $display = $this->displayName();
        $unnamed = preg_match('/^\d+$/u', $display) === 1 || $display === 'Tanpa nama syarikat';

        return ($unnamed ? '1|' : '0|').mb_strtolower($display);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function produce(): HasMany
    {
        return $this->hasMany(CompanyProduce::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(GalleryItem::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(ExportApplication::class);
    }
}
