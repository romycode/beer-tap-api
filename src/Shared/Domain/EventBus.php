<?php

namespace App\Shared\Domain;

interface EventBus
{
    /**
     * @param Event ...$events
     * @return void
     */
    public function publish(Event ...$events): void;
}
