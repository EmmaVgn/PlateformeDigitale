<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250404141124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE pdf (id INT AUTO_INCREMENT NOT NULL, modules_id INT NOT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_EF0DB8C60D6DC42 (modules_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pdf ADD CONSTRAINT FK_EF0DB8C60D6DC42 FOREIGN KEY (modules_id) REFERENCES module (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module ADD image_name VARCHAR(255) DEFAULT NULL, DROP file, DROP pdf
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE pdf DROP FOREIGN KEY FK_EF0DB8C60D6DC42
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE pdf
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module ADD file VARCHAR(255) NOT NULL, ADD pdf VARCHAR(255) NOT NULL, DROP image_name
        SQL);
    }
}
