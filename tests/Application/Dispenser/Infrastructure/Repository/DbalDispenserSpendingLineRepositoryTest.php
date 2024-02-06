<?php

namespace App\Tests\Application\Dispenser\Infrastructure\Repository;

use App\Dispenser\Domain\Model\SpendingLine;
use App\Dispenser\Domain\Repository\SpendingLineRepository;
use App\Shared\Domain\Uuid;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DbalDispenserSpendingLineRepositoryTest extends WebTestCase
{
    private const DISPENSER_SPENDING_LINE_ONE_ID = "7420caff-23a0-4f5c-9bfa-c47f901502ba";
    private const DISPENSER_SPENDING_LINE_TWO_ID = "7420caff-23a0-4f5c-9bfa-c47f901502bb";
    private SpendingLineRepository $sut;
    private Connection $conn;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = self::getContainer()->get(SpendingLineRepository::class);

        /** @var Connection $conn */
        $this->conn = self::getContainer()->get(Connection::class);
        $this->conn
            ->executeQuery("truncate spending_lines");
    }

    public function testShouldUpdateSavedDispenserSpendingLine(): void
    {
        $line = new SpendingLine(
            Uuid::fromString(self::DISPENSER_SPENDING_LINE_ONE_ID),
            Uuid::fromString(self::DISPENSER_SPENDING_LINE_ONE_ID),
             0.5,
            new \DateTimeImmutable(),
            null,
            null,
            null,
        );
        $this->sut->save($line);

        $actual = $this->conn
            ->prepare("select id, dispenser_id, opened_at from spending_lines where id = ?")
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

    public function testShouldReturnLatestLineForDispenser(): void
    {
        $start = new \DateTimeImmutable();
        $lineOne = new SpendingLine(
            Uuid::fromString(self::DISPENSER_SPENDING_LINE_ONE_ID),
            Uuid::fromString(self::DISPENSER_SPENDING_LINE_ONE_ID),
            0.5,
            $start,
            $start->modify('+5 seconds'),
            5,
            10,
        );
        $this->sut->save($lineOne);

        $lineTwo = new SpendingLine(
            Uuid::fromString(self::DISPENSER_SPENDING_LINE_TWO_ID),
            Uuid::fromString(self::DISPENSER_SPENDING_LINE_ONE_ID),
            0.5,
            $start->modify('+10 second'),
            $start->modify('+20 second'),
            10,
            20,
        );
        $this->sut->save($lineTwo);

        $actual = $this->sut->findLatestForDispenserId(Uuid::fromString(self::DISPENSER_SPENDING_LINE_ONE_ID));

        self::assertEquals(self::DISPENSER_SPENDING_LINE_TWO_ID, $actual->id()->toString());
    }
}
