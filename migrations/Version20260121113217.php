<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260121113217 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER INDEX uniq_8d93d649e7927c74 RENAME TO UNIQ_88BDF3E9E7927C74');
        $this->addSql('ALTER TABLE stock ALTER exchange_code TYPE VARCHAR(64)');
        $this->addSql('ALTER TABLE stock ALTER mic TYPE VARCHAR(64)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER INDEX uniq_88bdf3e9e7927c74 RENAME TO uniq_8d93d649e7927c74');
        $this->addSql('ALTER TABLE stock ALTER exchange_code TYPE VARCHAR(16)');
        $this->addSql('ALTER TABLE stock ALTER mic TYPE VARCHAR(16)');
    }
}
