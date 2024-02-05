<?php

declare(strict_types=1);

namespace App\Dispenser\Domain\Model;

enum DispenserStatus: string
{
    case Open = "open";
    case Close = "close";
}
