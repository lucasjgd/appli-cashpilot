<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250429143852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE avoir (id_livret INT NOT NULL, id_categ INT NOT NULL, budget_max_categ DOUBLE PRECISION NOT NULL, INDEX IDX_659B1A432A22A7EA (id_livret), INDEX IDX_659B1A43ED0B8043 (id_categ), PRIMARY KEY(id_livret, id_categ)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE depense (id INT AUTO_INCREMENT NOT NULL, livret_id INT NOT NULL, categorie_id INT NOT NULL, montant_depense DOUBLE PRECISION NOT NULL, description_depense VARCHAR(255) NOT NULL, date_depense DATE NOT NULL, INDEX IDX_3405975776274781 (livret_id), INDEX IDX_34059757BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE livret (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, nom_livret VARCHAR(100) NOT NULL, INDEX IDX_C151207FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE recurrence (id INT AUTO_INCREMENT NOT NULL, depense_id INT NOT NULL, frequence VARCHAR(50) NOT NULL, date_debut DATE NOT NULL, date_fin DATE DEFAULT NULL, UNIQUE INDEX UNIQ_1FB7F22141D81563 (depense_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, mdp VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avoir ADD CONSTRAINT FK_659B1A432A22A7EA FOREIGN KEY (id_livret) REFERENCES livret (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avoir ADD CONSTRAINT FK_659B1A43ED0B8043 FOREIGN KEY (id_categ) REFERENCES categorie (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE depense ADD CONSTRAINT FK_3405975776274781 FOREIGN KEY (livret_id) REFERENCES livret (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE depense ADD CONSTRAINT FK_34059757BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE livret ADD CONSTRAINT FK_C151207FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recurrence ADD CONSTRAINT FK_1FB7F22141D81563 FOREIGN KEY (depense_id) REFERENCES depense (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE avoir DROP FOREIGN KEY FK_659B1A432A22A7EA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE avoir DROP FOREIGN KEY FK_659B1A43ED0B8043
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE depense DROP FOREIGN KEY FK_3405975776274781
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE depense DROP FOREIGN KEY FK_34059757BCF5E72D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE livret DROP FOREIGN KEY FK_C151207FB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recurrence DROP FOREIGN KEY FK_1FB7F22141D81563
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE avoir
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE categorie
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE depense
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE livret
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE recurrence
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE utilisateur
        SQL);
    }
}
