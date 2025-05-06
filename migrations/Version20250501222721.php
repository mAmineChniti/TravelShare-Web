<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250501222721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE hotels ADD description TEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_885DBAFA989D9B62 ON posts (slug)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_885DBAFA8080B212 ON posts (post_unique)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE hotels DROP description
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_885DBAFA989D9B62 ON posts
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_885DBAFA8080B212 ON posts
        SQL);
    }
}
