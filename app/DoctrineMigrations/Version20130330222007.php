<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20130330222007 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->skipIf($this->sm->tablesExist(array('os_audit_ips')), "os_audit_ips table already exists.");
        $this->skipIf($this->sm->tablesExist(array('os_settings')), "os_settings table already exists.");

        $this->addSql("CREATE TABLE os_audit_ips (id INT AUTO_INCREMENT NOT NULL, ip VARCHAR(255) NOT NULL, name VARCHAR(64) DEFAULT NULL, allowed_to_clock TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_CF6F1072A5E3B32D (ip), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE os_settings (id INT AUTO_INCREMENT NOT NULL, brandName VARCHAR(64) NOT NULL, pruneAfter INT NOT NULL, weekStartDay VARCHAR(9) NOT NULL, weekStartDayClock VARCHAR(9) NOT NULL, defaultTimeResolution VARCHAR(255) NOT NULL, startHour VARCHAR(255) NOT NULL, endHour VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("INSERT INTO os_settings (id, brandName, pruneAfter, weekStartDay, weekStartDayClock, defaultTimeResolution, startHour, endHour) VALUES (1, 'OpenSkedge', 12, 'sunday', 'sunday', '1 hour', '07:00:00', '23:00:00');");
        $this->addSql("INSERT INTO os_audit_ips (ip, name, allowed_to_clock) VALUES ('127.0.0.1', 'Server', true);");
        $this->addSql("INSERT INTO os_audit_ips (ip, name, allowed_to_clock) VALUES ('::1', 'Server', true);");
        $this->addSql("INSERT INTO os_audit_ips (ip, name, allowed_to_clock) VALUES ('fe80::1', 'Server', true);");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("DROP TABLE os_audit_ips");
        $this->addSql("DROP TABLE os_settings");
    }
}
