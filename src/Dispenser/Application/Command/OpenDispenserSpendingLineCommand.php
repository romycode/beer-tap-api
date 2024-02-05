<?php

namespace App\Dispenser\Application\Command;

class OpenDispenserSpendingLineCommand
{
    public function __construct(public string $id, public string $dispenserId, public \DateTimeImmutable $openedAt)
    {
    }
}
