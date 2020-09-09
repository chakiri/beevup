<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200908150657 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE message_notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, receiver_id INT DEFAULT NULL, topic_id INT DEFAULT NULL, nb_messages INT DEFAULT NULL, INDEX IDX_7B55432CA76ED395 (user_id), INDEX IDX_7B55432CCD53EDB6 (receiver_id), INDEX IDX_7B55432C1F55203D (topic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, km INT DEFAULT NULL, nb_services INT NOT NULL, nb_users INT NOT NULL, price NUMERIC(5, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post_id INT DEFAULT NULL, comment_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, seen TINYINT(1) NOT NULL, INDEX IDX_14690B19A76ED395 (user_id), INDEX IDX_14690B194B89032C (post_id), INDEX IDX_14690B19F8697D13 (comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, offer_id INT NOT NULL, company_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, is_expired TINYINT(1) NOT NULL, nb_months INT DEFAULT NULL, INDEX IDX_A3C664D353C674EE (offer_id), UNIQUE INDEX UNIQ_A3C664D3979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_historic (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, last_login DATETIME DEFAULT NULL, last_logout DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_4DDF0B56A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE message_notification ADD CONSTRAINT FK_7B55432CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message_notification ADD CONSTRAINT FK_7B55432CCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message_notification ADD CONSTRAINT FK_7B55432C1F55203D FOREIGN KEY (topic_id) REFERENCES topic (id)');
        $this->addSql('ALTER TABLE post_notification ADD CONSTRAINT FK_14690B19A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post_notification ADD CONSTRAINT FK_14690B194B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post_notification ADD CONSTRAINT FK_14690B19F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D353C674EE FOREIGN KEY (offer_id) REFERENCES offer (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE user_historic ADD CONSTRAINT FK_4DDF0B56A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE dashboard_notification');
        $this->addSql('DROP TABLE notification');
        $this->addSql('ALTER TABLE comment DROP parent_id');
        $this->addSql('ALTER TABLE opportunity_notification DROP FOREIGN KEY FK_1738B3A54B89032C');
        $this->addSql('DROP INDEX IDX_1738B3A54B89032C ON opportunity_notification');
        $this->addSql('ALTER TABLE opportunity_notification ADD last_seen DATETIME DEFAULT NULL, DROP post_id, DROP created_at');
        $this->addSql('ALTER TABLE post ADD category_id INT DEFAULT NULL, ADD url_youtube VARCHAR(500) DEFAULT NULL, ADD filename VARCHAR(255) DEFAULT NULL, DROP category, DROP likes_number, DROP comments_number, CHANGE description description VARCHAR(1500) DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D12469DE2 FOREIGN KEY (category_id) REFERENCES post_category (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D12469DE2 ON post (category_id)');
        $this->addSql('ALTER TABLE profile ADD is_onboarding TINYINT(1) DEFAULT NULL, ADD address_post_code INT DEFAULT NULL, ADD job_title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE publicity ADD description VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE service ADD to_individuals TINYINT(1) NOT NULL, ADD to_professionals TINYINT(1) NOT NULL, ADD vat_rate VARCHAR(255) DEFAULT NULL, ADD unity VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE store ADD external_companies LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', CHANGE phone phone VARCHAR(255) DEFAULT NULL, CHANGE address_number address_number VARCHAR(255) NOT NULL, CHANGE introduction introduction VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_function ADD related_to VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D353C674EE');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D12469DE2');
        $this->addSql('CREATE TABLE dashboard_notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post_id INT DEFAULT NULL, owner_id INT NOT NULL, comment_id INT DEFAULT NULL, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, seen TINYINT(1) NOT NULL, INDEX IDX_596D0BE84B89032C (post_id), INDEX IDX_596D0BE8F8697D13 (comment_id), INDEX IDX_596D0BE8A76ED395 (user_id), INDEX IDX_596D0BE87E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, receiver_id INT DEFAULT NULL, topic_id INT DEFAULT NULL, nb_messages INT DEFAULT NULL, INDEX IDX_BF5476CACD53EDB6 (receiver_id), INDEX IDX_BF5476CAA76ED395 (user_id), INDEX IDX_BF5476CA1F55203D (topic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE dashboard_notification ADD CONSTRAINT FK_596D0BE84B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE dashboard_notification ADD CONSTRAINT FK_596D0BE87E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dashboard_notification ADD CONSTRAINT FK_596D0BE8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dashboard_notification ADD CONSTRAINT FK_596D0BE8F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA1F55203D FOREIGN KEY (topic_id) REFERENCES topic (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CACD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE message_notification');
        $this->addSql('DROP TABLE offer');
        $this->addSql('DROP TABLE post_category');
        $this->addSql('DROP TABLE post_notification');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE user_historic');
        $this->addSql('ALTER TABLE comment ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE opportunity_notification ADD post_id INT NOT NULL, ADD created_at DATETIME NOT NULL, DROP last_seen');
        $this->addSql('ALTER TABLE opportunity_notification ADD CONSTRAINT FK_1738B3A54B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('CREATE INDEX IDX_1738B3A54B89032C ON opportunity_notification (post_id)');
        $this->addSql('DROP INDEX IDX_5A8A6C8D12469DE2 ON post');
        $this->addSql('ALTER TABLE post ADD category VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD comments_number INT DEFAULT NULL, DROP url_youtube, DROP filename, CHANGE description description VARCHAR(512) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE category_id likes_number INT DEFAULT NULL');
        $this->addSql('ALTER TABLE profile DROP is_onboarding, DROP address_post_code, DROP job_title');
        $this->addSql('ALTER TABLE publicity DROP description');
        $this->addSql('ALTER TABLE service DROP to_individuals, DROP to_professionals, DROP vat_rate, DROP unity');
        $this->addSql('ALTER TABLE store DROP external_companies, CHANGE phone phone VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE address_number address_number INT NOT NULL, CHANGE introduction introduction VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user_function DROP related_to');
    }
}
