<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240323160757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE publicite CHANGE id_a id_a INT DEFAULT NULL');
        $this->addSql('ALTER TABLE publicite ADD CONSTRAINT FK_1D394E392F22143C FOREIGN KEY (id_a) REFERENCES actualite (id_a)');
        $this->addSql('CREATE INDEX IDX_1D394E392F22143C ON publicite (id_a)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE publicite DROP FOREIGN KEY FK_1D394E392F22143C');
        $this->addSql('DROP INDEX IDX_1D394E392F22143C ON publicite');
        $this->addSql('ALTER TABLE publicite CHANGE id_a id_a INT NOT NULL');
    }
}
