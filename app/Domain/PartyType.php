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
            self::Supplier => 'Pembekal',
            self::Exporter => 'Pengeksport',
            self::Marketer => 'Pemasar',
            self::Retailer => 'Peruncit',
            self::Other => 'Lain-lain',
        };
    }
}
