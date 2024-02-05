<?php

namespace App\Dispenser\Application\Query;

class GetSpendingLineForDispenserQuery
{
    public function __construct(public string $dispenserId)
    {
    }
}
