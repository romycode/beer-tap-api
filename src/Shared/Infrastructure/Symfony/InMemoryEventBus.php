<?php

namespace App\Shared\Infrastructure\Symfony;


use App\Shared\Domain\CommandBus;
use App\Shared\Domain\Event;
use App\Shared\Domain\EventBus;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class InMemoryEventBus implements EventBus
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $messageBus,
    ) {
        $this->messageBus = $messageBus;
    }

    public function publish(Event ...$events): void
    {
        foreach ($events as $event) {
            $this->handle($event);
        }
    }
}
