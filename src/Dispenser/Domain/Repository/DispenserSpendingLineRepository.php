<?php

declare(strict_types=1);

namespace App\Dispenser\Domain\Repository;

use App\Dispenser\Domain\Model\DispenserSpendingLine;
use App\Shared\Domain\Uuid;

interface DispenserSpendingLineRepository
{
     /**
     * @param Uuid $dispenserId
     * @return DispenserSpendingLine
     */
    public function findLatestForDispenserId(Uuid $dispenserId): DispenserSpendingLine;

    /** @return DispenserSpendingLine[] */
    public function findAllByDispenser(Uuid $dispenserId): array;

    public function save(DispenserSpendingLine $dispenserSpendingLine): void;
}
