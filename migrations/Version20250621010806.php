<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250621010806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE collecte CHANGE dateCollecte createdAt DATETIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pointcollecte CHANGE Statut statut VARCHAR(255) DEFAULT 'actif' NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pointcollecte ADD CONSTRAINT FK_DC3A7B2F9F2C3FAB FOREIGN KEY (zone_id) REFERENCES Zone (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DC3A7B2F9F2C3FAB ON pointcollecte (zone_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE Collecte CHANGE createdAt dateCollecte DATETIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE PointCollecte DROP FOREIGN KEY FK_DC3A7B2F9F2C3FAB
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_DC3A7B2F9F2C3FAB ON PointCollecte
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE PointCollecte CHANGE statut Statut VARCHAR(255) NOT NULL
        SQL);
    }
}
