<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240318184458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commentairetache (id_user INT DEFAULT NULL, id_C INT AUTO_INCREMENT NOT NULL, date_C DATE NOT NULL, texte_C VARCHAR(255) NOT NULL, id_T INT DEFAULT NULL, INDEX IDX_160BE8696B3CA4B (id_user), INDEX IDX_160BE8697991D01F (id_T), PRIMARY KEY(id_C)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE end_user (id_user INT AUTO_INCREMENT NOT NULL, id_muni INT DEFAULT NULL, nom_user VARCHAR(255) NOT NULL, email_user VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, type_user VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, location_user VARCHAR(255) NOT NULL, image_user VARCHAR(255) DEFAULT NULL, is_banned TINYINT(1) DEFAULT NULL, INDEX IDX_A3515A0DFE02D9AE (id_muni), PRIMARY KEY(id_user)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE municipality (id_muni INT AUTO_INCREMENT NOT NULL, nom_muni VARCHAR(255) NOT NULL, email_muni VARCHAR(255) NOT NULL, password_muni VARCHAR(255) NOT NULL, imagee_user VARCHAR(255) NOT NULL, PRIMARY KEY(id_muni)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tache (id_user INT DEFAULT NULL, id_T INT AUTO_INCREMENT NOT NULL, nom_Cat VARCHAR(255) NOT NULL, titre_T VARCHAR(30) NOT NULL, pieceJointe_T VARCHAR(255) NOT NULL, date_DT DATE NOT NULL, date_FT DATE NOT NULL, desc_T VARCHAR(255) NOT NULL, etat_T ENUM(\'TO_DO\', \'DOING\', \'DONE\'), INDEX IDX_938720756B3CA4B (id_user), PRIMARY KEY(id_T)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentairetache ADD CONSTRAINT FK_160BE8696B3CA4B FOREIGN KEY (id_user) REFERENCES end_user (id_user)');
        $this->addSql('ALTER TABLE commentairetache ADD CONSTRAINT FK_160BE8697991D01F FOREIGN KEY (id_T) REFERENCES tache (id_T) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE end_user ADD CONSTRAINT FK_A3515A0DFE02D9AE FOREIGN KEY (id_muni) REFERENCES municipality (id_muni)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_938720756B3CA4B FOREIGN KEY (id_user) REFERENCES end_user (id_user)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentairetache DROP FOREIGN KEY FK_160BE8696B3CA4B');
        $this->addSql('ALTER TABLE commentairetache DROP FOREIGN KEY FK_160BE8697991D01F');
        $this->addSql('ALTER TABLE end_user DROP FOREIGN KEY FK_A3515A0DFE02D9AE');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_938720756B3CA4B');
        $this->addSql('DROP TABLE commentairetache');
        $this->addSql('DROP TABLE end_user');
        $this->addSql('DROP TABLE municipality');
        $this->addSql('DROP TABLE tache');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
