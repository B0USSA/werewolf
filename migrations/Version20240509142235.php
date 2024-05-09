<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240509142235 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rooms DROP FOREIGN KEY FK_7CA11A961FB8D185');
        $this->addSql('DROP INDEX IDX_7CA11A961FB8D185 ON rooms');
        $this->addSql('ALTER TABLE rooms ADD started TINYINT(1) NOT NULL, CHANGE host_id host_id_id INT DEFAULT NULL, CHANGE room_passord room_password VARCHAR(255) DEFAULT NULL, CHANGE public public_room TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE rooms ADD CONSTRAINT FK_7CA11A96DC26D3A4 FOREIGN KEY (host_id_id) REFERENCES comptes (id)');
        $this->addSql('CREATE INDEX IDX_7CA11A96DC26D3A4 ON rooms (host_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rooms DROP FOREIGN KEY FK_7CA11A96DC26D3A4');
        $this->addSql('DROP INDEX IDX_7CA11A96DC26D3A4 ON rooms');
        $this->addSql('ALTER TABLE rooms ADD public TINYINT(1) NOT NULL, DROP public_room, DROP started, CHANGE host_id_id host_id INT DEFAULT NULL, CHANGE room_password room_passord VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE rooms ADD CONSTRAINT FK_7CA11A961FB8D185 FOREIGN KEY (host_id) REFERENCES comptes (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_7CA11A961FB8D185 ON rooms (host_id)');
    }
}
