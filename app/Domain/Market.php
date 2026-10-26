<?php

namespace App\Domain;

enum Market: string
{
    case Domestic = 'DOMESTIC';
    case Export = 'EXPORT';

    public function label(): string
    {
        return match ($this) {
            self::Domestic => 'Domestik',
            self::Export => 'Eksport',
        };
    }

    public static function fromInput(?string $value): self
    {
        return self::tryFrom((string) $value) ?? self::Export;
    }
}
