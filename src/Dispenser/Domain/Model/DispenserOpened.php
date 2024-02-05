<?php

declare(strict_types=1);

namespace App\Dispenser\Domain\Model;

use App\Shared\Domain\Event;

final class DispenserOpened implements Event
{
    public function __construct(public readonly string $id, public readonly \DateTimeImmutable $updatedAt)
    {
    }

    public function eventName(): string
    {
        return 'dispenser.1.opened';
    }
}
