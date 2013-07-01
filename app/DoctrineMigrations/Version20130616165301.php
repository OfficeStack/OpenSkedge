<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130616165301 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        if ($schema->hasTable('os_settings')) {
            $table = $schema->getTable('os_settings');
            if (!$table->hasColumn('massEmail')) {
                $this->addSql("ALTER TABLE os_settings ADD massEmail VARCHAR(255) DEFAULT NULL");
            }
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        if ($schema->hasTable('os_settings')) {
            $table = $schema->getTable('os_settings');
            if (!$table->hasColumn('massEmail')) {
                $this->addSql("ALTER TABLE os_settings DROP massEmail");
            }
        }
    }
}
