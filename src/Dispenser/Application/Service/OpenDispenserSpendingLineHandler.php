<?php

declare(strict_types=1);

namespace App\Dispenser\Application\Service;

use App\Dispenser\Application\Command\OpenDispenserSpendingLineCommand;
use App\Dispenser\Domain\Model\SpendingLine;
use App\Dispenser\Domain\Repository\DispenserRepository;
use App\Dispenser\Domain\Repository\SpendingLineRepository;
use App\Shared\Domain\CommandHandler;
use App\Shared\Domain\Uuid;

class OpenDispenserSpendingLineHandler implements CommandHandler
{
    public function __construct(
        private DispenserRepository $dispenserRepository,
        private SpendingLineRepository $dispenserSpendingLineRepository,
    ) {
    }

    public function __invoke(OpenDispenserSpendingLineCommand $command): void
    {
        $dispenser = $this->dispenserRepository->findById(Uuid::fromString($command->dispenserId));

        $this->dispenserSpendingLineRepository->save(
            new SpendingLine(
                Uuid::fromString($command->id),
                Uuid::fromString($command->dispenserId),
                $dispenser->flowVolume(),
                $command->openedAt,
                null,
                null,
                null,
            )
        );
    }
}
