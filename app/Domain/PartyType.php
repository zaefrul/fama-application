<?php

namespace App\Domain;

enum PartyType: string
{
    case Supplier = 'SUPPLIER';
    case Exporter = 'EXPORTER';
    case Marketer = 'MARKETER';
    case Importer = 'IMPORTER';
    case Retailer = 'RETAILER';
    case Other = 'OTHER';

    public function label(): string
    {
        return match ($this) {
            self::Supplier => 'Pengeluar',
            self::Exporter => 'Pengeksport',
            self::Marketer => 'Pemborong',
            self::Importer => 'Pengimport',
            self::Retailer => 'Peruncit',
            self::Other => 'Lain-lain',
        };
    }

    /**
     * Downstream rank. A pecahan may stay on this rank or move to a higher one.
     */
    public function rank(): ?int
    {
        return match ($this) {
            self::Supplier => 1,
            self::Exporter => 2,
            self::Marketer => 3,
            self::Importer => 4,
            self::Retailer => 5,
            self::Other => null,
        };
    }

    public function canPassTo(self $recipient): bool
    {
        $from = $this->rank();
        $to = $recipient->rank();
        if ($from === null || $to === null) {
            return false;
        }

        return $to >= $from;
    }

    /**
     * @return list<self>
     */
    public static function choices(): array
    {
        return [
            self::Supplier,
            self::Exporter,
            self::Marketer,
            self::Importer,
            self::Retailer,
            self::Other,
        ];
    }
}
