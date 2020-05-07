<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200506155628 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE topic ADD type_id INT DEFAULT NULL, DROP type');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1BC54C8C93 FOREIGN KEY (type_id) REFERENCES topic_type (id)');
        $this->addSql('CREATE INDEX IDX_9D40DE1BC54C8C93 ON topic (type_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1BC54C8C93');
        $this->addSql('DROP INDEX IDX_9D40DE1BC54C8C93 ON topic');
        $this->addSql('ALTER TABLE topic ADD type VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP type_id');
    }
}
