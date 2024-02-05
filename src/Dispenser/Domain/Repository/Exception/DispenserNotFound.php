<?php

namespace App\Dispenser\Domain\Repository\Exception;

final class DispenserNotFound extends \Exception
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf("dispenser with id: '%s' not found.", $id));
    }
}
