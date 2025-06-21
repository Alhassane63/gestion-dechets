<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250620062655 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE DemandeCollecte (id INT AUTO_INCREMENT NOT NULL, dateDemande DATETIME NOT NULL, statut VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, Citoyen_id INT DEFAULT NULL, INDEX IDX_60A24C27C25786A (Citoyen_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE PointCollecte (id INT AUTO_INCREMENT NOT NULL, Adresse VARCHAR(255) NOT NULL, Statut VARCHAR(255) NOT NULL, tournee_id INT DEFAULT NULL, INDEX IDX_DC3A7B2FF661D013 (tournee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE ProblemeSignale (id INT AUTO_INCREMENT NOT NULL, dateSignal DATETIME NOT NULL, message LONGTEXT NOT NULL, Utilisateur_id INT DEFAULT NULL, point_id INT NOT NULL, INDEX IDX_972EF89836EC9997 (Utilisateur_id), INDEX IDX_972EF898C028CEA2 (point_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE Tournee (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, Nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE DemandeCollecte ADD CONSTRAINT FK_60A24C27C25786A FOREIGN KEY (Citoyen_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE PointCollecte ADD CONSTRAINT FK_DC3A7B2FF661D013 FOREIGN KEY (tournee_id) REFERENCES Tournee (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ProblemeSignale ADD CONSTRAINT FK_972EF89836EC9997 FOREIGN KEY (Utilisateur_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ProblemeSignale ADD CONSTRAINT FK_972EF898C028CEA2 FOREIGN KEY (point_id) REFERENCES PointCollecte (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE DemandeCollecte DROP FOREIGN KEY FK_60A24C27C25786A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE PointCollecte DROP FOREIGN KEY FK_DC3A7B2FF661D013
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ProblemeSignale DROP FOREIGN KEY FK_972EF89836EC9997
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ProblemeSignale DROP FOREIGN KEY FK_972EF898C028CEA2
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE DemandeCollecte
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE PointCollecte
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE ProblemeSignale
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE Tournee
        SQL);
    }
}
