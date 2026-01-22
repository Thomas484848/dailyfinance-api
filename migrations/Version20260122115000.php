<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260122115000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add valuation fundamentals fields to stock';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE stock ADD revenue_ttm DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD revenue_growth_yoy DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD eps_growth_yoy DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD gross_margin DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD operating_margin DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD net_margin DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD free_cash_flow_ttm DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD ebitda_ttm DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD net_debt DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD net_debt_ebitda DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD price_to_fcf DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD price_to_cash_flow DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD return_on_equity DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD return_on_assets DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD debt_to_equity DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD forward_pe DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD forward_eps DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stock ADD peg_ratio DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE stock DROP peg_ratio');
        $this->addSql('ALTER TABLE stock DROP forward_eps');
        $this->addSql('ALTER TABLE stock DROP forward_pe');
        $this->addSql('ALTER TABLE stock DROP debt_to_equity');
        $this->addSql('ALTER TABLE stock DROP return_on_assets');
        $this->addSql('ALTER TABLE stock DROP return_on_equity');
        $this->addSql('ALTER TABLE stock DROP price_to_cash_flow');
        $this->addSql('ALTER TABLE stock DROP price_to_fcf');
        $this->addSql('ALTER TABLE stock DROP net_debt_ebitda');
        $this->addSql('ALTER TABLE stock DROP net_debt');
        $this->addSql('ALTER TABLE stock DROP ebitda_ttm');
        $this->addSql('ALTER TABLE stock DROP free_cash_flow_ttm');
        $this->addSql('ALTER TABLE stock DROP net_margin');
        $this->addSql('ALTER TABLE stock DROP operating_margin');
        $this->addSql('ALTER TABLE stock DROP gross_margin');
        $this->addSql('ALTER TABLE stock DROP eps_growth_yoy');
        $this->addSql('ALTER TABLE stock DROP revenue_growth_yoy');
        $this->addSql('ALTER TABLE stock DROP revenue_ttm');
    }
}
