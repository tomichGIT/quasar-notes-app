<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230723185505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorias_notas (categorias_id INTEGER NOT NULL, notas_id INTEGER NOT NULL, PRIMARY KEY(categorias_id, notas_id), CONSTRAINT FK_E53E783F5792B277 FOREIGN KEY (categorias_id) REFERENCES categorias (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_E53E783F9D65396 FOREIGN KEY (notas_id) REFERENCES notas (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_E53E783F5792B277 ON categorias_notas (categorias_id)');
        $this->addSql('CREATE INDEX IDX_E53E783F9D65396 ON categorias_notas (notas_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__notas AS SELECT id, nota, descripcion, id_usuario, updated_at, created_at, deleted_at FROM notas');
        $this->addSql('DROP TABLE notas');
        $this->addSql('CREATE TABLE notas (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, usuario_id INTEGER NOT NULL, nota VARCHAR(255) NOT NULL, descripcion CLOB NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, CONSTRAINT FK_65776388DB38439E FOREIGN KEY (usuario_id) REFERENCES usuarios (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO notas (id, nota, descripcion, usuario_id, updated_at, created_at, deleted_at) SELECT id, nota, descripcion, id_usuario, updated_at, created_at, deleted_at FROM __temp__notas');
        $this->addSql('DROP TABLE __temp__notas');
        $this->addSql('CREATE INDEX IDX_65776388DB38439E ON notas (usuario_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE categorias_notas');
        $this->addSql('CREATE TEMPORARY TABLE __temp__notas AS SELECT id, usuario_id, nota, descripcion, updated_at, created_at, deleted_at FROM notas');
        $this->addSql('DROP TABLE notas');
        $this->addSql('CREATE TABLE notas (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, id_usuario INTEGER NOT NULL, nota VARCHAR(255) NOT NULL, descripcion CLOB NOT NULL, updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , deleted_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('INSERT INTO notas (id, id_usuario, nota, descripcion, updated_at, created_at, deleted_at) SELECT id, usuario_id, nota, descripcion, updated_at, created_at, deleted_at FROM __temp__notas');
        $this->addSql('DROP TABLE __temp__notas');
    }
}
