<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130521235335 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->skipIf($this->sm->tablesExist(array('os_user_api_tokens')), "os_user_api_tokens table already exists.");

        $this->addSql("CREATE TABLE os_user_api_tokens (id INT AUTO_INCREMENT NOT NULL, uid INT DEFAULT NULL, token VARCHAR(64) NOT NULL, INDEX IDX_5B2B0AD9539B0606 (uid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE os_user_api_tokens ADD CONSTRAINT FK_5B2B0AD9539B0606 FOREIGN KEY (uid) REFERENCES os_user (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("DROP TABLE os_user_api_tokens");
    }
}
