<?php

namespace App\Shared\Domain;

interface Event
{
    public function eventName(): string;
}
