<?php

namespace App\Domain;

enum QrStatus: string
{
    case NotGenerated = 'NOT_GENERATED';
    case GeneratedInactive = 'GENERATED_INACTIVE';
    case Active = 'ACTIVE';

    public function label(): string
    {
        return match ($this) {
            self::NotGenerated => 'Belum Dijana',
            self::GeneratedInactive => 'Belum Aktif',
            self::Active => 'Aktif',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::GeneratedInactive => 'warning',
            self::NotGenerated => 'neutral',
        };
    }
}
