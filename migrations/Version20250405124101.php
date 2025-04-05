<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250405124101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_answer (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, answer_id INT NOT NULL, quiz_id INT NOT NULL, INDEX IDX_BF8F5118A76ED395 (user_id), INDEX IDX_BF8F5118AA334807 (answer_id), INDEX IDX_BF8F5118853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer ADD CONSTRAINT FK_BF8F5118A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer ADD CONSTRAINT FK_BF8F5118AA334807 FOREIGN KEY (answer_id) REFERENCES answer (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer ADD CONSTRAINT FK_BF8F5118853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_A412FA92989D9B62 ON quiz (slug)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer DROP FOREIGN KEY FK_BF8F5118A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer DROP FOREIGN KEY FK_BF8F5118AA334807
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer DROP FOREIGN KEY FK_BF8F5118853CD175
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_answer
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_A412FA92989D9B62 ON quiz
        SQL);
    }
}
