<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170627095441 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comments CHANGE zadanieid projectID INT NOT NULL');
        $this->addSql('ALTER TABLE files CHANGE zadanieid projectID INT NOT NULL');
        $this->addSql('ALTER TABLE project ADD working TINYINT(1) DEFAULT NULL, ADD designerFiles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', CHANGE file files LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD fullname VARCHAR(255) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comments CHANGE projectid zadanieID INT NOT NULL');
        $this->addSql('ALTER TABLE files CHANGE projectid zadanieID INT NOT NULL');
        $this->addSql('ALTER TABLE user DROP fullname');
        $this->addSql('ALTER TABLE project DROP working, DROP designerFiles, CHANGE files file LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
