<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200805093246 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3C54C8C93');
        $this->addSql('DROP INDEX IDX_A3C664D3C54C8C93 ON subscription');
        $this->addSql('ALTER TABLE subscription CHANGE type_id offer_id INT NOT NULL');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D353C674EE FOREIGN KEY (offer_id) REFERENCES offer (id)');
        $this->addSql('CREATE INDEX IDX_A3C664D353C674EE ON subscription (offer_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D353C674EE');
        $this->addSql('DROP INDEX IDX_A3C664D353C674EE ON subscription');
        $this->addSql('ALTER TABLE subscription CHANGE offer_id type_id INT NOT NULL');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3C54C8C93 FOREIGN KEY (type_id) REFERENCES offer (id)');
        $this->addSql('CREATE INDEX IDX_A3C664D3C54C8C93 ON subscription (type_id)');
    }
}
