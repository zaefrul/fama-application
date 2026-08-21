<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    public const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'actor_user_id',
        'actor_role',
        'action',
        'object_type',
        'object_id',
        'before_json',
        'after_json',
        'remarks',
    ];
}
