<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200716082305 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE posts_notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post_id INT DEFAULT NULL, owner_id INT NOT NULL, comment_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, seen TINYINT(1) NOT NULL, INDEX IDX_1DEF4385A76ED395 (user_id), INDEX IDX_1DEF43854B89032C (post_id), INDEX IDX_1DEF43857E3C61F9 (owner_id), INDEX IDX_1DEF4385F8697D13 (comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE posts_notification ADD CONSTRAINT FK_1DEF4385A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE posts_notification ADD CONSTRAINT FK_1DEF43854B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE posts_notification ADD CONSTRAINT FK_1DEF43857E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE posts_notification ADD CONSTRAINT FK_1DEF4385F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('DROP TABLE dashboard_notification');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dashboard_notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post_id INT DEFAULT NULL, owner_id INT NOT NULL, comment_id INT DEFAULT NULL, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, seen TINYINT(1) NOT NULL, INDEX IDX_596D0BE84B89032C (post_id), INDEX IDX_596D0BE8F8697D13 (comment_id), INDEX IDX_596D0BE8A76ED395 (user_id), INDEX IDX_596D0BE87E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE dashboard_notification ADD CONSTRAINT FK_596D0BE84B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE dashboard_notification ADD CONSTRAINT FK_596D0BE87E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dashboard_notification ADD CONSTRAINT FK_596D0BE8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dashboard_notification ADD CONSTRAINT FK_596D0BE8F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('DROP TABLE posts_notification');
    }
}
