<?php

declare(strict_types=1);

namespace App\Dispenser\Domain\Repository;

use App\Dispenser\Domain\Model\Dispenser;
use App\Dispenser\Domain\Repository\Exception\DispenserNotFound;
use App\Shared\Domain\Exception\UnexpectedError;
use App\Shared\Domain\Uuid;

interface DispenserRepository
{
    /** @throws DispenserNotFound|UnexpectedError */
    public function findById(Uuid $uuid): Dispenser;

    /** @throws UnexpectedError */
    public function save(Dispenser $dispenser): void;
}
