<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230725185016 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorias (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, categoria VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE categorias_notas (categorias_id INTEGER NOT NULL, notas_id INTEGER NOT NULL, PRIMARY KEY(categorias_id, notas_id), CONSTRAINT FK_E53E783F5792B277 FOREIGN KEY (categorias_id) REFERENCES categorias (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_E53E783F9D65396 FOREIGN KEY (notas_id) REFERENCES notas (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_E53E783F5792B277 ON categorias_notas (categorias_id)');
        $this->addSql('CREATE INDEX IDX_E53E783F9D65396 ON categorias_notas (notas_id)');
        $this->addSql('CREATE TABLE notas (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, usuario_id INTEGER NOT NULL, nota VARCHAR(255) NOT NULL, descripcion CLOB NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, CONSTRAINT FK_65776388DB38439E FOREIGN KEY (usuario_id) REFERENCES usuarios (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_65776388DB38439E ON notas (usuario_id)');
        $this->addSql('CREATE TABLE usuarios (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, usuario VARCHAR(255) NOT NULL, pass VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE categorias');
        $this->addSql('DROP TABLE categorias_notas');
        $this->addSql('DROP TABLE notas');
        $this->addSql('DROP TABLE usuarios');
    }
}
