<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260119130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align index names and defaults with Doctrine mapping';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER INDEX idx_watchlist_item_watchlist_id RENAME TO IDX_1DEA83F683DD0D94');
        $this->addSql('ALTER INDEX idx_watchlist_item_stock_id RENAME TO IDX_1DEA83F6DCD6110');
        $this->addSql('ALTER INDEX idx_watchlist_user_id RENAME TO IDX_340388D3A76ED395');
        $this->addSql('ALTER INDEX idx_password_reset_token_user_id RENAME TO IDX_6B7BA4B6A76ED395');
        $this->addSql('ALTER INDEX idx_password_reset_token_hash RENAME TO IDX_6B7BA4B6B3BC57DA');
        $this->addSql('ALTER INDEX uniq_user_email RENAME TO UNIQ_8D93D649E7927C74');
        $this->addSql('DROP INDEX idx_stock_symbol');
        $this->addSql('DROP INDEX idx_stock_exchange_code');
        $this->addSql('ALTER TABLE stock ALTER active DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE stock ALTER active SET DEFAULT true');
        $this->addSql('CREATE INDEX idx_stock_symbol ON stock (symbol)');
        $this->addSql('CREATE INDEX idx_stock_exchange_code ON stock (exchange_code)');
        $this->addSql('ALTER INDEX UNIQ_8D93D649E7927C74 RENAME TO uniq_user_email');
        $this->addSql('ALTER INDEX IDX_6B7BA4B6B3BC57DA RENAME TO idx_password_reset_token_hash');
        $this->addSql('ALTER INDEX IDX_6B7BA4B6A76ED395 RENAME TO idx_password_reset_token_user_id');
        $this->addSql('ALTER INDEX IDX_340388D3A76ED395 RENAME TO idx_watchlist_user_id');
        $this->addSql('ALTER INDEX IDX_1DEA83F6DCD6110 RENAME TO idx_watchlist_item_stock_id');
        $this->addSql('ALTER INDEX IDX_1DEA83F683DD0D94 RENAME TO idx_watchlist_item_watchlist_id');
    }
}
