<?php

declare(strict_types=1);

namespace App\Dispenser\Domain\Model;

use App\Shared\Domain\Uuid;

final class Dispenser
{
    public function __construct(
        private readonly Uuid $id,
        private float $flowVolume,
        private DispenserStatus $status,
        private readonly \DateTimeImmutable $createdAt,
    ) {
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function flowVolume(): float
    {
        return $this->flowVolume;
    }

    public function status(): DispenserStatus
    {
        return $this->status;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
