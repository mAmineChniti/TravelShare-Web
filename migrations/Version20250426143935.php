<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250426143935 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chambres DROP FOREIGN KEY fk_hotel_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chambres CHANGE disponible disponible INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chambres ADD CONSTRAINT FK_36C929623243BB18 FOREIGN KEY (hotel_id) REFERENCES hotels (hotel_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE excursions CHANGE image image LONGBLOB NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hotels CHANGE adress adress LONGTEXT DEFAULT NULL, CHANGE image_h image_h LONGBLOB NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offres_voyage CHANGE description description LONGTEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE posts ADD post_title VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_offres_voyage CHANGE status status INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users CHANGE role role INT DEFAULT 0, CHANGE photo photo LONGBLOB DEFAULT NULL, CHANGE compte compte INT DEFAULT 0
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_offres_voyage CHANGE status status TINYINT(1) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE excursions CHANGE image image MEDIUMBLOB NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE posts DROP post_title
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hotels CHANGE adress adress TEXT DEFAULT NULL, CHANGE image_h image_h MEDIUMBLOB NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chambres DROP FOREIGN KEY FK_36C929623243BB18
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chambres CHANGE disponible disponible TINYINT(1) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE chambres ADD CONSTRAINT fk_hotel_id FOREIGN KEY (hotel_id) REFERENCES hotels (hotel_id) ON UPDATE CASCADE ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users CHANGE role role TINYINT(1) DEFAULT 0, CHANGE photo photo MEDIUMBLOB DEFAULT NULL, CHANGE compte compte TINYINT(1) DEFAULT 0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offres_voyage CHANGE description description TEXT DEFAULT NULL
        SQL);
    }
}
