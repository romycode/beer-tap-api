<?php

namespace App\Tests\Application\Dispenser\Infrastructure\Repository;

use App\Dispenser\Domain\Model\Dispenser;
use App\Dispenser\Domain\Model\DispenserStatus;
use App\Dispenser\Domain\Repository\DispenserRepository;
use App\Dispenser\Domain\Repository\Exception\DispenserNotFound;
use App\Shared\Domain\Uuid;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DbalDispenserRepositoryTest extends WebTestCase
{
    private const DISPENSER_ID = "7420caff-23a0-4f5c-9bfa-c47f901502bb";
    private DispenserRepository $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = self::getContainer()->get(DispenserRepository::class);

        /** @var Connection $conn */
        $conn = self::getContainer()->get(Connection::class);
        $conn->prepare("delete from dispensers where id = ?")->executeQuery([self::DISPENSER_ID]);
    }

    public function testShouldReturnDispenserNotFound(): void
    {
        self::expectException(DispenserNotFound::class);
        self::expectExceptionMessage("dispenser with id: '7420caff-23a0-4f5c-9bfa-c47f901502bb' not found.");

        $this->sut->findById(Uuid::fromString("7420caff-23a0-4f5c-9bfa-c47f901502bb"));
    }

    public function testShouldReturnDispenser(): void
    {
        $expected = new Dispenser(
            Uuid::fromString(self::DISPENSER_ID),
            0.5,
            DispenserStatus::Close,
            new \DateTimeImmutable(),
        );
        $this->sut->save($expected);

        $this->assertDispenser(
            $expected,
            $this->sut->findById(Uuid::fromString(self::DISPENSER_ID))
        );
    }

    public function testShouldUpdateSavedDispenser(): void
    {
        $one = new Dispenser(
            Uuid::fromString(self::DISPENSER_ID),
            0.5,
            DispenserStatus::Close,
            new \DateTimeImmutable(),
        );
        $this->sut->save($one);

        $expected = new Dispenser(
            Uuid::fromString(self::DISPENSER_ID),
            10.5,
            DispenserStatus::Open,
            new \DateTimeImmutable(),
        );
        $this->sut->save($expected);

        $this->assertDispenser(
            $expected,
            $this->sut->findById(Uuid::fromString(self::DISPENSER_ID))
        );
    }

    private function assertDispenser(Dispenser $expected, Dispenser $actual): void
    {
        self::assertEquals($expected->id()->toString(), $actual->id()->toString());
        self::assertEquals($expected->flowVolume(), $actual->flowVolume());
        self::assertEquals($expected->status()->value, $actual->status()->value);
        self::assertEquals($expected->createdAt()->getTimestamp(), $actual->createdAt()->getTimestamp());
    }
}
