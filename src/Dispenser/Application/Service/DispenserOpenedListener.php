<?php

declare(strict_types=1);

namespace App\Dispenser\Application\Service;

use App\Dispenser\Application\Command\OpenDispenserSpendingLineCommand;
use App\Dispenser\Domain\Model\DispenserOpened;
use App\Shared\Domain\UuidGenerator;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class DispenserOpenedListener implements MessageHandlerInterface
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus, private UuidGenerator $uuidGenerator)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(DispenserOpened $event): void
    {
        $this->handle(
            new OpenDispenserSpendingLineCommand(
                $this->uuidGenerator->generate()->toString(),
                $event->id,
                $event->updatedAt,
            )
        );
    }
}
