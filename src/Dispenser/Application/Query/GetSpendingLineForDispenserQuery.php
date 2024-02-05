<?php

namespace App\Dispenser\Application\Query;

use App\Shared\Domain\Query;

class GetSpendingLineForDispenserQuery implements Query
{
    public function __construct(public string $dispenserId)
    {
    }

    public function queryName(): string
    {
        return 'dispensers.spending_lines';
    }
}
