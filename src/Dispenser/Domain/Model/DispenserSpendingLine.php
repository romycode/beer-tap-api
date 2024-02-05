<?php

declare(strict_types=1);

namespace App\Dispenser\Domain\Model;

use App\Dispenser\Domain\Model\Exception\DispenserStatusUpdateFailed;
use App\Shared\Domain\Clock;
use App\Shared\Domain\EventRecorder;
use App\Shared\Domain\Uuid;

final class DispenserSpendingLine
{
    public function __construct(
        private readonly Uuid $id,
        private readonly Uuid $dispenserId,
        private readonly \DateTimeImmutable $openedAt,
        private readonly ?\DateTimeImmutable $closedAt,
    ) {
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function dispenserId(): Uuid
    {
        return $this->dispenserId;
    }

    public function flowVolume(): float
    {
        return $this->flowVolume;
    }

    public function openedAt(): \DateTimeImmutable
    {
        return $this->openedAt;
    }

    public function closedAt(): \DateTimeImmutable
    {
        return $this->closedAt;
    }
}
