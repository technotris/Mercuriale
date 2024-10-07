<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241005123133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file_import ADD supplier_id INT DEFAULT NULL, CHANGE filename filename VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE file_import ADD CONSTRAINT FK_2559E8A22ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('CREATE INDEX IDX_2559E8A22ADD6D8C ON file_import (supplier_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file_import DROP FOREIGN KEY FK_2559E8A22ADD6D8C');
        $this->addSql('DROP INDEX IDX_2559E8A22ADD6D8C ON file_import');
        $this->addSql('ALTER TABLE file_import DROP supplier_id, CHANGE filename filename VARCHAR(255) NOT NULL');
    }
}
