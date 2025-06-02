<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602073838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE recurrence (id INT AUTO_INCREMENT NOT NULL, depense_id INT NOT NULL, frequence INT NOT NULL, date_debut DATE NOT NULL, date_fin DATE DEFAULT NULL, UNIQUE INDEX UNIQ_1FB7F22141D81563 (depense_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recurrence ADD CONSTRAINT FK_1FB7F22141D81563 FOREIGN KEY (depense_id) REFERENCES depense (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE depense DROP est_recurrente
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE recurrence DROP FOREIGN KEY FK_1FB7F22141D81563
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE recurrence
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE depense ADD est_recurrente TINYINT(1) NOT NULL
        SQL);
    }
}
