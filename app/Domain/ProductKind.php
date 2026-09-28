<?php

namespace App\Domain;

enum ProductKind: string
{
    case Produce = 'PRODUCE';
    case Livestock = 'LIVESTOCK';

    public static function fromQuery(?string $value): ?self
    {
        return match ($value) {
            'produce', self::Produce->value => self::Produce,
            'livestock', self::Livestock->value => self::Livestock,
            default => null,
        };
    }

    public function query(): string
    {
        return $this === self::Livestock ? 'livestock' : 'produce';
    }

    public function label(): string
    {
        return match ($this) {
            self::Produce => 'Keluaran Pertanian',
            self::Livestock => 'Haiwan Ternakan',
        };
    }
}
