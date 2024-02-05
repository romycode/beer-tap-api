<?php

namespace App\Dispenser\Application\Command;

use App\Shared\Domain\Command;

class CloseDispenserSpendingLineCommand implements Command
{
    public function __construct(public string $dispenserId, public \DateTimeImmutable $closedAt)
    {
    }

    public function commandName(): string
    {
        return 'dispenser.spending_line.close';
    }
}
