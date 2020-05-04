<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200504135204 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company ADD bar_code LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE store ADD default_adviser_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE store ADD CONSTRAINT FK_FF5758776440E1B5 FOREIGN KEY (default_adviser_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_FF5758776440E1B5 ON store (default_adviser_id)');
        $this->addSql('ALTER TABLE user DROP bar_code');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company DROP bar_code');
        $this->addSql('ALTER TABLE store DROP FOREIGN KEY FK_FF5758776440E1B5');
        $this->addSql('DROP INDEX IDX_FF5758776440E1B5 ON store');
        $this->addSql('ALTER TABLE store DROP default_adviser_id');
        $this->addSql('ALTER TABLE user ADD bar_code LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
