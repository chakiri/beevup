<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200517132906 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company ADD other_category VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD to_company_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DB0A8F8AB FOREIGN KEY (to_company_id) REFERENCES company (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A8A6C8DB0A8F8AB ON post (to_company_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company DROP other_category');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DB0A8F8AB');
        $this->addSql('DROP INDEX UNIQ_5A8A6C8DB0A8F8AB ON post');
        $this->addSql('ALTER TABLE post DROP to_company_id');
    }
}
