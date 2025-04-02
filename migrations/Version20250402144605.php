<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250402144605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_formations (user_id INT NOT NULL, formation_id INT NOT NULL, INDEX IDX_E7F7E7DBA76ED395 (user_id), INDEX IDX_E7F7E7DB5200282E (formation_id), PRIMARY KEY(user_id, formation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_formations ADD CONSTRAINT FK_E7F7E7DBA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_formations ADD CONSTRAINT FK_E7F7E7DB5200282E FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_formations DROP FOREIGN KEY FK_E7F7E7DBA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_formations DROP FOREIGN KEY FK_E7F7E7DB5200282E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_formations
        SQL);
    }
}
