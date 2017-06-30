<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170629111153 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE files ADD CONSTRAINT FK_635405957FD403F FOREIGN KEY (projectID) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_635405957FD403F ON files (projectID)');
        $this->addSql('ALTER TABLE project DROP manager_files, DROP designer_files');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE files DROP FOREIGN KEY FK_635405957FD403F');
        $this->addSql('DROP INDEX IDX_635405957FD403F ON files');
        $this->addSql('ALTER TABLE project ADD manager_files LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, ADD designer_files LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\'');
    }
}
