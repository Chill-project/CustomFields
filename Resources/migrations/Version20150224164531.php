<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add an option column to customfieldsgroup
 */
class Version20150224164531 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE customfieldsgroup ADD options JSON DEFAULT \'{}\'::json');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE CustomFieldsGroup DROP options');
    }
}
