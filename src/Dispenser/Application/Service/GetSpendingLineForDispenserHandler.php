<?php

declare(strict_types=1);

namespace App\Dispenser\Application\Service;

use App\Dispenser\Application\Model\GetSpendingLineForDispenserResponse;
use App\Dispenser\Application\Model\GetSpendingLineForDispenserResponseCollection;
use App\Dispenser\Application\Query\GetSpendingLineForDispenserQuery;
use App\Dispenser\Domain\Repository\DispenserSpendingLineRepository;
use App\Shared\Domain\Clock;
use App\Shared\Domain\Uuid;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetSpendingLineForDispenserHandler implements MessageHandlerInterface
{
    public function __construct(
        private Clock $clock,
        private DispenserSpendingLineRepository $dispenserSpendingLineRepository,
    ) {
    }

    public function __invoke(GetSpendingLineForDispenserQuery $query): GetSpendingLineForDispenserResponseCollection
    {
        $lines = $this->dispenserSpendingLineRepository->findAllByDispenser(
            Uuid::fromString($query->dispenserId),
        );

        return new GetSpendingLineForDispenserResponseCollection(
            array_map(
                fn($line) => new GetSpendingLineForDispenserResponse(
                    $line->openedAt(),
                    $line->closedAt(),
                    $line->flowVolume(),
                    $line->totalAmount($this->clock),
                ),
                $lines
            )
        );
    }
}
