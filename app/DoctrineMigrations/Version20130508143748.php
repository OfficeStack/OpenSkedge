<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130508143748 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("CREATE TABLE os_shift (id INT AUTO_INCREMENT NOT NULL, uid INT DEFAULT NULL, puid INT DEFAULT NULL, sid INT DEFAULT NULL, spid INT DEFAULT NULL, pid INT DEFAULT NULL, status VARCHAR(255) NOT NULL, creationTime DATETIME NOT NULL, startTime DATETIME NOT NULL, endTime DATETIME NOT NULL, notes VARCHAR(255) DEFAULT NULL, INDEX IDX_2F98D1B2539B0606 (uid), INDEX IDX_2F98D1B2E6A1A094 (puid), INDEX IDX_2F98D1B257167AB4 (sid), INDEX IDX_2F98D1B2F2DFCD91 (spid), INDEX IDX_2F98D1B25550C4ED (pid), INDEX users (uid, puid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE os_shift ADD CONSTRAINT FK_2F98D1B2539B0606 FOREIGN KEY (uid) REFERENCES os_user (id)");
        $this->addSql("ALTER TABLE os_shift ADD CONSTRAINT FK_2F98D1B2E6A1A094 FOREIGN KEY (puid) REFERENCES os_user (id)");
        $this->addSql("ALTER TABLE os_shift ADD CONSTRAINT FK_2F98D1B257167AB4 FOREIGN KEY (sid) REFERENCES os_schedule (id)");
        $this->addSql("ALTER TABLE os_shift ADD CONSTRAINT FK_2F98D1B2F2DFCD91 FOREIGN KEY (spid) REFERENCES os_schedule_period (id)");
        $this->addSql("ALTER TABLE os_shift ADD CONSTRAINT FK_2F98D1B25550C4ED FOREIGN KEY (pid) REFERENCES os_position (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("DROP TABLE os_shift");
    }
}
