<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221225105955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mark DROP FOREIGN KEY FK_6674F271BEB939B8');
        $this->addSql('DROP INDEX UNIQ_6674F271BEB939B8 ON mark');
        $this->addSql('ALTER TABLE mark ADD exo_id INT DEFAULT NULL, ADD started_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE fscore_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mark ADD CONSTRAINT FK_6674F271A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE mark ADD CONSTRAINT FK_6674F271DA1C6F33 FOREIGN KEY (exo_id) REFERENCES exercice (id)');
        $this->addSql('CREATE INDEX IDX_6674F271A76ED395 ON mark (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6674F271DA1C6F33 ON mark (exo_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mark DROP FOREIGN KEY FK_6674F271A76ED395');
        $this->addSql('ALTER TABLE mark DROP FOREIGN KEY FK_6674F271DA1C6F33');
        $this->addSql('DROP INDEX IDX_6674F271A76ED395 ON mark');
        $this->addSql('DROP INDEX UNIQ_6674F271DA1C6F33 ON mark');
        $this->addSql('ALTER TABLE mark ADD fscore_id INT DEFAULT NULL, DROP user_id, DROP exo_id, DROP started_at');
        $this->addSql('ALTER TABLE mark ADD CONSTRAINT FK_6674F271BEB939B8 FOREIGN KEY (fscore_id) REFERENCES file (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6674F271BEB939B8 ON mark (fscore_id)');
    }
}
