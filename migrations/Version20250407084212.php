<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250407084212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE forum_message (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, topic_id INT DEFAULT NULL, content LONGTEXT NOT NULL, ceated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_47717D0EF675F31B (author_id), INDEX IDX_47717D0E1F55203D (topic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE forum_topic (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_853478CCF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE forum_message ADD CONSTRAINT FK_47717D0EF675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE forum_message ADD CONSTRAINT FK_47717D0E1F55203D FOREIGN KEY (topic_id) REFERENCES forum_topic (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE forum_topic ADD CONSTRAINT FK_853478CCF675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE forum_message DROP FOREIGN KEY FK_47717D0EF675F31B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE forum_message DROP FOREIGN KEY FK_47717D0E1F55203D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE forum_topic DROP FOREIGN KEY FK_853478CCF675F31B
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE forum_message
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE forum_topic
        SQL);
    }
}
