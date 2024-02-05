<?php

declare(strict_types=1);

namespace App\Dispenser\Infrastructure\Repository;

use App\Dispenser\Domain\Model\Dispenser;
use App\Dispenser\Domain\Model\DispenserStatus;
use App\Dispenser\Domain\Repository\DispenserRepository;
use App\Dispenser\Domain\Repository\Exception\DispenserAlreadyExists;
use App\Dispenser\Domain\Repository\Exception\DispenserNotFound;
use App\Shared\Domain\Uuid;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Types;
use Psr\Log\LoggerInterface;

class DbalDispenserRepository implements DispenserRepository
{
    private const DATE_FORMAT = "U.u";
    private const TABLE_NAME = 'dispensers';
    private const FIELD_TYPES = [
        'id' => 'guid',
        'state' => 'string',
        'flow_volume' => 'float',
        'created_at' => 'string',
    ];

    private Connection $connection;
    private LoggerInterface $logger;

    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->connection = $connection;
    }

    /** @throws DispenserNotFound|Exception */
    final public function findById(Uuid $uuid): Dispenser
    {
        $data = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where('id = :id')
            ->setParameter('id', $uuid->toString(), Types::GUID)
            ->executeQuery()
            ->fetchAssociative();

        if (empty($data)) {
            throw new DispenserNotFound($uuid->toString());
        }

        return $this->deserializeDispenser($data);
    }

    /** @throws Exception */
    final public function save(Dispenser $dispenser): void
    {
        $data = $this->serializeDispenser($dispenser);
        $primaryKey = ['id' => $dispenser->id()->toString()];
        try {
            $this->connection->insert(
                self::TABLE_NAME,
                array_merge($data, $primaryKey),
                self::FIELD_TYPES
            );
        } catch (Exception\UniqueConstraintViolationException) {
            $this->connection->update(
                self::TABLE_NAME,
                array_merge($data, $primaryKey),
                [
                    'id' => $dispenser->id()->toString(),
                ],
                self::FIELD_TYPES
            );
        }
    }

    private function serializeDispenser(Dispenser $dispenser): array
    {
        return [
            'id' => $dispenser->id()->toString(),
            'flow_volume' => $dispenser->flowVolume(),
            'state' => $dispenser->status()->value,
            'created_at' => $dispenser->createdAt()->format(self::DATE_FORMAT),
        ];
    }

    private function deserializeDispenser(array $data): Dispenser
    {
        return new Dispenser(
            Uuid::fromString($data['id']),
            floatval($data['flow_volume']),
            DispenserStatus::from($data['state']),
            \DateTimeImmutable::createFromFormat(
                self::DATE_FORMAT,
                $data['created_at'],
            ),
        );
    }
}
