<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200404185206 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA23EDC87');
        $this->addSql('DROP INDEX IDX_BF5476CA23EDC87 ON notification');
        $this->addSql('ALTER TABLE notification CHANGE subject_id receiver_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CACD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BF5476CACD53EDB6 ON notification (receiver_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CACD53EDB6');
        $this->addSql('DROP INDEX IDX_BF5476CACD53EDB6 ON notification');
        $this->addSql('ALTER TABLE notification CHANGE receiver_id subject_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA23EDC87 FOREIGN KEY (subject_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BF5476CA23EDC87 ON notification (subject_id)');
    }
}
