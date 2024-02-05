<?php

namespace App\Tests\Unit\Dispenser\Application\Service;

use App\Dispenser\Application\Model\GetSpendingLineForDispenserResponse;
use App\Dispenser\Application\Model\GetSpendingLineForDispenserResponseCollection;
use App\Dispenser\Application\Query\GetSpendingLineForDispenserQuery;
use App\Dispenser\Application\Service\GetSpendingLineForDispenserHandler;
use App\Dispenser\Domain\Model\DispenserSpendingLine;
use App\Dispenser\Domain\Repository\DispenserSpendingLineRepository;
use App\Shared\Domain\Clock;
use App\Shared\Domain\Uuid;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetSpendingLineForDispenserHandlerTest extends TestCase
{
    private Clock|MockObject $clock;
    private DispenserSpendingLineRepository|MockObject $dispenserSpendingLineRepository;
    private GetSpendingLineForDispenserHandler $sut;

    final public function setUp(): void
    {
        parent::setUp();
        $this->clock = self::createMock(Clock::class);
        $this->dispenserSpendingLineRepository = self::createMock(DispenserSpendingLineRepository::class);
        $this->sut = new GetSpendingLineForDispenserHandler($this->clock, $this->dispenserSpendingLineRepository);
    }

    public function testShouldReturnAllLines(): void
    {
        $id = "a6782a74-e821-48a9-939f-814fb099c6e2";
        $dispenserId = "a6782a74-e821-48a9-939f-814fb099c6e1";

        $base = new \DateTimeImmutable();

        $this->dispenserSpendingLineRepository
            ->expects(self::once())
            ->method('findAllByDispenser')
            ->with(Uuid::fromString($dispenserId))
            ->willReturn([
                new DispenserSpendingLine(
                    Uuid::fromString($id),
                    Uuid::fromString($dispenserId),
                    0.01,
                    $base->modify('-22 second'),
                    null,
                    null,
                    null,
                ),
            ]);

        $this->clock->expects(self::once())->method('current')->willReturn($base);

        $res = $this->sut->__invoke(new GetSpendingLineForDispenserQuery($dispenserId));

        self::assertEquals(new GetSpendingLineForDispenserResponseCollection([
            new GetSpendingLineForDispenserResponse(
                $base->modify('-22 second'),
                null,
                0.01,
                2.695,
            )
        ]), $res);
    }
}
