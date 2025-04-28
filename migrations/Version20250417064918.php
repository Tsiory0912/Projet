<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to add a unique index on the numero_agent column in the agent table.
 */
final class Version20250417064918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add unique index on numero_agent column in agent table';
    }

    public function up(Schema $schema): void
    {
        // Create unique index on numero_agent
        $this->addSql('ALTER TABLE agent ADD UNIQUE INDEX UNIQ_268B9C9D89D4B7A0 (numero_agent)');
    }

    public function down(Schema $schema): void
    {
        // Drop the unique index
        $this->addSql('ALTER TABLE agent DROP INDEX UNIQ_268B9C9D89D4B7A0');
    }
}
