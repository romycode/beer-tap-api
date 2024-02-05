<?php

declare(strict_types=1);

namespace App\Dispenser\Domain\Model;

 use App\Shared\Domain\Clock;
use App\Shared\Domain\Uuid;

final class DispenserSpendingLine
{
    private const DEFAULT_PRICE_LITER = '12.25';

    public function __construct(
        private readonly Uuid $id,
        private readonly Uuid $dispenserId,
        private readonly float $flowVolume,
        private readonly \DateTimeImmutable $openedAt,
        private ?\DateTimeImmutable $closedAt,
        private ?int $duration,
        private ?float $outputVolume,
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

    public function closedAt(): ?\DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function duration(): ?int
    {
        return $this->duration;
    }

    public function outputVolume(): ?float
    {
        return $this->outputVolume;
    }

    public function close(\DateTimeImmutable $closedAt)
    {
        $this->closedAt = $closedAt;

        $this->duration = intval($this->closedAt->format('Uv')) - intval($this->openedAt->format('Uv'));
        $this->outputVolume = ($this->duration / 1000) * $this->flowVolume;
    }

    public function totalAmount(Clock $clock): float
    {
        if (null === $this->closedAt) {
            $closedAt = $clock->current();
            $duration = intval($closedAt->format('Uv')) - intval($this->openedAt->format('Uv'));
            $outputVolume = ($duration / 1000) * $this->flowVolume;
            $totalAmount = $outputVolume * self::DEFAULT_PRICE_LITER;
        } else {
            $totalAmount = $this->outputVolume * self::DEFAULT_PRICE_LITER;
        }

        return round($totalAmount, 3);
    }
}
