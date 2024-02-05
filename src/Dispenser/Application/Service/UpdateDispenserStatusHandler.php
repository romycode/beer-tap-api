<?php

declare(strict_types=1);

namespace App\Dispenser\Application\Service;

use App\Dispenser\Application\Command\UpdateStatusDispenserCommand;
use App\Dispenser\Domain\Model\DispenserStatus;
use App\Dispenser\Domain\Model\Exception\DispenserStatusUpdateFailed;
use App\Dispenser\Domain\Repository\DispenserRepository;
use App\Dispenser\Domain\Repository\Exception\DispenserNotFound;
use App\Shared\Domain\Uuid;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateDispenserStatusHandler implements MessageHandlerInterface
{
    public function __construct(
        private DispenserRepository $dispenserRepository,
    ) {
    }

    /**
     * @throws DispenserNotFound
     * @throws DispenserStatusUpdateFailed
     */
    public function __invoke(UpdateStatusDispenserCommand $command): void
    {
        $dispenser = $this->dispenserRepository->findById(Uuid::fromString($command->id));

        match ($command->status) {
            DispenserStatus::Open->value => $dispenser->open($command->updatedAt),
            DispenserStatus::Close->value => $dispenser->close($command->updatedAt),
        };

        $this->dispenserRepository->save($dispenser);
    }
}
