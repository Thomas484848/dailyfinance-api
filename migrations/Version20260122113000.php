<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260122113000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add valuation score fields to stock';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE stock ADD valuation_score DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD valuation_label VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD valuation_confidence DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD valuation_breakdown JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD valuation_explain_text TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD valuation_updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE stock DROP valuation_updated_at');
        $this->addSql('ALTER TABLE stock DROP valuation_explain_text');
        $this->addSql('ALTER TABLE stock DROP valuation_breakdown');
        $this->addSql('ALTER TABLE stock DROP valuation_confidence');
        $this->addSql('ALTER TABLE stock DROP valuation_label');
        $this->addSql('ALTER TABLE stock DROP valuation_score');
    }
}
