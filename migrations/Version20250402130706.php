<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250402130706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE formation ADD objectif_de_formation LONGTEXT NOT NULL, ADD programme LONGTEXT NOT NULL, ADD modalite_acces LONGTEXT NOT NULL, ADD modalite_evaluation LONGTEXT NOT NULL, ADD cout_et_financement LONGTEXT NOT NULL, ADD contact LONGTEXT NOT NULL, ADD accessibilite LONGTEXT NOT NULL, ADD public_cible LONGTEXT NOT NULL, ADD pre_requis LONGTEXT NOT NULL, ADD duree LONGTEXT NOT NULL, ADD date_formation LONGTEXT NOT NULL, ADD lieu LONGTEXT NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, headers LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, queue_name VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE formation DROP objectif_de_formation, DROP programme, DROP modalite_acces, DROP modalite_evaluation, DROP cout_et_financement, DROP contact, DROP accessibilite, DROP public_cible, DROP pre_requis, DROP duree, DROP date_formation, DROP lieu
        SQL);
    }
}
