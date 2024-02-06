<?php

declare(strict_types=1);

namespace App\Dispenser\Infrastructure\Repository;

use App\Dispenser\Domain\Model\Dispenser;
use App\Dispenser\Domain\Model\DispenserStatus;
use App\Dispenser\Domain\Repository\DispenserRepository;
use App\Dispenser\Domain\Repository\Exception\DispenserNotFound;
use App\Shared\Domain\Exception\UnexpectedError;
use App\Shared\Domain\Uuid;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Types;
use Psr\Log\LoggerInterface;

class DbalDispenserRepository implements DispenserRepository
{
    private const DATE_FORMAT = 'Uv';
    private const DATE_DESERIALIZE_FORMAT = 'U.v';
    private const TABLE_NAME = 'dispensers';
    private const FIELD_TYPES = [
        'id' => 'guid',
        'state' => 'string',
        'flow_volume' => 'float',
        'created_at' => 'bigint',
    ];

    public function __construct(private readonly Connection $connection, private readonly LoggerInterface $logger)
    {
    }

    /** @throws DispenserNotFound|UnexpectedError */
    final public function findById(Uuid $uuid): Dispenser
    {
        try {
            $data = $this->connection
                ->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE_NAME)
                ->where('id = :id')
                ->setParameter('id', $uuid->toString(), Types::GUID)
                ->executeQuery()
                ->fetchAssociative();
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new UnexpectedError($e);
        }

        if (empty($data)) {
            throw new DispenserNotFound($uuid->toString());
        }

        return $this->deserialize($data);
    }

    /** @throws UnexpectedError */
    final public function save(Dispenser $dispenser): void
    {
        $data = $this->serialize($dispenser);
        $primaryKey = ['id' => $dispenser->id()->toString()];
        try {
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
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new UnexpectedError($e);
        }
    }

    private function serialize(Dispenser $dispenser): array
    {
        return [
            'id' => $dispenser->id()->toString(),
            'flow_volume' => $dispenser->flowVolume(),
            'state' => $dispenser->status()->value,
            'created_at' => $dispenser->createdAt()->format(self::DATE_FORMAT),
        ];
    }

    private function deserialize(array $data): Dispenser
    {
        return new Dispenser(
            Uuid::fromString($data['id']),
            floatval($data['flow_volume']),
            DispenserStatus::from($data['state']),
            $this->deserializeDateTime(strval($data['created_at'])),
        );
    }

    private function deserializeDateTime(string $createdAt): false|\DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat(
            self::DATE_DESERIALIZE_FORMAT,
            substr($createdAt, 0, strlen($createdAt) - 3) . '.' . substr($createdAt, strlen($createdAt) - 3),
        );
    }
}
