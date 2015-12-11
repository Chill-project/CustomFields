<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151210205610 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE custom_field_long_choice_options_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE custom_field_long_choice_options (id INT NOT NULL, '
                . 'parent_id INT DEFAULT NULL, '
                . 'key VARCHAR(15) NOT NULL, '
                . 'text jsonb NOT NULL, '
                . 'active boolean NOT NULL,'
                . 'internal_key VARCHAR(50) NOT NULL DEFAULT \'\', '
                . 'PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_14BBB8E0727ACA70 ON custom_field_long_choice_options (parent_id)');
        $this->addSql('ALTER TABLE custom_field_long_choice_options ADD CONSTRAINT cf_long_choice_self_referencing '
                . 'FOREIGN KEY (parent_id) REFERENCES custom_field_long_choice_options (id) '
                . 'NOT DEFERRABLE INITIALLY IMMEDIATE');
        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE custom_field_long_choice_options DROP CONSTRAINT cf_long_choice_self_referencing');
        $this->addSql('DROP SEQUENCE custom_field_long_choice_options_id_seq CASCADE');
        $this->addSql('DROP TABLE custom_field_long_choice_options');
    }
}
