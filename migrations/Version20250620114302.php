<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250620114302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE collecte ADD commentaire VARCHAR(255) DEFAULT NULL, ADD collecteEffectuee TINYINT(1) NOT NULL, ADD agent_id INT NOT NULL, ADD pointCollecte_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE collecte ADD CONSTRAINT FK_ACD8286B3414710B FOREIGN KEY (agent_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE collecte ADD CONSTRAINT FK_ACD8286B48A8C6C3 FOREIGN KEY (pointCollecte_id) REFERENCES PointCollecte (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_ACD8286B3414710B ON collecte (agent_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_ACD8286B48A8C6C3 ON collecte (pointCollecte_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pointcollecte ADD typeDechets VARCHAR(255) NOT NULL, ADD latitude DOUBLE PRECISION NOT NULL, ADD longitude DOUBLE PRECISION NOT NULL, ADD reference VARCHAR(255) DEFAULT NULL, ADD photo VARCHAR(255) DEFAULT NULL, ADD description VARCHAR(255) DEFAULT NULL, CHANGE tournee_id tournee_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE signalement ADD type VARCHAR(50) NOT NULL, ADD photo VARCHAR(255) DEFAULT NULL, ADD statut VARCHAR(255) NOT NULL, ADD dateTraitement DATETIME DEFAULT NULL, ADD commentaireTraitement VARCHAR(255) DEFAULT NULL, ADD citoyen_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE signalement ADD CONSTRAINT FK_7229DEC343787BBA FOREIGN KEY (citoyen_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7229DEC343787BBA ON signalement (citoyen_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP role
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE Collecte DROP FOREIGN KEY FK_ACD8286B3414710B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE Collecte DROP FOREIGN KEY FK_ACD8286B48A8C6C3
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_ACD8286B3414710B ON Collecte
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_ACD8286B48A8C6C3 ON Collecte
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE Collecte DROP commentaire, DROP collecteEffectuee, DROP agent_id, DROP pointCollecte_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE PointCollecte DROP typeDechets, DROP latitude, DROP longitude, DROP reference, DROP photo, DROP description, CHANGE tournee_id tournee_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE Signalement DROP FOREIGN KEY FK_7229DEC343787BBA
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_7229DEC343787BBA ON Signalement
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE Signalement DROP type, DROP photo, DROP statut, DROP dateTraitement, DROP commentaireTraitement, DROP citoyen_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` ADD role VARCHAR(255) NOT NULL
        SQL);
    }
}
