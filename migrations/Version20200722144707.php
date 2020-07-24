<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200722144707 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_historic ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_historic ADD CONSTRAINT FK_4DDF0B56A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4DDF0B56A76ED395 ON user_historic (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_historic DROP FOREIGN KEY FK_4DDF0B56A76ED395');
        $this->addSql('DROP INDEX UNIQ_4DDF0B56A76ED395 ON user_historic');
        $this->addSql('ALTER TABLE user_historic DROP user_id');
    }
}
