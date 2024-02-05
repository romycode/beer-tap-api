<?php

declare(strict_types=1);

namespace App\Dispenser\Application\Model;

class GetSpendingLineForDispenserResponseCollection
{
    /** @param GetSpendingLineForDispenserResponse[] $items */
    public function __construct(
        public readonly array $items
    ) {
    }
}
