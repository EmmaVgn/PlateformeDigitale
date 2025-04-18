<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250418145510 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE feedback_formation (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, formation_id INT DEFAULT NULL, note_contenu INT NOT NULL, note_support INT NOT NULL, commentaire_libre LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_FE047F04A76ED395 (user_id), INDEX IDX_FE047F045200282E (formation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback_formation ADD CONSTRAINT FK_FE047F04A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback_formation ADD CONSTRAINT FK_FE047F045200282E FOREIGN KEY (formation_id) REFERENCES formation (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback_formation DROP FOREIGN KEY FK_FE047F04A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback_formation DROP FOREIGN KEY FK_FE047F045200282E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE feedback_formation
        SQL);
    }
}
