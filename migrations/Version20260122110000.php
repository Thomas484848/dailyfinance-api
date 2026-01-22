<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260122110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add eps_ttm column to stock for PE calculation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE stock ADD eps_ttm DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE stock DROP eps_ttm');
    }
}
