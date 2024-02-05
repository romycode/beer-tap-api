<?php

declare(strict_types=1);

namespace App\Dispenser\Application\Command;

use App\Shared\Domain\Command;

class UpdateStatusDispenserCommand implements Command
{
    public function __construct(public string $id, public string $status, public \DateTimeImmutable $updatedAt)
    {
    }

    public function commandName(): string
    {
        return 'dispenser.status.update';
    }
}
