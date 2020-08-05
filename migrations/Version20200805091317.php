<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200805091317 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE offer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, km INT DEFAULT NULL, services_number INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, type_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, INDEX IDX_A3C664D3979B1AD6 (company_id), INDEX IDX_A3C664D3C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3C54C8C93 FOREIGN KEY (type_id) REFERENCES offer (id)');
        $this->addSql('ALTER TABLE profile CHANGE address_post_code address_post_code INT DEFAULT NULL');
        $this->addSql('ALTER TABLE store ADD external_companies LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3C54C8C93');
        $this->addSql('DROP TABLE offer');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('ALTER TABLE profile CHANGE address_post_code address_post_code INT NOT NULL');
        $this->addSql('ALTER TABLE store DROP external_companies');
    }
}
