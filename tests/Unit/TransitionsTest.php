<?php

namespace Tests\Unit;

use App\Domain\Transitions;
use PHPUnit\Framework\TestCase;

class TransitionsTest extends TestCase
{
    public function test_application_transitions(): void
    {
        $this->assertTrue(Transitions::canTransitionApplication('DRAFT', 'SUBMITTED'));
        $this->assertTrue(Transitions::canTransitionApplication('SUBMITTED', 'UNDER_REVIEW'));
        $this->assertTrue(Transitions::canTransitionApplication('UNDER_REVIEW', 'APPROVED'));
        $this->assertTrue(Transitions::canTransitionApplication('UNDER_REVIEW', 'REJECTED'));
        $this->assertFalse(Transitions::canTransitionApplication('APPROVED', 'REJECTED'));
        $this->assertFalse(Transitions::canTransitionApplication('REJECTED', 'DRAFT'));
        $this->assertFalse(Transitions::canTransitionApplication('DRAFT', 'APPROVED'));
    }

    public function test_qr_transitions(): void
    {
        $this->assertTrue(Transitions::canTransitionQr('NOT_GENERATED', 'GENERATED_INACTIVE'));
        $this->assertTrue(Transitions::canTransitionQr('GENERATED_INACTIVE', 'ACTIVE'));
        $this->assertFalse(Transitions::canTransitionQr('ACTIVE', 'GENERATED_INACTIVE'));
    }
}
