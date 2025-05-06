<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250506185557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE excursions CHANGE image image VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE excursions ADD CONSTRAINT FK_B044FAB5D7ED1D4B FOREIGN KEY (guide_id) REFERENCES guides (guide_id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamations ADD CONSTRAINT FK_1CAD6B76A76ED395 FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponses ADD CONSTRAINT FK_1E512EC62D6BA2D9 FOREIGN KEY (reclamation_id) REFERENCES reclamations (reclamation_id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users CHANGE phone_num phone_num VARCHAR(15) NOT NULL, CHANGE role role INT DEFAULT 0 NOT NULL, CHANGE compte compte INT DEFAULT 0 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE excursions DROP FOREIGN KEY FK_B044FAB5D7ED1D4B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE excursions CHANGE image image LONGBLOB NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamations DROP FOREIGN KEY FK_1CAD6B76A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponses DROP FOREIGN KEY FK_1E512EC62D6BA2D9
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_1483A5E9E7927C74 ON users
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users CHANGE phone_num phone_num INT NOT NULL, CHANGE role role INT DEFAULT 0, CHANGE compte compte INT DEFAULT 0
        SQL);
    }
}
