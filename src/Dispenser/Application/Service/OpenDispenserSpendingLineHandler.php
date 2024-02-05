<?php

declare(strict_types=1);

namespace App\Dispenser\Application\Service;

use App\Dispenser\Application\Command\OpenDispenserSpendingLineCommand;
use App\Dispenser\Domain\Model\DispenserSpendingLine;
use App\Dispenser\Domain\Repository\DispenserRepository;
use App\Dispenser\Domain\Repository\DispenserSpendingLineRepository;
use App\Shared\Domain\Uuid;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class OpenDispenserSpendingLineHandler implements MessageHandlerInterface
{
    public function __construct(
        private DispenserRepository $dispenserRepository,
        private DispenserSpendingLineRepository $dispenserSpendingLineRepository,
    ) {
    }

    public function __invoke(OpenDispenserSpendingLineCommand $command): void
    {
        $dispenser = $this->dispenserRepository->findById(Uuid::fromString($command->dispenserId));

        $this->dispenserSpendingLineRepository->save(
            new DispenserSpendingLine(
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
