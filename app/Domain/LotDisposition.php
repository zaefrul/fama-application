<?php

namespace App\Domain;

enum LotDisposition: string
{
    case Holding = 'HOLDING';
    case Sold = 'SOLD';

    public function label(): string
    {
        return match ($this) {
            self::Holding => 'Dipegang',
            self::Sold => 'Dijual',
        };
    }
}
