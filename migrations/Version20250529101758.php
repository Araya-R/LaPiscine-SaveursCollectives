<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250529101758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des contraintes et index sur la table recipe (déjà existantes, donc neutralisées)';
    }

    public function up(Schema $schema): void
    {
        // Contraintes déjà présentes — lignes désactivées pour éviter les doublons

        /*
        $this->addSql(<<<'SQL'
            ALTER TABLE recipe ADD CONSTRAINT FK_DA88B13712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)
        SQL);
        */

        /*
        $this->addSql(<<<'SQL'
            ALTER TABLE recipe ADD CONSTRAINT FK_DA88B137A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        */

        /*
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DA88B13712469DE2 ON recipe (category_id)
        SQL);
        */

        /*
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DA88B137A76ED395 ON recipe (user_id)
        SQL);
        */
    }

    public function down(Schema $schema): void
    {
        // Tu peux laisser le down() tel quel si tu veux, mais il ne sera jamais appelé en production
        $this->addSql(<<<'SQL'
            ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B13712469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B137A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_DA88B13712469DE2 ON recipe
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_DA88B137A76ED395 ON recipe
        SQL);
    }
}
