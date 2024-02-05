<?php

declare(strict_types=1);

namespace App\Dispenser\Application\Service;

use App\Dispenser\Application\Command\CloseDispenserSpendingLineCommand;
use App\Dispenser\Domain\Model\DispenserClosed;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class DispenserClosedListener implements MessageHandlerInterface
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus,)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(DispenserClosed $event): void
    {
        $this->handle(
            new CloseDispenserSpendingLineCommand(
                $event->id,
                $event->closedAt,
            )
        );
    }
}
