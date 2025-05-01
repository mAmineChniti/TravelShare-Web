<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250501180331 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Add the slug and post_unique columns
        $this->addSql(<<<'SQL'
            ALTER TABLE posts ADD slug VARCHAR(255) DEFAULT NULL, ADD post_unique VARCHAR(255) DEFAULT NULL
        SQL);

        // Populate the post_unique column with unique values
        $this->addSql(<<<'SQL'
            UPDATE posts SET post_unique = UUID()
        SQL);

        // Populate the slug column by combining postTitle and post_unique
        $this->addSql(<<<'SQL'
            UPDATE posts SET slug = CONCAT(postTitle, '-', post_unique)
        SQL);

        // Alter the columns to make them NOT NULL
        $this->addSql(<<<'SQL'
            ALTER TABLE posts MODIFY slug VARCHAR(255) NOT NULL, MODIFY post_unique VARCHAR(255) NOT NULL
        SQL);

        // Create unique indexes on the slug and post_unique columns
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
            DROP INDEX UNIQ_885DBAFA989D9B62 ON posts
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_885DBAFA8080B212 ON posts
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE posts DROP slug, DROP post_unique
        SQL);
    }
}
