<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250528151031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON avoir
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avoir DROP mois_annee
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avoir ADD PRIMARY KEY (id_livret, id_categ)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX `PRIMARY` ON avoir
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avoir ADD mois_annee DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avoir ADD PRIMARY KEY (id_livret, id_categ, mois_annee)
        SQL);
    }
}
