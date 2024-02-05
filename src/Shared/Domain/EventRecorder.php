<?php

declare(strict_types=1);

namespace App\Shared\Domain;

trait EventRecorder
{
    /** @var array<Event> */
    private array $events = [];

    final public function pullDomainEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    final public function record(Event $event): void
    {
        $this->events[] = $event;
    }
}
