<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250406121937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE progression (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, formation_id INT NOT NULL, progress DOUBLE PRECISION NOT NULL, INDEX IDX_D5B25073A76ED395 (user_id), INDEX IDX_D5B250735200282E (formation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE progression ADD CONSTRAINT FK_D5B25073A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE progression ADD CONSTRAINT FK_D5B250735200282E FOREIGN KEY (formation_id) REFERENCES formation (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_formation DROP progression
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE progression DROP FOREIGN KEY FK_D5B25073A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE progression DROP FOREIGN KEY FK_D5B250735200282E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE progression
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_formation ADD progression INT NOT NULL
        SQL);
    }
}
