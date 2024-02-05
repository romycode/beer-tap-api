<?php

namespace App\Shared\Infrastructure\Symfony;


use App\Shared\Domain\CommandBus;
use Symfony\Component\Messenger\MessageBusInterface;

class InMemoryCommandBus implements CommandBus
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
    }

    public function execute(mixed $command): mixed
    {
        return $this->messageBus->dispatch($command);
    }
}
