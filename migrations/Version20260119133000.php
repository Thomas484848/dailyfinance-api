<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260119133000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename user table to app_user to avoid reserved keyword issues';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" RENAME TO app_user');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_user RENAME TO "user"');
    }
}
