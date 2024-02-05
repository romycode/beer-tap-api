<?php

namespace App\Dispenser\Application\Command;

use App\Shared\Domain\Command;

class OpenDispenserSpendingLineCommand implements Command
{
    public function __construct(public string $id, public string $dispenserId, public \DateTimeImmutable $openedAt)
    {
    }

    public function commandName(): string
    {
        return 'dispenser.spending_line.open';
    }
}
