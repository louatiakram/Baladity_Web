<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240322234059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE publicite (id_pub INT AUTO_INCREMENT NOT NULL, id_user INT DEFAULT NULL, titre_pub VARCHAR(255) NOT NULL, description_pub VARCHAR(255) NOT NULL, contact_pub INT NOT NULL, localisation_pub VARCHAR(255) NOT NULL, id_a INT NOT NULL, image_pub VARCHAR(255) NOT NULL, offre_pub VARCHAR(255) NOT NULL, INDEX IDX_1D394E396B3CA4B (id_user), PRIMARY KEY(id_pub)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE publicite ADD CONSTRAINT FK_1D394E396B3CA4B FOREIGN KEY (id_user) REFERENCES enduser (id_user)');
        $this->addSql('ALTER TABLE actualite CHANGE date_a date_a DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE publicite DROP FOREIGN KEY FK_1D394E396B3CA4B');
        $this->addSql('DROP TABLE publicite');
        $this->addSql('ALTER TABLE actualite CHANGE date_a date_a DATE DEFAULT NULL');
    }
}
