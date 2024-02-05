<?php

namespace App\Shared\Domain;

interface Clock
{
    public function current(): \DateTimeImmutable;
}
