<?php

namespace Tests\Unit;

use App\Domain\Ids;
use PHPUnit\Framework\TestCase;

class IdsTest extends TestCase
{
    public function test_application_numbers_increment_from_the_highest_serial(): void
    {
        $this->assertSame('FAMA-2026-000001', Ids::nextApplicationNo([]));
        $this->assertSame(
            'FAMA-2026-000125',
            Ids::nextApplicationNo(['FAMA-2026-000123', 'FAMA-2026-000015', 'FAMA-2026-000124']),
        );
    }

    public function test_qr_codes_increment_and_ignore_non_numeric_suffixes(): void
    {
        $this->assertSame('GPL-QR-000001', Ids::nextQrCode([]));
        $this->assertSame(
            'GPL-QR-000124',
            Ids::nextQrCode(['GPL-QR-000123', 'GPL-QR-demo', 'GPL-QR-000011']),
        );
    }
}
