<?php

declare(strict_types=1);

namespace App\Dispenser\Domain\Model;

use App\Dispenser\Domain\Model\Exception\DispenserStatusUpdateFailed;
use App\Shared\Domain\Clock;
use App\Shared\Domain\EventRecorder;
use App\Shared\Domain\Uuid;

final class Dispenser
{
    use EventRecorder;

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

    public function open(\DateTimeImmutable $updatedAt): void
    {
        if ($this->status->value == DispenserStatus::Open->value) {
            throw new DispenserStatusUpdateFailed(DispenserStatus::Open->value);
        }

        $this->status = DispenserStatus::Open;

        $this->record(new DispenserOpened($this->id->toString(), $updatedAt));
    }

    public function close(\DateTimeImmutable $updatedAt): void
    {
        if ($this->status->value == DispenserStatus::Close->value) {
            throw new DispenserStatusUpdateFailed(DispenserStatus::Close->value);
        }

        $this->status = DispenserStatus::Close;

        $this->record(new DispenserClosed($this->id->toString(), $updatedAt));
    }
}
