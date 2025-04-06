<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250406120849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer ADD question_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer ADD CONSTRAINT FK_BF8F51181E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BF8F51181E27F6BF ON user_answer (question_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer DROP FOREIGN KEY FK_BF8F51181E27F6BF
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_BF8F51181E27F6BF ON user_answer
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer DROP question_id
        SQL);
    }
}
