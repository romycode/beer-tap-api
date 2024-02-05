<?php

namespace App\Tests\Application\Dispenser\Infrastructure\Repository;

use App\Dispenser\Domain\Model\DispenserSpendingLine;
use App\Dispenser\Domain\Repository\DispenserSpendingLineRepository;
use App\Shared\Domain\Uuid;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DbalDispenserSpendingLineRepositoryTest extends WebTestCase
{
    private const DISPENSER_SPENDING_LINE_ONE_ID = "7420caff-23a0-4f5c-9bfa-c47f901502ba";
    private const DISPENSER_SPENDING_LINE_TWO_ID = "7420caff-23a0-4f5c-9bfa-c47f901502bb";
    private DispenserSpendingLineRepository $sut;
    private Connection $conn;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = self::getContainer()->get(DispenserSpendingLineRepository::class);

        /** @var Connection $conn */
        $this->conn = self::getContainer()->get(Connection::class);
        $this->conn
            ->prepare("delete from dispensers_spending_lines where id = ?")
            ->executeQuery([self::DISPENSER_SPENDING_LINE_ONE_ID]);
    }

    public function testShouldUpdateSavedDispenserSpendingLine(): void
    {
        $line = new DispenserSpendingLine(
            Uuid::fromString(self::DISPENSER_SPENDING_LINE_ONE_ID),
            Uuid::fromString(self::DISPENSER_SPENDING_LINE_ONE_ID),
            new \DateTimeImmutable(),
            null,
            null,
            null,
        );
        $this->sut->save($line);

        $actual = $this->conn
            ->prepare("select id, dispenser_id, opened_at from dispensers_spending_lines where id = ?")
            ->executeQuery([self::DISPENSER_SPENDING_LINE_ONE_ID])
            ->fetchAssociative();

        self::assertNotEmpty($actual);
        self::assertEquals(self::DISPENSER_SPENDING_LINE_ONE_ID, $actual['id']);
        self::assertEquals(self::DISPENSER_SPENDING_LINE_ONE_ID, $actual['dispenser_id']);
        self::assertEquals(
            $line->openedAt()->format('Uv'),
            $actual['opened_at'],
        );
    }

    public function testShouldReturnAllLinesForDispenser(): void
    {
        $start = new \DateTimeImmutable();
        $lineOne = new DispenserSpendingLine(
            Uuid::fromString(self::DISPENSER_SPENDING_LINE_ONE_ID),
            Uuid::fromString(self::DISPENSER_SPENDING_LINE_ONE_ID),
            $start,
            $start->modify('+5 seconds'),
            5,
            10,
        );
        $this->sut->save($lineOne);

        $lineTwo = new DispenserSpendingLine(
            Uuid::fromString(self::DISPENSER_SPENDING_LINE_TWO_ID),
            Uuid::fromString(self::DISPENSER_SPENDING_LINE_ONE_ID),
            $start->modify('+10 seconds'),
            $start->modify('+20 seconds'),
            10,
            20,
        );
        $this->sut->save($lineTwo);

        $actual = $this->sut->findAllByDispenserId(Uuid::fromString(self::DISPENSER_SPENDING_LINE_ONE_ID));

        self::assertCount(2, $actual);
        self::assertEquals([$lineOne->id()->toString(), $lineTwo->id()->toString()], array_map(static fn(DispenserSpendingLine $line) => $line->id()->toString(), $actual));
    }
}
