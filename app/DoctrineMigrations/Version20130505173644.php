<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130505173644 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE os_audit_late (id INT AUTO_INCREMENT NOT NULL, uid INT DEFAULT NULL, sid INT DEFAULT NULL, spid INT DEFAULT NULL, pid INT DEFAULT NULL, status VARCHAR(255) NOT NULL, arrivalTime DATETIME DEFAULT NULL, creationTime DATETIME NOT NULL, notes VARCHAR(255) DEFAULT NULL, INDEX IDX_3465AAE8539B0606 (uid), INDEX IDX_3465AAE857167AB4 (sid), INDEX IDX_3465AAE8F2DFCD91 (spid), INDEX IDX_3465AAE85550C4ED (pid), INDEX lateShiftCreated (creationTime), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE os_audit_late ADD CONSTRAINT FK_3465AAE8539B0606 FOREIGN KEY (uid) REFERENCES os_user (id)");
        $this->addSql("ALTER TABLE os_audit_late ADD CONSTRAINT FK_3465AAE857167AB4 FOREIGN KEY (sid) REFERENCES os_schedule (id)");
        $this->addSql("ALTER TABLE os_audit_late ADD CONSTRAINT FK_3465AAE8F2DFCD91 FOREIGN KEY (spid) REFERENCES os_schedule_period (id)");
        $this->addSql("ALTER TABLE os_audit_late ADD CONSTRAINT FK_3465AAE85550C4ED FOREIGN KEY (pid) REFERENCES os_position (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE os_audit_late");
    }
}
