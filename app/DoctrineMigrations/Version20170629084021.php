<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170629084021 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE project ADD date_designer DATETIME DEFAULT NULL, ADD designer_accepted TINYINT(1) DEFAULT NULL, ADD for_approval TINYINT(1) DEFAULT NULL, ADD date_executioner DATETIME DEFAULT NULL, ADD executioner_accepted TINYINT(1) DEFAULT NULL, ADD over_date DATETIME DEFAULT NULL, ADD designer_finished_date DATETIME DEFAULT NULL, ADD seen_by_little_boss TINYINT(1) DEFAULT NULL, ADD seen_by_designer TINYINT(1) DEFAULT NULL, ADD seen_by_manager TINYINT(1) DEFAULT NULL, ADD seen_by_executioner TINYINT(1) DEFAULT NULL, ADD seen_by_boss TINYINT(1) DEFAULT NULL, ADD manager_link LONGTEXT DEFAULT NULL, ADD designer_link LONGTEXT DEFAULT NULL, DROP dateDesigner, DROP designerAccepted, DROP dateExecutioner, DROP executionerAccepted, DROP designerFinishedDate, DROP seenByLittleBoss, DROP seenByDesigner, DROP seenByManager, DROP seenByExecutioner, DROP seenByBoss, DROP overDate, DROP forApproval, CHANGE fromuser from_user VARCHAR(255) NOT NULL, CHANGE typetask type_task VARCHAR(255) DEFAULT NULL, CHANGE files manager_files LONGTEXT DEFAULT NULL, CHANGE designerfiles designer_files LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE project ADD dateDesigner DATETIME DEFAULT NULL, ADD designerAccepted TINYINT(1) DEFAULT NULL, ADD dateExecutioner DATETIME DEFAULT NULL, ADD executionerAccepted TINYINT(1) DEFAULT NULL, ADD designerFinishedDate DATETIME DEFAULT NULL, ADD seenByLittleBoss TINYINT(1) DEFAULT NULL, ADD seenByDesigner TINYINT(1) DEFAULT NULL, ADD seenByManager TINYINT(1) DEFAULT NULL, ADD seenByExecutioner TINYINT(1) DEFAULT NULL, ADD files LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, ADD seenByBoss TINYINT(1) DEFAULT NULL, ADD overDate DATETIME DEFAULT NULL, ADD forApproval TINYINT(1) DEFAULT NULL, DROP date_designer, DROP designer_accepted, DROP for_approval, DROP date_executioner, DROP executioner_accepted, DROP over_date, DROP designer_finished_date, DROP seen_by_little_boss, DROP seen_by_designer, DROP seen_by_manager, DROP seen_by_executioner, DROP seen_by_boss, DROP manager_files, DROP manager_link, DROP designer_link, CHANGE from_user fromUser VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE type_task typeTask VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE designer_files designerFiles LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\'');
    }
}
