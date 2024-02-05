<?php

namespace App\Shared\Domain;

interface QueryBus
{
    public function ask(Query $query): mixed;
}
