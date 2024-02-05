<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20240203155010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create dispensers table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('dispensers');

        $table->addColumn('id', Types::GUID)->setNotnull(true)->setLength(36);
        $table->addColumn('state', Types::STRING)->setNotnull(true)->setDefault('closed');
        $table->addColumn('flow_volume', Types::FLOAT)->setNotnull(true);
        $table->addColumn('created_at', Types::BIGINT)->setNotnull(true);

        $table->setPrimaryKey(['id']);
        $table->addIndex(['created_at'], 'idx_dispensers_created_at');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('dispensers');
    }
}
