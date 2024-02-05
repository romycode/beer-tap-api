<?php

declare(strict_types=1);

namespace App\Dispenser\Application\Service;

use App\Dispenser\Application\Command\OpenDispenserSpendingLineCommand;
use App\Dispenser\Domain\Model\DispenserOpened;
use App\Shared\Domain\CommandBus;
use App\Shared\Domain\EventListener;
use App\Shared\Domain\UuidGenerator;

class DispenserOpenedListener implements EventListener
{
    public function __construct(private CommandBus $commandBus, private UuidGenerator $uuidGenerator)
    {
    }

    public function __invoke(DispenserOpened $event): void
    {
        $this->commandBus->execute(
            new OpenDispenserSpendingLineCommand(
                $this->uuidGenerator->generate()->toString(),
                $event->id,
                $event->updatedAt,
            )
        );
    }
}
