<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250420155612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Renommer la colonne `date_maj` en `date_entree` dans la table `entree`';
    }

    public function up(Schema $schema): void
    {
        // Vérification si la colonne `date_maj` existe encore
        $result = $this->connection->fetchOne("SHOW COLUMNS FROM entree LIKE 'date_maj'");

        if ($result) {
            // Supprimer la colonne `date_maj` si elle existe encore
            $this->addSql("ALTER TABLE entree DROP COLUMN date_maj");
        }

        // Vérification si la colonne `date_entree` existe déjà
        $result = $this->connection->fetchOne("SHOW COLUMNS FROM entree LIKE 'date_entree'");

        if (!$result) {
            // Si la colonne n'existe pas, on renomme `date_maj` en `date_entree`
            $this->addSql(<<<'SQL'
                ALTER TABLE entree CHANGE date_maj date_entree DATETIME NOT NULL
            SQL);
        }
    }

    public function down(Schema $schema): void
    {
        // Vérification si la colonne `date_entree` existe déjà
        $result = $this->connection->fetchOne("SHOW COLUMNS FROM entree LIKE 'date_entree'");

        if ($result) {
            // Si la colonne existe, on renomme `date_entree` en `date_maj`
            $this->addSql(<<<'SQL'
                ALTER TABLE entree CHANGE date_entree date_maj DATETIME NOT NULL
            SQL);
        }
    }
}
