<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250620131809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update PointCollecte and related entities';
    }

    public function up(Schema $schema): void
    {
        // Rename and change column Statut to statut in point_collecte
        $this->addSql("ALTER TABLE point_collecte CHANGE COLUMN Statut statut VARCHAR(255) DEFAULT 'actif' NOT NULL");

        // Add columns in collecte table
        $this->addSql(<<<'SQL'
            ALTER TABLE collecte 
            ADD COLUMN agent_id INT DEFAULT NULL,
            ADD COLUMN point_collecte_id INT DEFAULT NULL,
            ADD COLUMN commentaire VARCHAR(255) DEFAULT NULL,
            ADD COLUMN collecte_effectuee TINYINT(1) DEFAULT 0 NOT NULL
        SQL);

        // Add foreign keys
        $this->addSql(<<<'SQL'
            ALTER TABLE collecte 
            ADD CONSTRAINT FK_38C8A55A642B8210 FOREIGN KEY (agent_id) REFERENCES user (id),
            ADD CONSTRAINT FK_38C8A55A66089E2D FOREIGN KEY (point_collecte_id) REFERENCES point_collecte (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // Drop foreign keys
        $this->addSql(<<<'SQL'
            ALTER TABLE collecte 
            DROP FOREIGN KEY FK_38C8A55A642B8210,
            DROP FOREIGN KEY FK_38C8A55A66089E2D
        SQL);

        // Drop columns in collecte
        $this->addSql(<<<'SQL'
            ALTER TABLE collecte 
            DROP COLUMN agent_id,
            DROP COLUMN point_collecte_id,
            DROP COLUMN commentaire,
            DROP COLUMN collecte_effectuee
        SQL);

        // Rename and change column statut back to Statut in point_collecte
        $this->addSql("ALTER TABLE point_collecte CHANGE COLUMN statut Statut VARCHAR(255) DEFAULT NULL");
    }
}
