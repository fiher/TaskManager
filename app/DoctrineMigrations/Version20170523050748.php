<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170523050748 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, content LONGTEXT DEFAULT NULL, madeBy VARCHAR(255) NOT NULL, creatorRole VARCHAR(255) NOT NULL, zadanieID INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE files (id INT AUTO_INCREMENT NOT NULL, filePath LONGTEXT NOT NULL, zadanieID INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, department VARCHAR(255) DEFAULT NULL, role VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zadanie (id INT AUTO_INCREMENT NOT NULL, department VARCHAR(255) DEFAULT NULL, fromUser VARCHAR(255) NOT NULL, typeTask VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, term DATETIME NOT NULL, date DATETIME NOT NULL, dateDesigner DATETIME DEFAULT NULL, designerAccepted TINYINT(1) DEFAULT NULL, dateExecutioner DATETIME DEFAULT NULL, executionerAccepted TINYINT(1) DEFAULT NULL, designerFinishedDate DATETIME DEFAULT NULL, designer VARCHAR(255) DEFAULT NULL, Executioner VARCHAR(255) DEFAULT NULL, isOver TINYINT(1) NOT NULL, seenByLittleBos TINYINT(1) DEFAULT NULL, seenByDesigner TINYINT(1) DEFAULT NULL, seenByManager TINYINT(1) DEFAULT NULL, seenByExecutioner TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE files');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE zadanie');
    }
}
