<?php

declare(strict_types=1);

namespace App\Dispenser\Application\Model;

class GetSpendingLineForDispenserResponse
{
    public function __construct(
        public readonly \DateTimeImmutable $openedAt,
        public readonly ?\DateTimeImmutable $closedAt,
        public readonly float $flowVolume,
        public readonly float $totalAmount,
    ) {
    }
}
