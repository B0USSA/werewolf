<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240511103355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE joueurs (id INT AUTO_INCREMENT NOT NULL, dead TINYINT(1) NOT NULL, in_room TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE joueurs_rooms (joueurs_id INT NOT NULL, rooms_id INT NOT NULL, INDEX IDX_173FB56EA3DC7281 (joueurs_id), INDEX IDX_173FB56E8E2368AB (rooms_id), PRIMARY KEY(joueurs_id, rooms_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE joueurs_comptes (joueurs_id INT NOT NULL, comptes_id INT NOT NULL, INDEX IDX_CC7FE653A3DC7281 (joueurs_id), INDEX IDX_CC7FE653DCED588B (comptes_id), PRIMARY KEY(joueurs_id, comptes_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE joueurs_rooms ADD CONSTRAINT FK_173FB56EA3DC7281 FOREIGN KEY (joueurs_id) REFERENCES joueurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE joueurs_rooms ADD CONSTRAINT FK_173FB56E8E2368AB FOREIGN KEY (rooms_id) REFERENCES rooms (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE joueurs_comptes ADD CONSTRAINT FK_CC7FE653A3DC7281 FOREIGN KEY (joueurs_id) REFERENCES joueurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE joueurs_comptes ADD CONSTRAINT FK_CC7FE653DCED588B FOREIGN KEY (comptes_id) REFERENCES comptes (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE joueurs_rooms DROP FOREIGN KEY FK_173FB56EA3DC7281');
        $this->addSql('ALTER TABLE joueurs_rooms DROP FOREIGN KEY FK_173FB56E8E2368AB');
        $this->addSql('ALTER TABLE joueurs_comptes DROP FOREIGN KEY FK_CC7FE653A3DC7281');
        $this->addSql('ALTER TABLE joueurs_comptes DROP FOREIGN KEY FK_CC7FE653DCED588B');
        $this->addSql('DROP TABLE joueurs');
        $this->addSql('DROP TABLE joueurs_rooms');
        $this->addSql('DROP TABLE joueurs_comptes');
    }
}
