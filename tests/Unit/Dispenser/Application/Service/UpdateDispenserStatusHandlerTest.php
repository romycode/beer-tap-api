<?php

namespace App\Tests\Unit\Dispenser\Application\Service;

use App\Dispenser\Application\Command\UpdateStatusDispenserCommand;
use App\Dispenser\Application\Service\UpdateDispenserStatusHandler;
use App\Dispenser\Domain\Model\Dispenser;
use App\Dispenser\Domain\Model\DispenserOpened;
use App\Dispenser\Domain\Model\DispenserStatus;
use App\Dispenser\Domain\Repository\DispenserRepository;
use App\Shared\Domain\Uuid;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UpdateDispenserStatusHandlerTest extends TestCase
{
    private DispenserRepository|MockObject $dispenserRepository;
    private UpdateDispenserStatusHandler $sut;

    final public function setUp(): void
    {
        parent::setUp();
        $this->dispenserRepository = self::createMock(DispenserRepository::class);
        $this->sut = new UpdateDispenserStatusHandler($this->dispenserRepository);
    }

    public function testShouldUpdateDispenserStatus(): void
    {
        $id = Uuid::fromString('d8725eed-c44d-43f1-957d-7fea901dde02');
        $createdAt = new \DateTimeImmutable();

        $dispenser = new Dispenser(
            $id,
            2.0,
            DispenserStatus::Close,
            $createdAt,
        );

        $this->dispenserRepository
            ->expects(self::once())
            ->method('findById')
            ->with($id)
            ->willReturn($dispenser);

        $dispenser->record(new DispenserOpened($id->toString(), $createdAt));

        $this->dispenserRepository
            ->expects(self::once())
            ->method('save')
            ->with($dispenser);

        $this->sut->__invoke(
            new UpdateStatusDispenserCommand($dispenser->id()->toString(), 'open', new \DateTimeImmutable())
        );
    }
}
