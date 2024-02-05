<?php

namespace App\Shared\Infrastructure\Symfony;


use App\Shared\Domain\QueryBus;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class InMemoryQueryBus implements QueryBus
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $messageBus,
    ) {
        $this->messageBus = $messageBus;
    }

    public function ask(mixed $query): mixed
    {
        return $this->handle($query);
    }
}
