<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250406123838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE module_view (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, module_id INT NOT NULL, viewed_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_44C1EE82A76ED395 (user_id), INDEX IDX_44C1EE82AFC2B591 (module_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE quiz_completion (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, quiz_id INT NOT NULL, completed_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_71B91042A76ED395 (user_id), INDEX IDX_71B91042853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_view ADD CONSTRAINT FK_44C1EE82A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_view ADD CONSTRAINT FK_44C1EE82AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_completion ADD CONSTRAINT FK_71B91042A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_completion ADD CONSTRAINT FK_71B91042853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE module_view DROP FOREIGN KEY FK_44C1EE82A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_view DROP FOREIGN KEY FK_44C1EE82AFC2B591
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_completion DROP FOREIGN KEY FK_71B91042A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_completion DROP FOREIGN KEY FK_71B91042853CD175
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE module_view
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE quiz_completion
        SQL);
    }
}
