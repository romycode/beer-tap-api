<?php

namespace App\Tests\Unit\Dispenser\Application\Service;

use App\Dispenser\Application\Command\UpdateStatusDispenserCommand;
use App\Dispenser\Application\Service\UpdateDispenserStatusHandler;
use App\Dispenser\Domain\Model\Dispenser;
use App\Dispenser\Domain\Model\DispenserOpened;
use App\Dispenser\Domain\Model\DispenserStatus;
use App\Dispenser\Domain\Repository\DispenserRepository;
use App\Shared\Domain\EventBus;
use App\Shared\Domain\Uuid;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UpdateDispenserStatusHandlerTest extends TestCase
{
    private EventBus|MockObject $eventBus;
    private DispenserRepository|MockObject $dispenserRepository;
    private UpdateDispenserStatusHandler $sut;

    final public function setUp(): void
    {
        parent::setUp();
        $this->eventBus = $this->createMock(EventBus::class);
        $this->dispenserRepository = self::createMock(DispenserRepository::class);
        $this->sut = new UpdateDispenserStatusHandler($this->eventBus, $this->dispenserRepository);
    }

    public function testShouldUpdateDispenserStatus(): void
    {
        $id = Uuid::fromString('d8725eed-c44d-43f1-957d-7fea901dde02');
        $base = new \DateTimeImmutable();
        $createdAt = $base->modify('-60 second');
        $updatedAt = $base;

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

        $dispenserOpened = new DispenserOpened($id->toString(), $updatedAt);

        $expected = new Dispenser(
            $dispenser->id(),
            $dispenser->flowVolume(),
            DispenserStatus::Open,
            $createdAt,
        );
        $expected->record($dispenserOpened);
        $this->dispenserRepository
            ->expects(self::once())
            ->method('save')
            ->with($expected);

        $this->eventBus
            ->expects(self::once())
            ->method('publish')
            ->with(...[$dispenserOpened]);

        $this->sut->__invoke(
            new UpdateStatusDispenserCommand($dispenser->id()->toString(), 'open', $updatedAt)
        );
    }
}
