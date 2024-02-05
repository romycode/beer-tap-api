<?php

declare(strict_types=1);

namespace App\Dispenser\Application\Service;

use App\Dispenser\Application\Command\CloseDispenserSpendingLineCommand;
use App\Dispenser\Domain\Model\DispenserClosed;
use App\Shared\Domain\CommandBus;
use App\Shared\Domain\EventListener;

class DispenserClosedListener implements EventListener
{
    public function __construct(private CommandBus $commandBus)
    {
    }

    public function __invoke(DispenserClosed $event): void
    {
        $this->commandBus->execute(
            new CloseDispenserSpendingLineCommand(
                $event->id,
                $event->closedAt,
            )
        );
    }
}
