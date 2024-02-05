<?php

declare(strict_types=1);

namespace App\Dispenser\Domain\Model\Exception;

class DispenserStatusUpdateFailed extends \Exception
{

    public function __construct(string $status)
    {
        parent::__construct(sprintf("cannot update status to: '%s'", $status));
    }
}
