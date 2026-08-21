<?php

namespace App\Domain;

use RuntimeException;

final class Transitions
{
    /**
     * @var array<string, list<string>>
     */
    private const APPLICATION = [
        'DRAFT' => ['SUBMITTED'],
        'SUBMITTED' => ['UNDER_REVIEW'],
        'UNDER_REVIEW' => ['APPROVED', 'REJECTED'],
        'APPROVED' => [],
        'REJECTED' => [],
    ];

    /**
     * @var array<string, list<string>>
     */
    private const QR = [
        'NOT_GENERATED' => ['GENERATED_INACTIVE'],
        'GENERATED_INACTIVE' => ['ACTIVE'],
        'ACTIVE' => [],
    ];

    public static function canTransitionApplication(string $from, string $to): bool
    {
        return in_array($to, self::APPLICATION[$from] ?? [], true);
    }

    public static function canTransitionQr(string $from, string $to): bool
    {
        return in_array($to, self::QR[$from] ?? [], true);
    }

    public static function assertApplicationTransition(string $from, string $to): void
    {
        if (! self::canTransitionApplication($from, $to)) {
            throw new RuntimeException("Peralihan status permohonan tidak sah: {$from} → {$to}");
        }
    }

    public static function assertQrTransition(string $from, string $to): void
    {
        if (! self::canTransitionQr($from, $to)) {
            throw new RuntimeException("Peralihan status QR tidak sah: {$from} → {$to}");
        }
    }
}
