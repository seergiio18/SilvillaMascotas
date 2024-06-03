<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240522111557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAAF760EA80');
        $this->addSql('DROP INDEX IDX_6716CCAAF760EA80 ON pedidos');
        $this->addSql('ALTER TABLE pedidos DROP id_producto, DROP cantidad');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pedidos ADD id_producto INT DEFAULT NULL, ADD cantidad INT NOT NULL');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAAF760EA80 FOREIGN KEY (id_producto) REFERENCES productos (id)');
        $this->addSql('CREATE INDEX IDX_6716CCAAF760EA80 ON pedidos (id_producto)');
    }
}
