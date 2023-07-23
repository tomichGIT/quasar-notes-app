<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230723182643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__usuarios AS SELECT id, usuario, pass, created_at, updated_at, deleted_at FROM usuarios');
        $this->addSql('DROP TABLE usuarios');
        $this->addSql('CREATE TABLE usuarios (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, usuario VARCHAR(255) NOT NULL, pass VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO usuarios (id, usuario, pass, created_at, updated_at, deleted_at) SELECT id, usuario, pass, created_at, updated_at, deleted_at FROM __temp__usuarios');
        $this->addSql('DROP TABLE __temp__usuarios');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__usuarios AS SELECT id, usuario, pass, created_at, updated_at, deleted_at FROM usuarios');
        $this->addSql('DROP TABLE usuarios');
        $this->addSql('CREATE TABLE usuarios (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, usuario VARCHAR(255) NOT NULL, pass VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , deleted_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('INSERT INTO usuarios (id, usuario, pass, created_at, updated_at, deleted_at) SELECT id, usuario, pass, created_at, updated_at, deleted_at FROM __temp__usuarios');
        $this->addSql('DROP TABLE __temp__usuarios');
    }
}
