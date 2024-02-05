<?php

declare(strict_types=1);

namespace App\Dispenser\Application\Service;

use App\Dispenser\Application\Command\CloseDispenserSpendingLineCommand;
use App\Dispenser\Domain\Repository\DispenserSpendingLineRepository;
use App\Shared\Domain\Uuid;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CloseDispenserSpendingLineHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly DispenserSpendingLineRepository $dispenserSpendingLineRepository,
    ) {
    }

    public function __invoke(CloseDispenserSpendingLineCommand $command): void
    {
        $actual = $this->dispenserSpendingLineRepository->findLatestForDispenserId(
            Uuid::fromString($command->dispenserId)
        );
        $actual->close($command->closedAt);
        $this->dispenserSpendingLineRepository->save($actual);
    }
}