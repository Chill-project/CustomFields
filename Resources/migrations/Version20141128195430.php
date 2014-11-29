<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141128195430 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE SEQUENCE CustomField_id_seq INCREMENT BY 1 MINVALUE 1 START 1;");
        $this->addSql("CREATE SEQUENCE CustomFieldsDefaultGroup_id_seq INCREMENT BY 1 MINVALUE 1 START 1;");
        $this->addSql("CREATE SEQUENCE CustomFieldsGroup_id_seq INCREMENT BY 1 MINVALUE 1 START 1;");
        $this->addSql("CREATE TABLE CustomField (id INT NOT NULL, name JSON NOT NULL, slug VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, active BOOLEAN NOT NULL, ordering DOUBLE PRECISION NOT NULL, options JSON NOT NULL, customFieldGroup_id INT DEFAULT NULL, PRIMARY KEY(id));");
        $this->addSql("CREATE INDEX IDX_40FB5D6DFEC418B ON CustomField (customFieldGroup_id);");
        $this->addSql("CREATE TABLE CustomFieldsDefaultGroup (id INT NOT NULL, entity VARCHAR(255) NOT NULL, customFieldsGroup_id INT DEFAULT NULL, PRIMARY KEY(id));");
        $this->addSql("CREATE INDEX IDX_286DC95DF53B66 ON CustomFieldsDefaultGroup (customFieldsGroup_id);");
        $this->addSql("CREATE UNIQUE INDEX unique_entity ON CustomFieldsDefaultGroup (entity);");
        $this->addSql("CREATE TABLE CustomFieldsGroup (id INT NOT NULL, name JSON NOT NULL, entity VARCHAR(255) NOT NULL, PRIMARY KEY(id));");
        $this->addSql("ALTER TABLE CustomField ADD CONSTRAINT FK_40FB5D6DFEC418B FOREIGN KEY (customFieldGroup_id) REFERENCES CustomFieldsGroup (id) NOT DEFERRABLE INITIALLY IMMEDIATE;");
        $this->addSql("ALTER TABLE CustomFieldsDefaultGroup ADD CONSTRAINT FK_286DC95DF53B66 FOREIGN KEY (customFieldsGroup_id) REFERENCES CustomFieldsGroup (id) NOT DEFERRABLE INITIALLY IMMEDIATE;");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
