<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200505142038 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE store_services ADD store_id INT NOT NULL, ADD service_id INT NOT NULL, ADD price NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE store_services ADD CONSTRAINT FK_4D459E93B092A811 FOREIGN KEY (store_id) REFERENCES store (id)');
        $this->addSql('ALTER TABLE store_services ADD CONSTRAINT FK_4D459E93ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_4D459E93B092A811 ON store_services (store_id)');
        $this->addSql('CREATE INDEX IDX_4D459E93ED5CA9E6 ON store_services (service_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE store_services DROP FOREIGN KEY FK_4D459E93B092A811');
        $this->addSql('ALTER TABLE store_services DROP FOREIGN KEY FK_4D459E93ED5CA9E6');
        $this->addSql('DROP INDEX IDX_4D459E93B092A811 ON store_services');
        $this->addSql('DROP INDEX IDX_4D459E93ED5CA9E6 ON store_services');
        $this->addSql('ALTER TABLE store_services DROP store_id, DROP service_id, DROP price');
    }
}
