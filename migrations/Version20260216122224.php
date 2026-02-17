<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260216122224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create booked_appointment table with FK to available_appointment';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE booked_appointment (id UUID NOT NULL, available_appointment_id UUID NOT NULL, email VARCHAR(255) NOT NULL, is_active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_666DCBC74380B4D ON booked_appointment (available_appointment_id)');
        $this->addSql('ALTER TABLE booked_appointment ADD CONSTRAINT FK_666DCBC74380B4D FOREIGN KEY (available_appointment_id) REFERENCES available_appointment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE booked_appointment DROP CONSTRAINT FK_666DCBC74380B4D');
        $this->addSql('DROP TABLE booked_appointment');
    }
}
