<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204122224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE document_id_seq CASCADE');
        $this->addSql('CREATE TABLE available_appointment (id UUID NOT NULL, date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, duration_minutes INT NOT NULL, is_active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE document');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE document_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE document (id SERIAL NOT NULL, embedding vector NOT NULL, content TEXT NOT NULL, source_type TEXT NOT NULL, source_name TEXT NOT NULL, chunk_number INT DEFAULT NULL, metadata JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE available_appointment');
    }
}
