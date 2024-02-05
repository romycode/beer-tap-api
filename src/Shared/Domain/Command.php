<?php

namespace App\Shared\Domain;

interface Command
{
    public function commandName(): string;
}
