<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200723081015 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE opportunity_notification DROP FOREIGN KEY FK_1738B3A54B89032C');
        $this->addSql('DROP INDEX IDX_1738B3A54B89032C ON opportunity_notification');
        $this->addSql('ALTER TABLE opportunity_notification DROP post_id, DROP created_at');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE opportunity_notification ADD post_id INT NOT NULL, ADD created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE opportunity_notification ADD CONSTRAINT FK_1738B3A54B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('CREATE INDEX IDX_1738B3A54B89032C ON opportunity_notification (post_id)');
    }
}
