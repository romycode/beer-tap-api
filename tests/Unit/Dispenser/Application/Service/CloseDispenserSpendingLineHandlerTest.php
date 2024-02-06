<?php

namespace App\Tests\Unit\Dispenser\Application\Service;

use App\Dispenser\Application\Command\CloseDispenserSpendingLineCommand;
use App\Dispenser\Application\Service\CloseDispenserSpendingLineHandler;
use App\Dispenser\Domain\Model\SpendingLine;
use App\Dispenser\Domain\Repository\SpendingLineRepository;
use App\Shared\Domain\Uuid;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CloseDispenserSpendingLineHandlerTest extends TestCase
{
    private SpendingLineRepository|MockObject $dispenserSpendingLineRepository;
    private CloseDispenserSpendingLineHandler $sut;

    final public function setUp(): void
    {
        parent::setUp();
        $this->dispenserSpendingLineRepository = self::createMock(SpendingLineRepository::class);
        $this->sut = new CloseDispenserSpendingLineHandler($this->dispenserSpendingLineRepository);
    }

    public function testShouldCloseADispenserSpendingLine(): void
    {
        $id = "e2c645d6-17f1-4a41-9854-8c52d533d112";
        $dispenserId = "08f2e4f8-0069-4144-839b-1bf1c40e3c9d";
        $start = new \DateTimeImmutable();
        $openedAt = $start->modify('-20 seconds');
        $closedAt = $start->modify('-10 seconds');

        $this->dispenserSpendingLineRepository
            ->expects(self::once())
            ->method('findLatestForDispenserId')
            ->with(Uuid::fromString($dispenserId))
            ->willReturn(
                new SpendingLine(
                    Uuid::fromString($id),
                    Uuid::fromString($dispenserId),
                    0.5,
                    $openedAt,
                    $closedAt,
                    10000,
                    5.0,
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
                    $closedAt,
                    10000,
                    5.0,
                )
            );

        $this->sut->__invoke(
            new CloseDispenserSpendingLineCommand(
                $dispenserId,
                $closedAt,
            )
        );
    }
}
