<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20240203155012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create dispensers spending lines table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('dispensers_spending_lines');

        $table->addColumn('id', Types::GUID)->setNotnull(true)->setLength(36);
        $table->addColumn('dispenser_id', Types::GUID)->setNotnull(true);
        $table->addColumn('opened_at', Types::BIGINT)->setNotnull(true);
        $table->addColumn('closed_at', Types::BIGINT)->setNotnull(false);
        $table->addColumn('duration', Types::INTEGER)->setNotnull(false);
        $table->addColumn('output_volume', Types::DECIMAL)->setNotnull(false);

        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('dispensers_spending_lines');
    }
}
