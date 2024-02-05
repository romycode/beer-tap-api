<?php

declare(strict_types=1);

namespace App\Dispenser\Domain\Repository;

use App\Dispenser\Domain\Model\Dispenser;
use App\Dispenser\Domain\Repository\Exception\DispenserAlreadyExists;
use App\Dispenser\Domain\Repository\Exception\DispenserNotFound;
use App\Shared\Domain\Uuid;

interface DispenserRepository
{
    /** @throws DispenserNotFound */
    public function findById(Uuid $uuid): Dispenser;

    public function save(Dispenser $dispenser): void;
}
