<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260116150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create aggregator tables: instrument, metrics, provider_cache, symbol_alias, job, job_run';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE instrument (
            id VARCHAR(64) NOT NULL,
            symbol VARCHAR(64) NOT NULL,
            exchange_code VARCHAR(16) DEFAULT NULL,
            name VARCHAR(255) DEFAULT NULL,
            country VARCHAR(64) DEFAULT NULL,
            currency VARCHAR(8) DEFAULT NULL,
            sector VARCHAR(128) DEFAULT NULL,
            industry VARCHAR(128) DEFAULT NULL,
            isin VARCHAR(32) DEFAULT NULL,
            active BOOLEAN NOT NULL DEFAULT true,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX uniq_instrument_symbol_exchange ON instrument (symbol, exchange_code)');
        $this->addSql('CREATE INDEX idx_instrument_symbol ON instrument (symbol)');
        $this->addSql('CREATE INDEX idx_instrument_exchange_code ON instrument (exchange_code)');
        $this->addSql('CREATE INDEX idx_instrument_country ON instrument (country)');

        $this->addSql('CREATE TABLE symbol_alias (
            id VARCHAR(64) NOT NULL,
            instrument_id VARCHAR(64) NOT NULL,
            provider VARCHAR(32) NOT NULL,
            symbol VARCHAR(32) NOT NULL,
            exchange_code VARCHAR(16) DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX uniq_symbol_alias_provider_symbol_exchange ON symbol_alias (provider, symbol, exchange_code)');
        $this->addSql('CREATE INDEX idx_symbol_alias_instrument_id ON symbol_alias (instrument_id)');
        $this->addSql('CREATE INDEX idx_symbol_alias_provider ON symbol_alias (provider)');
        $this->addSql('ALTER TABLE symbol_alias ADD CONSTRAINT fk_symbol_alias_instrument FOREIGN KEY (instrument_id) REFERENCES instrument (id) ON DELETE CASCADE');

        $this->addSql('CREATE TABLE provider_cache (
            id VARCHAR(64) NOT NULL,
            instrument_id VARCHAR(64) NOT NULL,
            provider VARCHAR(32) NOT NULL,
            endpoint VARCHAR(32) NOT NULL,
            payload_json TEXT NOT NULL,
            fetched_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            ttl_seconds INT NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_provider_cache_instrument_id ON provider_cache (instrument_id)');
        $this->addSql('CREATE INDEX idx_provider_cache_provider_endpoint ON provider_cache (provider, endpoint)');
        $this->addSql('CREATE INDEX idx_provider_cache_fetched_at ON provider_cache (fetched_at)');
        $this->addSql('ALTER TABLE provider_cache ADD CONSTRAINT fk_provider_cache_instrument FOREIGN KEY (instrument_id) REFERENCES instrument (id) ON DELETE CASCADE');

        $this->addSql('CREATE TABLE metrics (
            id VARCHAR(64) NOT NULL,
            instrument_id VARCHAR(64) NOT NULL,
            as_of_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            price DOUBLE PRECISION DEFAULT NULL,
            open DOUBLE PRECISION DEFAULT NULL,
            high DOUBLE PRECISION DEFAULT NULL,
            low DOUBLE PRECISION DEFAULT NULL,
            close DOUBLE PRECISION DEFAULT NULL,
            volume DOUBLE PRECISION DEFAULT NULL,
            market_cap DOUBLE PRECISION DEFAULT NULL,
            pe DOUBLE PRECISION DEFAULT NULL,
            eps DOUBLE PRECISION DEFAULT NULL,
            dividend_yield DOUBLE PRECISION DEFAULT NULL,
            revenue_ttm DOUBLE PRECISION DEFAULT NULL,
            net_income_ttm DOUBLE PRECISION DEFAULT NULL,
            gross_margin DOUBLE PRECISION DEFAULT NULL,
            operating_margin DOUBLE PRECISION DEFAULT NULL,
            profit_margin DOUBLE PRECISION DEFAULT NULL,
            dividend_per_share DOUBLE PRECISION DEFAULT NULL,
            payout_ratio DOUBLE PRECISION DEFAULT NULL,
            revenue_per_share DOUBLE PRECISION DEFAULT NULL,
            eps_diluted DOUBLE PRECISION DEFAULT NULL,
            shares_outstanding DOUBLE PRECISION DEFAULT NULL,
            float_shares DOUBLE PRECISION DEFAULT NULL,
            beta DOUBLE PRECISION DEFAULT NULL,
            week52_high DOUBLE PRECISION DEFAULT NULL,
            week52_low DOUBLE PRECISION DEFAULT NULL,
            avg_volume DOUBLE PRECISION DEFAULT NULL,
            enterprise_value DOUBLE PRECISION DEFAULT NULL,
            ebitda_ttm DOUBLE PRECISION DEFAULT NULL,
            free_cash_flow_ttm DOUBLE PRECISION DEFAULT NULL,
            operating_cash_flow_ttm DOUBLE PRECISION DEFAULT NULL,
            gross_profit_ttm DOUBLE PRECISION DEFAULT NULL,
            total_debt DOUBLE PRECISION DEFAULT NULL,
            total_cash DOUBLE PRECISION DEFAULT NULL,
            debt_to_equity DOUBLE PRECISION DEFAULT NULL,
            current_ratio DOUBLE PRECISION DEFAULT NULL,
            quick_ratio DOUBLE PRECISION DEFAULT NULL,
            price_to_book DOUBLE PRECISION DEFAULT NULL,
            price_to_sales DOUBLE PRECISION DEFAULT NULL,
            peg_ratio DOUBLE PRECISION DEFAULT NULL,
            ev_to_ebitda DOUBLE PRECISION DEFAULT NULL,
            ev_to_revenue DOUBLE PRECISION DEFAULT NULL,
            book_value_per_share DOUBLE PRECISION DEFAULT NULL,
            roa DOUBLE PRECISION DEFAULT NULL,
            roe DOUBLE PRECISION DEFAULT NULL,
            roi DOUBLE PRECISION DEFAULT NULL,
            price_source VARCHAR(32) DEFAULT NULL,
            open_source VARCHAR(32) DEFAULT NULL,
            high_source VARCHAR(32) DEFAULT NULL,
            low_source VARCHAR(32) DEFAULT NULL,
            close_source VARCHAR(32) DEFAULT NULL,
            volume_source VARCHAR(32) DEFAULT NULL,
            market_cap_source VARCHAR(32) DEFAULT NULL,
            pe_source VARCHAR(32) DEFAULT NULL,
            eps_source VARCHAR(32) DEFAULT NULL,
            dividend_yield_source VARCHAR(32) DEFAULT NULL,
            revenue_ttm_source VARCHAR(32) DEFAULT NULL,
            net_income_ttm_source VARCHAR(32) DEFAULT NULL,
            gross_margin_source VARCHAR(32) DEFAULT NULL,
            operating_margin_source VARCHAR(32) DEFAULT NULL,
            profit_margin_source VARCHAR(32) DEFAULT NULL,
            dividend_per_share_source VARCHAR(32) DEFAULT NULL,
            payout_ratio_source VARCHAR(32) DEFAULT NULL,
            revenue_per_share_source VARCHAR(32) DEFAULT NULL,
            eps_diluted_source VARCHAR(32) DEFAULT NULL,
            shares_outstanding_source VARCHAR(32) DEFAULT NULL,
            float_shares_source VARCHAR(32) DEFAULT NULL,
            beta_source VARCHAR(32) DEFAULT NULL,
            week52_high_source VARCHAR(32) DEFAULT NULL,
            week52_low_source VARCHAR(32) DEFAULT NULL,
            avg_volume_source VARCHAR(32) DEFAULT NULL,
            enterprise_value_source VARCHAR(32) DEFAULT NULL,
            ebitda_ttm_source VARCHAR(32) DEFAULT NULL,
            free_cash_flow_ttm_source VARCHAR(32) DEFAULT NULL,
            operating_cash_flow_ttm_source VARCHAR(32) DEFAULT NULL,
            gross_profit_ttm_source VARCHAR(32) DEFAULT NULL,
            total_debt_source VARCHAR(32) DEFAULT NULL,
            total_cash_source VARCHAR(32) DEFAULT NULL,
            debt_to_equity_source VARCHAR(32) DEFAULT NULL,
            current_ratio_source VARCHAR(32) DEFAULT NULL,
            quick_ratio_source VARCHAR(32) DEFAULT NULL,
            price_to_book_source VARCHAR(32) DEFAULT NULL,
            price_to_sales_source VARCHAR(32) DEFAULT NULL,
            peg_ratio_source VARCHAR(32) DEFAULT NULL,
            ev_to_ebitda_source VARCHAR(32) DEFAULT NULL,
            ev_to_revenue_source VARCHAR(32) DEFAULT NULL,
            book_value_per_share_source VARCHAR(32) DEFAULT NULL,
            roa_source VARCHAR(32) DEFAULT NULL,
            roe_source VARCHAR(32) DEFAULT NULL,
            roi_source VARCHAR(32) DEFAULT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_metrics_instrument_id ON metrics (instrument_id)');
        $this->addSql('CREATE INDEX idx_metrics_as_of_date ON metrics (as_of_date)');
        $this->addSql('ALTER TABLE metrics ADD CONSTRAINT fk_metrics_instrument FOREIGN KEY (instrument_id) REFERENCES instrument (id) ON DELETE CASCADE');

        $this->addSql('CREATE TABLE job (
            id VARCHAR(64) NOT NULL,
            type VARCHAR(64) NOT NULL,
            status VARCHAR(32) NOT NULL DEFAULT \'pending\',
            meta_json TEXT DEFAULT NULL,
            started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            finished_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_job_type ON job (type)');
        $this->addSql('CREATE INDEX idx_job_status ON job (status)');

        $this->addSql('CREATE TABLE job_run (
            id VARCHAR(64) NOT NULL,
            job_id VARCHAR(64) NOT NULL,
            instrument_id VARCHAR(64) NOT NULL,
            status VARCHAR(32) NOT NULL DEFAULT \'pending\',
            error TEXT DEFAULT NULL,
            started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            finished_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_job_run_job_id ON job_run (job_id)');
        $this->addSql('CREATE INDEX idx_job_run_instrument_id ON job_run (instrument_id)');
        $this->addSql('CREATE INDEX idx_job_run_status ON job_run (status)');
        $this->addSql('ALTER TABLE job_run ADD CONSTRAINT fk_job_run_job FOREIGN KEY (job_id) REFERENCES job (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_run ADD CONSTRAINT fk_job_run_instrument FOREIGN KEY (instrument_id) REFERENCES instrument (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE job_run');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE metrics');
        $this->addSql('DROP TABLE provider_cache');
        $this->addSql('DROP TABLE symbol_alias');
        $this->addSql('DROP TABLE instrument');
    }
}
