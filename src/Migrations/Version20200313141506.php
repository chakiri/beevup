<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200313141506 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company CHANGE category_id category_id INT DEFAULT NULL, CHANGE phone phone VARCHAR(255) DEFAULT NULL, CHANGE address_number address_number INT DEFAULT NULL, CHANGE address_street address_street VARCHAR(255) DEFAULT NULL, CHANGE address_post_code address_post_code INT DEFAULT NULL, CHANGE city city VARCHAR(255) DEFAULT NULL, CHANGE country country VARCHAR(255) DEFAULT NULL, CHANGE logo logo VARCHAR(255) DEFAULT NULL, CHANGE video video VARCHAR(255) DEFAULT NULL, CHANGE description description VARCHAR(500) DEFAULT NULL, CHANGE website website VARCHAR(255) DEFAULT NULL, CHANGE latitude latitude NUMERIC(11, 8) DEFAULT NULL, CHANGE longitude longitude NUMERIC(11, 8) DEFAULT NULL, CHANGE introduction introduction VARCHAR(500) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4FBF094F26E94372 ON company (siret)');
        $this->addSql('ALTER TABLE profile CHANGE user_id user_id INT NOT NULL, CHANGE gender gender INT DEFAULT NULL, CHANGE mobile_number mobile_number VARCHAR(255) DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(255) DEFAULT NULL, CHANGE function function INT DEFAULT NULL, CHANGE lastname lastname VARCHAR(255) DEFAULT NULL, CHANGE firstname firstname VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE service CHANGE user_id user_id INT DEFAULT NULL, CHANGE price price NUMERIC(5, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE store CHANGE avatar avatar VARCHAR(255) DEFAULT NULL, CHANGE latitude latitude NUMERIC(11, 8) DEFAULT NULL, CHANGE longitude longitude NUMERIC(11, 8) DEFAULT NULL, CHANGE introduction introduction VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP INDEX IDX_8D93D649979B1AD6, ADD UNIQUE INDEX UNIQ_8D93D649979B1AD6 (company_id)');
        $this->addSql('ALTER TABLE user CHANGE type_id type_id INT DEFAULT NULL, CHANGE modified_at modified_at DATETIME DEFAULT NULL, CHANGE is_deleted is_deleted TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_4FBF094F26E94372 ON company');
        $this->addSql('ALTER TABLE company CHANGE category_id category_id INT DEFAULT NULL, CHANGE phone phone VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE address_number address_number INT DEFAULT NULL, CHANGE address_street address_street VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE address_post_code address_post_code INT DEFAULT NULL, CHANGE city city VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE country country VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE logo logo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE video video VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE description description VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE website website VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE latitude latitude NUMERIC(11, 8) DEFAULT \'NULL\', CHANGE longitude longitude NUMERIC(11, 8) DEFAULT \'NULL\', CHANGE introduction introduction VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE profile CHANGE user_id user_id INT DEFAULT NULL, CHANGE gender gender INT DEFAULT NULL, CHANGE lastname lastname VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE firstname firstname VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE mobile_number mobile_number INT DEFAULT NULL, CHANGE phone_number phone_number INT DEFAULT NULL, CHANGE function function INT DEFAULT NULL');
        $this->addSql('ALTER TABLE service CHANGE user_id user_id INT DEFAULT NULL, CHANGE price price NUMERIC(5, 2) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE store CHANGE latitude latitude NUMERIC(11, 8) DEFAULT \'NULL\', CHANGE longitude longitude NUMERIC(11, 8) DEFAULT \'NULL\', CHANGE avatar avatar VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE introduction introduction VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user DROP INDEX UNIQ_8D93D649979B1AD6, ADD INDEX IDX_8D93D649979B1AD6 (company_id)');
        $this->addSql('ALTER TABLE user CHANGE type_id type_id INT DEFAULT NULL, CHANGE modified_at modified_at DATETIME DEFAULT \'NULL\', CHANGE is_deleted is_deleted TINYINT(1) DEFAULT \'NULL\'');
    }
}
