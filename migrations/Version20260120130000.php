<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260120130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add avatar_url to app_user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_user ADD avatar_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_user DROP avatar_url');
    }
}
