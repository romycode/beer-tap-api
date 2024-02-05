<?php

namespace App\Tests\Unit\Dispenser\Application\Service;

use App\Dispenser\Application\Command\CreateDispenserCommand;
use App\Dispenser\Application\Service\CreateDispenserHandler;
use App\Dispenser\Domain\Model\Dispenser;
use App\Dispenser\Domain\Model\DispenserStatus;
use App\Dispenser\Domain\Repository\DispenserRepository;
use App\Shared\Domain\Clock;
use App\Shared\Domain\Uuid;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CreateDispenserHandlerTest extends TestCase
{
    private Clock|MockObject $clock;
    private DispenserRepository|MockObject $dispenserRepository;
    private CreateDispenserHandler $sut;

    final public function setUp(): void
    {
        parent::setUp();
        $this->clock = self::createMock(Clock::class);
        $this->dispenserRepository = self::createMock(DispenserRepository::class);
        $this->sut = new CreateDispenserHandler($this->clock, $this->dispenserRepository);
    }

    /** @test */
    public function shouldCreateDispenser(): void
    {
        $createdAt = new \DateTimeImmutable();
        $dispenser = new Dispenser(
            Uuid::fromString('d8725eed-c44d-43f1-957d-7fea901dde02'),
            2.0,
            DispenserStatus::Close,
            $createdAt,
        );

        $this->dispenserRepository
            ->expects(self::once())
            ->method('save')
            ->with($dispenser);

        $this->clock
            ->expects(self::once())
            ->method('current')
            ->willReturn($createdAt);

        $this->sut->__invoke(new CreateDispenserCommand($dispenser->id()->toString(), $dispenser->flowVolume()));
    }
}
