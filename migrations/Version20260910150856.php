<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260910150856 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE friendship (id INT AUTO_INCREMENT NOT NULL, statut VARCHAR(20) NOT NULL, date_creation DATETIME NOT NULL, demandeur_id INT NOT NULL, destinataire_id INT NOT NULL, INDEX IDX_7234A45F95A6EE59 (demandeur_id), INDEX IDX_7234A45FA4F84F6E (destinataire_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE friendship ADD CONSTRAINT FK_7234A45F95A6EE59 FOREIGN KEY (demandeur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE friendship ADD CONSTRAINT FK_7234A45FA4F84F6E FOREIGN KEY (destinataire_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE friendship DROP FOREIGN KEY FK_7234A45F95A6EE59');
        $this->addSql('ALTER TABLE friendship DROP FOREIGN KEY FK_7234A45FA4F84F6E');
        $this->addSql('DROP TABLE friendship');
    }
}
