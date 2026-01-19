<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260119123000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add index on password reset token hash';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX idx_password_reset_token_hash ON password_reset_token (token_hash)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_password_reset_token_hash');
    }
}
