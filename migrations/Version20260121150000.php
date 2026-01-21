<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260121150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add color and cover_image to watchlist';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE watchlist ADD color VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE watchlist ADD cover_image TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE watchlist DROP color');
        $this->addSql('ALTER TABLE watchlist DROP cover_image');
    }
}
