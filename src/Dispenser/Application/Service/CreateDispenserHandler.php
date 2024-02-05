<?php

declare(strict_types=1);

namespace App\Dispenser\Application\Service;

use App\Dispenser\Application\Command\CreateDispenserCommand;
use App\Dispenser\Domain\Model\Dispenser;
use App\Dispenser\Domain\Model\DispenserStatus;
use App\Dispenser\Domain\Repository\DispenserRepository;
use App\Shared\Domain\Clock;
use App\Shared\Domain\Uuid;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateDispenserHandler implements MessageHandlerInterface
{
    public function __construct(
        private Clock $clock,
        private DispenserRepository $dispenserRepository,
    ) {
    }

    public function __invoke(CreateDispenserCommand $command): void
    {
        $this->dispenserRepository->save(
            new Dispenser(
                Uuid::fromString($command->id),
                $command->flowVolume,
                DispenserStatus::Close,
                $this->clock->current()
            )
        );
    }
}
