<?php

namespace App\Tests\Unit\Dispenser\Application\Service;

use App\Dispenser\Application\Command\OpenDispenserSpendingLineCommand;
use App\Dispenser\Application\Service\OpenDispenserSpendingLineHandler;
use App\Dispenser\Domain\Model\Dispenser;
use App\Dispenser\Domain\Model\SpendingLine;
use App\Dispenser\Domain\Model\DispenserStatus;
use App\Dispenser\Domain\Repository\DispenserRepository;
use App\Dispenser\Domain\Repository\SpendingLineRepository;
use App\Shared\Domain\Uuid;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OpenDispenserSpendingLineHandlerTest extends TestCase
{
    private DispenserRepository|MockObject $dispenserRepository;
    private SpendingLineRepository|MockObject $dispenserSpendingLineRepository;
    private OpenDispenserSpendingLineHandler $sut;

    final public function setUp(): void
    {
        parent::setUp();
        $this->dispenserRepository = self::createMock(DispenserRepository::class);
        $this->dispenserSpendingLineRepository = self::createMock(SpendingLineRepository::class);
        $this->sut = new OpenDispenserSpendingLineHandler(
            $this->dispenserRepository,
            $this->dispenserSpendingLineRepository
        );
    }

    public function testShouldCloseANewDispenserSpendingLine(): void
    {
        $id = "e2c645d6-17f1-4a41-9854-8c52d533d112";
        $dispenserId = "08f2e4f8-0069-4144-839b-1bf1c40e3c9d";
        $openedAt = new \DateTimeImmutable();

        $this->dispenserRepository
            ->expects(self::once())
            ->method('findById')
            ->with(Uuid::fromString($dispenserId))
            ->willReturn(
                new Dispenser(
                    Uuid::fromString($dispenserId),
                    0.5,
                    DispenserStatus::Close,
                    $openedAt->modify('-1 day')
                )
            );

        $this->dispenserSpendingLineRepository
            ->expects(self::once())
            ->method('save')
            ->with(
                new SpendingLine(
                    Uuid::fromString($id),
                    Uuid::fromString($dispenserId),
                    0.5,
                    $openedAt,
                    null,
                    null,
                    null,
                )
            );

        $this->sut->__invoke(
            new OpenDispenserSpendingLineCommand(
                $id,
                $dispenserId,
                $openedAt,
            )
        );
    }
}
