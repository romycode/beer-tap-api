<?php

namespace App\Dispenser\Application\Command;

class CloseDispenserSpendingLineCommand
{
    public function __construct(public string $dispenserId, public \DateTimeImmutable $closedAt)
    {
    }
}
