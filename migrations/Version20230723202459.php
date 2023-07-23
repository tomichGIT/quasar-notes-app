<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230723202459 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__categorias AS SELECT id, categoria, created_at FROM categorias');
        $this->addSql('DROP TABLE categorias');
        $this->addSql('CREATE TABLE categorias (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, categoria VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('INSERT INTO categorias (id, categoria, created_at) SELECT id, categoria, created_at FROM __temp__categorias');
        $this->addSql('DROP TABLE __temp__categorias');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__categorias AS SELECT id, categoria, created_at FROM categorias');
        $this->addSql('DROP TABLE categorias');
        $this->addSql('CREATE TABLE categorias (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, categoria VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('INSERT INTO categorias (id, categoria, created_at) SELECT id, categoria, created_at FROM __temp__categorias');
        $this->addSql('DROP TABLE __temp__categorias');
    }
}
