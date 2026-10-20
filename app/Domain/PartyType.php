<?php

namespace App\Domain;

enum PartyType: string
{
    case Supplier = 'SUPPLIER';
    case Exporter = 'EXPORTER';
    case Marketer = 'MARKETER';
    case Retailer = 'RETAILER';
    case Other = 'OTHER';

    public function label(): string
    {
        return match ($this) {
            self::Supplier => 'Pengeluar',
            self::Exporter => 'Pemborong',
            self::Marketer => 'Pemborong',
            self::Retailer => 'Peruncit',
            self::Other => 'Lain-lain',
        };
    }

    /**
     * @return list<self>
     */
    public static function choices(): array
    {
        return [
            self::Supplier,
            self::Exporter,
            self::Retailer,
            self::Other,
        ];
    }
}
