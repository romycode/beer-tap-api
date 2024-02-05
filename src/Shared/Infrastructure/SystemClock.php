<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure;

use App\Shared\Domain\Clock;

final class SystemClock implements Clock
{
    public function current(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
