<?php

namespace App\Shared\Domain;

interface CommandBus
{
    public function execute(Command $command): mixed;
}
