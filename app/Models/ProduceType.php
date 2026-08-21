<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProduceType extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = ['id', 'name'];

    public function companyRows(): HasMany
    {
        return $this->hasMany(CompanyProduce::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(ExportApplication::class);
    }
}
