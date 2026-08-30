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
        if (preg_match('/^\d+\s*[\-\x{2013}\x{2014}]\s*(.+)$/u', $name, $matches) === 1
            || preg_match('/^\d+\s*\((.+)\)\s*$/u', $name, $matches) === 1) {
            $inner = trim($matches[1]);
            if ($inner !== '') {
                return $inner;
            }
        }

        return $name !== '' ? $name : 'Tanpa nama syarikat';
    }

    public function leadingNumber(): ?int
    {
        if (preg_match('/^(\d+)\b/u', trim((string) $this->name), $matches) === 1) {
            return (int) $matches[1];
        }

        return null;
    }

    public function nameSortKey(): string
    {
        $number = $this->leadingNumber();
        if ($number === null) {
            return '1|'.mb_strtolower(trim((string) $this->name));
        }

        return '0|'.str_pad((string) $number, 6, '0', STR_PAD_LEFT).'|'.mb_strtolower($this->displayName());
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
