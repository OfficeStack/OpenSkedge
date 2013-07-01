<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130616122810 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $tables = $schema->getTables();
        foreach ($tables as $table) {
            if ($table->getName() === "os_user") {
                if ($table->hasForeignKey('FK_7BB0CD52BF396750')) {
                    $this->addSql("ALTER TABLE os_user DROP FOREIGN KEY FK_7BB0CD52BF396750");
                }
            } else if ($table->getName() === "os_clock") {
                if (!$table->hasColumn("uid")) {
                    $this->addSql("ALTER TABLE os_clock ADD uid INT DEFAULT NULL");
                    $this->addSql("UPDATE os_clock SET uid = id");
                }

                if (!$table->hasForeignKey('FK_34E85465539B0606')) {
                    $this->addSql("ALTER TABLE os_clock ADD CONSTRAINT FK_34E85465539B0606 FOREIGN KEY (uid) REFERENCES os_user (id)");
                }

                if (!$table->hasIndex('UNIQ_34E85465539B0606')) {
                    $this->addSql("CREATE UNIQUE INDEX UNIQ_34E85465539B0606 ON os_clock (uid)");
                }
            }
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE os_clock DROP FOREIGN KEY FK_34E85465539B0606");
        $this->addSql("DROP INDEX UNIQ_34E85465539B0606 ON os_clock");
        $this->addSql("ALTER TABLE os_clock DROP uid");
        $this->addSql("ALTER TABLE os_user ADD CONSTRAINT FK_7BB0CD52BF396750 FOREIGN KEY (id) REFERENCES os_clock (id)");
    }
}
