<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour ajouter les colonnes date_maj et updated_at
 */
final class Version20250420162057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des colonnes date_maj à la table entree et updated_at à la table stock.';
    }

    public function up(Schema $schema): void
    {
        // Ajouter la colonne 'date_maj' à la table 'entree'
        $this->addSql('ALTER TABLE entree ADD date_maj DATETIME NOT NULL');

        // Ajouter la colonne 'updated_at' à la table 'stock'
        $this->addSql('ALTER TABLE stock ADD updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // Supprimer la colonne 'date_maj' de la table 'entree'
        $this->addSql('ALTER TABLE entree DROP COLUMN date_maj');

        // Supprimer la colonne 'updated_at' de la table 'stock'
        $this->addSql('ALTER TABLE stock DROP COLUMN updated_at');
    }
}
