<?php

namespace App\Domain;

enum ApplicationStatus: string
{
    case Draft = 'DRAFT';
    case Submitted = 'SUBMITTED';
    case UnderReview = 'UNDER_REVIEW';
    case Approved = 'APPROVED';
    case Rejected = 'REJECTED';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf',
            self::Submitted => 'Dihantar',
            self::UnderReview => 'Dalam Semakan',
            self::Approved => 'Diluluskan',
            self::Rejected => 'Ditolak',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Approved => 'success',
            self::Rejected => 'danger',
            self::UnderReview, self::Submitted => 'info',
            self::Draft => 'neutral',
        };
    }
}
