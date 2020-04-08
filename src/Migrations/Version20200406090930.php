<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200406090930 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F1F55203D');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA1F55203D');
        $this->addSql('ALTER TABLE user_topic DROP FOREIGN KEY FK_7F8225431F55203D');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, user_id INT NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, parent_id INT DEFAULT NULL, INDEX IDX_9474526C4B89032C (post_id), INDEX IDX_9474526CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dashboard_notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post_id INT DEFAULT NULL, owner_id INT NOT NULL, comment_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, seen TINYINT(1) NOT NULL, INDEX IDX_596D0BE8A76ED395 (user_id), INDEX IDX_596D0BE84B89032C (post_id), INDEX IDX_596D0BE87E3C61F9 (owner_id), INDEX IDX_596D0BE8F8697D13 (comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME DEFAULT NULL, likes_number INT DEFAULT NULL, comments_number INT DEFAULT NULL, distinct_user_comment_number INT DEFAULT NULL, INDEX IDX_5A8A6C8DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_like (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_653627B8A76ED395 (user_id), INDEX IDX_653627B84B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recommandation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, service_id INT DEFAULT NULL, company_id INT DEFAULT NULL, message VARCHAR(255) NOT NULL, create_at DATETIME NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_C7782A28A76ED395 (user_id), INDEX IDX_C7782A28ED5CA9E6 (service_id), INDEX IDX_C7782A28979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dashboard_notification ADD CONSTRAINT FK_596D0BE8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dashboard_notification ADD CONSTRAINT FK_596D0BE84B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE dashboard_notification ADD CONSTRAINT FK_596D0BE87E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dashboard_notification ADD CONSTRAINT FK_596D0BE8F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post_like ADD CONSTRAINT FK_653627B8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post_like ADD CONSTRAINT FK_653627B84B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE recommandation ADD CONSTRAINT FK_C7782A28A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE recommandation ADD CONSTRAINT FK_C7782A28ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE recommandation ADD CONSTRAINT FK_C7782A28979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('DROP TABLE topic');
        $this->addSql('DROP TABLE user_topic');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FCD53EDB6');
        $this->addSql('DROP INDEX IDX_B6BD307F1F55203D ON message');
        $this->addSql('DROP INDEX IDX_B6BD307FCD53EDB6 ON message');
        $this->addSql('ALTER TABLE message ADD topic VARCHAR(50) NOT NULL, DROP topic_id, DROP receiver_id, DROP is_private');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CACD53EDB6');
        $this->addSql('DROP INDEX IDX_BF5476CA1F55203D ON notification');
        $this->addSql('DROP INDEX IDX_BF5476CACD53EDB6 ON notification');
        $this->addSql('ALTER TABLE notification ADD topic VARCHAR(255) NOT NULL, DROP topic_id, DROP receiver_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dashboard_notification DROP FOREIGN KEY FK_596D0BE8F8697D13');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4B89032C');
        $this->addSql('ALTER TABLE dashboard_notification DROP FOREIGN KEY FK_596D0BE84B89032C');
        $this->addSql('ALTER TABLE post_like DROP FOREIGN KEY FK_653627B84B89032C');
        $this->addSql('CREATE TABLE topic (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, type VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_topic (user_id INT NOT NULL, topic_id INT NOT NULL, INDEX IDX_7F822543A76ED395 (user_id), INDEX IDX_7F8225431F55203D (topic_id), PRIMARY KEY(user_id, topic_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_topic ADD CONSTRAINT FK_7F8225431F55203D FOREIGN KEY (topic_id) REFERENCES topic (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_topic ADD CONSTRAINT FK_7F822543A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE dashboard_notification');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_like');
        $this->addSql('DROP TABLE recommandation');
        $this->addSql('ALTER TABLE message ADD topic_id INT DEFAULT NULL, ADD receiver_id INT DEFAULT NULL, ADD is_private TINYINT(1) NOT NULL, DROP topic');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F1F55203D FOREIGN KEY (topic_id) REFERENCES topic (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F1F55203D ON message (topic_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FCD53EDB6 ON message (receiver_id)');
        $this->addSql('ALTER TABLE notification ADD topic_id INT DEFAULT NULL, ADD receiver_id INT DEFAULT NULL, DROP topic');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA1F55203D FOREIGN KEY (topic_id) REFERENCES topic (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CACD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BF5476CA1F55203D ON notification (topic_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CACD53EDB6 ON notification (receiver_id)');
    }
}
