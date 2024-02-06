<?php

declare(strict_types=1);

namespace App\Dispenser\Domain\Repository;

use App\Dispenser\Domain\Model\SpendingLine;
use App\Shared\Domain\Exception\UnexpectedError;
use App\Shared\Domain\Uuid;

interface SpendingLineRepository
{
    /**
     * @param Uuid $dispenserId
     * @return SpendingLine
     * @throws UnexpectedError
     */
    public function findLatestForDispenserId(Uuid $dispenserId): SpendingLine;

    /**
     * @return SpendingLine[]
     * @throws UnexpectedError
     */
    public function findAllByDispenser(Uuid $dispenserId): array;

    /** @throws UnexpectedError */
    public function save(SpendingLine $dispenserSpendingLine): void;
}
