<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320150307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE actualite (id_a INT AUTO_INCREMENT NOT NULL, id_user INT DEFAULT NULL, description_a VARCHAR(255) NOT NULL, image_a VARCHAR(255) NOT NULL, date_a DATE NOT NULL, titre_a VARCHAR(255) NOT NULL, INDEX IDX_549281976B3CA4B (id_user), PRIMARY KEY(id_a)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE enduser (id_user INT AUTO_INCREMENT NOT NULL, id_muni INT DEFAULT NULL, nom_user VARCHAR(255) NOT NULL, email_user VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, type_user VARCHAR(255) NOT NULL, phoneNumber_user VARCHAR(255) NOT NULL, location_user VARCHAR(255) NOT NULL, image_user VARCHAR(255) DEFAULT NULL, isBanned TINYINT(1) DEFAULT NULL, INDEX IDX_B6790ACEFE02D9AE (id_muni), PRIMARY KEY(id_user)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE muni (id_muni INT AUTO_INCREMENT NOT NULL, nom_muni VARCHAR(255) NOT NULL, email_muni VARCHAR(255) NOT NULL, password_muni VARCHAR(255) NOT NULL, imagee_user VARCHAR(255) NOT NULL, PRIMARY KEY(id_muni)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE actualite ADD CONSTRAINT FK_549281976B3CA4B FOREIGN KEY (id_user) REFERENCES enduser (id_user)');
        $this->addSql('ALTER TABLE enduser ADD CONSTRAINT FK_B6790ACEFE02D9AE FOREIGN KEY (id_muni) REFERENCES muni (id_muni)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE actualite DROP FOREIGN KEY FK_549281976B3CA4B');
        $this->addSql('ALTER TABLE enduser DROP FOREIGN KEY FK_B6790ACEFE02D9AE');
        $this->addSql('DROP TABLE actualite');
        $this->addSql('DROP TABLE enduser');
        $this->addSql('DROP TABLE muni');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
