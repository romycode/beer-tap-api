<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure;

use App\Shared\Domain\UuidGenerator;
use Ramsey\Uuid\Uuid;

class RamseyUuidGenerator implements UuidGenerator
{
    public function generate(): \App\Shared\Domain\Uuid
    {
        return \App\Shared\Domain\Uuid::fromString(Uuid::uuid4()->toString());
    }
}
