<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240329085741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messagerie (id_message INT AUTO_INCREMENT NOT NULL, date_message DATE NOT NULL, contenu_message VARCHAR(255) NOT NULL, type_message VARCHAR(255) NOT NULL, receiverId_message INT DEFAULT NULL, senderId_message INT DEFAULT NULL, INDEX IDX_14E8F60CA70E98AA (receiverId_message), INDEX IDX_14E8F60C30E8DF34 (senderId_message), PRIMARY KEY(id_message)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE messagerie ADD CONSTRAINT FK_14E8F60CA70E98AA FOREIGN KEY (receiverId_message) REFERENCES enduser (id_user)');
        $this->addSql('ALTER TABLE messagerie ADD CONSTRAINT FK_14E8F60C30E8DF34 FOREIGN KEY (senderId_message) REFERENCES enduser (id_user)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE messagerie DROP FOREIGN KEY FK_14E8F60CA70E98AA');
        $this->addSql('ALTER TABLE messagerie DROP FOREIGN KEY FK_14E8F60C30E8DF34');
        $this->addSql('DROP TABLE messagerie');
    }
}
