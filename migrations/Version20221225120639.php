<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221225120639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mark DROP INDEX UNIQ_6674F271DA1C6F33, ADD INDEX IDX_6674F271DA1C6F33 (exo_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mark DROP INDEX IDX_6674F271DA1C6F33, ADD UNIQUE INDEX UNIQ_6674F271DA1C6F33 (exo_id)');
    }
}
