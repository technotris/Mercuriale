<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241004161615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE file_import (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_FILE_NAME (filename), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(6) NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_PRODUCT_CODE (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supplier (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_SUPPLIER_NAME (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supplier_product (id INT AUTO_INCREMENT NOT NULL, supplier_id INT DEFAULT NULL, product_id INT DEFAULT NULL, import_id INT DEFAULT NULL, price INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_522F70B22ADD6D8C (supplier_id), INDEX IDX_522F70B24584665A (product_id), INDEX IDX_522F70B2B6A263D9 (import_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE supplier_product ADD CONSTRAINT FK_522F70B22ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('ALTER TABLE supplier_product ADD CONSTRAINT FK_522F70B24584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE supplier_product ADD CONSTRAINT FK_522F70B2B6A263D9 FOREIGN KEY (import_id) REFERENCES file_import (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE supplier_product DROP FOREIGN KEY FK_522F70B22ADD6D8C');
        $this->addSql('ALTER TABLE supplier_product DROP FOREIGN KEY FK_522F70B24584665A');
        $this->addSql('ALTER TABLE supplier_product DROP FOREIGN KEY FK_522F70B2B6A263D9');
        $this->addSql('DROP TABLE file_import');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE supplier');
        $this->addSql('DROP TABLE supplier_product');
        $this->addSql('DROP TABLE `user`');
    }
}
