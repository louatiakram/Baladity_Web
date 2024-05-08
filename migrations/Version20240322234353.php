<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240322234353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avis (id_avis INT AUTO_INCREMENT NOT NULL, id_user INT DEFAULT NULL, id_muni INT DEFAULT NULL, id_equipement INT DEFAULT NULL, note_avis INT NOT NULL, commentaire_avis VARCHAR(255) NOT NULL, date_avis DATETIME NOT NULL, INDEX IDX_8F91ABF06B3CA4B (id_user), INDEX IDX_8F91ABF0FE02D9AE (id_muni), INDEX IDX_8F91ABF01D3E4624 (id_equipement), PRIMARY KEY(id_avis)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE enduser (id_user INT AUTO_INCREMENT NOT NULL, id_muni INT DEFAULT NULL, nom_user VARCHAR(255) NOT NULL, email_user VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, type_user VARCHAR(255) NOT NULL, phoneNumber_user VARCHAR(255) NOT NULL, location_user VARCHAR(255) NOT NULL, image_user VARCHAR(255) DEFAULT NULL, isBanned TINYINT(1) DEFAULT NULL, INDEX IDX_B6790ACEFE02D9AE (id_muni), PRIMARY KEY(id_user)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipement (id_equipement INT AUTO_INCREMENT NOT NULL, id_user INT DEFAULT NULL, id_muni INT DEFAULT NULL, reference_eq VARCHAR(255) NOT NULL, nom_eq VARCHAR(255) NOT NULL, categorie_eq VARCHAR(255) NOT NULL, quantite_eq INT NOT NULL, date_ajouteq DATETIME NOT NULL, description_eq VARCHAR(255) NOT NULL, image_eq VARCHAR(255) NOT NULL, INDEX IDX_B8B4C6F36B3CA4B (id_user), INDEX IDX_B8B4C6F3FE02D9AE (id_muni), PRIMARY KEY(id_equipement)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE muni (id_muni INT AUTO_INCREMENT NOT NULL, nom_muni VARCHAR(255) NOT NULL, email_muni VARCHAR(255) NOT NULL, password_muni VARCHAR(255) NOT NULL, imagee_user VARCHAR(255) NOT NULL, PRIMARY KEY(id_muni)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF06B3CA4B FOREIGN KEY (id_user) REFERENCES enduser (id_user)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0FE02D9AE FOREIGN KEY (id_muni) REFERENCES muni (id_muni)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF01D3E4624 FOREIGN KEY (id_equipement) REFERENCES equipement (id_equipement)');
        $this->addSql('ALTER TABLE enduser ADD CONSTRAINT FK_B6790ACEFE02D9AE FOREIGN KEY (id_muni) REFERENCES muni (id_muni)');
        $this->addSql('ALTER TABLE equipement ADD CONSTRAINT FK_B8B4C6F36B3CA4B FOREIGN KEY (id_user) REFERENCES enduser (id_user)');
        $this->addSql('ALTER TABLE equipement ADD CONSTRAINT FK_B8B4C6F3FE02D9AE FOREIGN KEY (id_muni) REFERENCES muni (id_muni)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF06B3CA4B');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0FE02D9AE');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF01D3E4624');
        $this->addSql('ALTER TABLE enduser DROP FOREIGN KEY FK_B6790ACEFE02D9AE');
        $this->addSql('ALTER TABLE equipement DROP FOREIGN KEY FK_B8B4C6F36B3CA4B');
        $this->addSql('ALTER TABLE equipement DROP FOREIGN KEY FK_B8B4C6F3FE02D9AE');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE enduser');
        $this->addSql('DROP TABLE equipement');
        $this->addSql('DROP TABLE muni');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
