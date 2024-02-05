<?php

declare(strict_types=1);

namespace App\Dispenser\Application\Model;

class GetSpendingLineForDispenserResponseCollection
{
    /** @param GetSpendingLineForDispenserResponse[] $items */
    public function __construct(
        public readonly array $items
    ) {
    }

    public function toArray(): array
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = [
                'closed_at' => $item->closedAt,
                'opened_at' => $item->openedAt,
                'flow_volume' => $item->flowVolume,
                'total_amount' => $item->totalAmount,
            ];
        }
        return $items;
    }
}
