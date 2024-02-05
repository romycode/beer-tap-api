<?php

namespace App\Tests\Unit\Dispenser\Domain\Model;

use App\Dispenser\Domain\Model\DispenserSpendingLine;
use App\Shared\Domain\Clock;
use App\Shared\Domain\Uuid;
use PHPUnit\Framework\TestCase;

class DispenserSpendingLineTest extends TestCase
{
    private DispenserSpendingLine $sut;

    public function testTotalAmount(): void
    {
        $base = new \DateTimeImmutable();
        $clock = $this->createMock(Clock::class);
        $this->sut = new DispenserSpendingLine(
            Uuid::fromString('9888838b-b027-438f-b6ab-dfdff00d5a7a'),
            Uuid::fromString('abf5b49e-29a4-4dde-9690-678941f58171'),
            0.064,
            $base->modify('-22 second'),
            $base,
            22000,
            1.408,
        );

        self::assertEquals(17.248, $this->sut->totalAmount($clock));
    }

    public function testTotalAmountIfLineIsOpen(): void
    {
        $base = new \DateTimeImmutable();
        $clock = $this->createMock(Clock::class);
        $this->sut = new DispenserSpendingLine(
            Uuid::fromString('9888838b-b027-438f-b6ab-dfdff00d5a7a'),
            Uuid::fromString('abf5b49e-29a4-4dde-9690-678941f58171'),
            0.064,
            $base->modify('-22 second'),
            null,
            null,
            null,
        );

        $clock->expects(self::once())->method('current')->willReturn($base);

        self::assertEquals(17.248, $this->sut->totalAmount($clock));
    }
}
