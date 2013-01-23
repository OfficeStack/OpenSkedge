<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130120012237 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $indices = $this->sm->listTableIndexes('os_archived_clock');
        foreach($indices as $index) {
            if($index->getName()=='week') {
                $skip = true;
            }
        }
        $this->skipIf($skip, "Index already exists on os_archived_clock.");
        $this->addSql("CREATE INDEX week ON os_archived_clock (week)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("DROP INDEX week ON os_archived_clock");
    }
}
