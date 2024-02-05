<?php

declare(strict_types=1);

namespace App\Dispenser\Application\Command;

use App\Shared\Domain\Command;

class CreateDispenserCommand implements Command
{
    public function __construct(public string $id, public float $flowVolume)
    {
    }

    public function commandName(): string
    {
        return 'dispenser.create';
    }
}
