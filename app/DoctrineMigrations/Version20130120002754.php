<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130120002754 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->skipIf($this->sm->tablesExist(array('os_archived_clock')), "os_archived_clock table already exists.");

        $this->addSql("CREATE TABLE os_archived_clock (id INT AUTO_INCREMENT NOT NULL, uid INT DEFAULT NULL, week DATETIME NOT NULL, sun VARCHAR(96) NOT NULL, mon VARCHAR(96) NOT NULL, tue VARCHAR(96) NOT NULL, wed VARCHAR(96) NOT NULL, thu VARCHAR(96) NOT NULL, fri VARCHAR(96) NOT NULL, sat VARCHAR(96) NOT NULL, INDEX IDX_E2AE7B56539B0606 (uid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE os_archived_clock ADD CONSTRAINT FK_E2AE7B56539B0606 FOREIGN KEY (uid) REFERENCES os_user (id)");
        $this->addSql("ALTER TABLE os_clock CHANGE last_clock unix_last_clock int(11) NOT NULL");
        $this->addSql("ALTER TABLE os_clock ADD last_clock DATETIME NOT NULL");
        $this->addSql("UPDATE os_clock SET last_clock=FROM_UNIXTIME(unix_last_clock)");
        $this->addSql("ALTER TABLE os_clock DROP unix_last_clock");
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("DROP TABLE os_archived_clock");
        $this->addSql("ALTER TABLE os_clock CHANGE last_clock datetime_last_clock DATETIME NOT NULL");
        $this->addSql("ALTER TABLE os_clock ADD last_clock int(11) NOT NULL");
        $this->addSql("UPDATE os_clock SET last_clock=UNIX_TIMESTAMP(last_clock)");
        $this->addSql("ALTER TABLE os_clock DROP datetime_last_clock");
    }
}
