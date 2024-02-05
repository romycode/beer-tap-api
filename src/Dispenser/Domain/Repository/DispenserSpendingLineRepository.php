<?php

declare(strict_types=1);

namespace App\Dispenser\Domain\Repository;

use App\Dispenser\Domain\Model\DispenserSpendingLine;
use App\Shared\Domain\Uuid;

interface DispenserSpendingLineRepository
{
    /** @return DispenserSpendingLine[] */
    public function findAllByDispenserId(Uuid $dispenserId): array;

    public function save(DispenserSpendingLine $dispenserSpendingLine): void;
}
