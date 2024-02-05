<?php

declare(strict_types=1);

namespace App\Dispenser\Application\Command;

class CreateDispenserCommand
{
    public function __construct(public string $id, public float $flowVolume)
    {
    }
}
