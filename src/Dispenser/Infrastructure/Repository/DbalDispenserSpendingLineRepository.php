<?php

declare(strict_types=1);

namespace App\Dispenser\Infrastructure\Repository;

use App\Dispenser\Domain\Model\DispenserSpendingLine;
use App\Dispenser\Domain\Repository\DispenserSpendingLineRepository;
use App\Shared\Domain\Uuid;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Types\Types;

class DbalDispenserSpendingLineRepository implements DispenserSpendingLineRepository
{
    private const DATE_FORMAT = 'Uv';
    private const DATE_DESERIALIZE_FORMAT = 'U.v';
    private const TABLE_NAME = 'dispensers_spending_lines';
    private const FIELD_TYPES = [
        'id' => 'guid',
        'dispenser_id' => 'guid',
        'opened_at' => 'bigint',
        'closed_at' => 'bigint',
        'flow_volume' => 'float',
        'duration' => 'integer',
        'output_volume' => 'float',
    ];

    public function __construct(private Connection $connection)
    {
    }

    public function findLatestForDispenserId(Uuid $dispenserId): DispenserSpendingLine
    {
        $data = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where('dispenser_id = :dispenser_id')
            ->setParameter('dispenser_id', $dispenserId->toString(), Types::GUID)
            ->orderBy('opened_at', 'DESC')
            ->executeQuery()
            ->fetchAssociative();

        return $this->deserialize($data);
    }

    public function save(DispenserSpendingLine $dispenserSpendingLine): void
    {
        $data = $this->serialize($dispenserSpendingLine);
        $primaryKey = ['id' => $dispenserSpendingLine->id()->toString()];

        try {
            $this->connection->insert(
                self::TABLE_NAME,
                array_merge($data, $primaryKey),
                self::FIELD_TYPES
            );
        } catch (UniqueConstraintViolationException) {
        }
    }

    private function serialize(DispenserSpendingLine $dispenserSpendingLine): array
    {
        return [
            'id' => $dispenserSpendingLine->id()->toString(),
            'dispenser_id' => $dispenserSpendingLine->dispenserId()->toString(),
            'opened_at' => intval($dispenserSpendingLine->openedAt()->format(self::DATE_FORMAT)),
            'closed_at' => intval($dispenserSpendingLine->closedAt()?->format(self::DATE_FORMAT)),
            'flow_volume' => $dispenserSpendingLine->flowVolume(),
            'duration' => $dispenserSpendingLine->duration(),
            'output_volume' => $dispenserSpendingLine->outputVolume(),
        ];
    }

    public function findAllByDispenser(Uuid $dispenserId): array
    {
        $data = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where('dispenser_id = :dispenser_id')
            ->setParameter('dispenser_id', $dispenserId->toString(), Types::GUID)
            ->orderBy('opened_at', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map(static fn($item) => $this->deserialize($item),$data);
    }

    private function deserialize(array $data): DispenserSpendingLine
    {
        return new DispenserSpendingLine(
            Uuid::fromString($data['id']),
            Uuid::fromString($data['dispenser_id']),
            floatval($data['flow_volume']),
            $this->deserializeDateTime(strval($data['opened_at'])),
            null !== $data['closed_at']
                ? $this->deserializeDateTime(strval($data['closed_at']))
                : null
            ,
            (int)$data['duration'] ?? null,
            (float)$data['output_volume'] ?? null,
        );
    }

    private function deserializeDateTime(string $timestampInMilliseconds): false|\DateTimeImmutable
    {
        $seconds = substr($timestampInMilliseconds, 0, strlen($timestampInMilliseconds) - 3);
        $milliseconds = substr($timestampInMilliseconds, strlen($timestampInMilliseconds) - 3);

        return \DateTimeImmutable::createFromFormat(self::DATE_DESERIALIZE_FORMAT, $seconds . '.' . $milliseconds);
    }
}
