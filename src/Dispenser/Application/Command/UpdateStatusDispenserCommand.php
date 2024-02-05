<?php

declare(strict_types=1);

namespace App\Dispenser\Application\Command;

class UpdateStatusDispenserCommand
{
    public function __construct(public string $id, public string $status, public \DateTimeImmutable $updatedAt)
    {
    }
}
