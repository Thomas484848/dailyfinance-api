<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260121153000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add stock_price_history table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE stock_price_history (id SERIAL NOT NULL, stock_id INT NOT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, price DOUBLE PRECISION DEFAULT NULL, volume DOUBLE PRECISION DEFAULT NULL, source VARCHAR(32) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX stock_price_history_stock_ts_idx ON stock_price_history (stock_id, timestamp)');
        $this->addSql('ALTER TABLE stock_price_history ADD CONSTRAINT FK_7D3A1A8C4B365660 FOREIGN KEY (stock_id) REFERENCES stock (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE stock_price_history');
    }
}
