<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250416143523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE chambres (chambre_id INT AUTO_INCREMENT NOT NULL, hotel_id INT NOT NULL, numero_chambre VARCHAR(255) NOT NULL, type_enu VARCHAR(255) NOT NULL, prix_par_nuit NUMERIC(10, 0) NOT NULL, disponible INT NOT NULL, INDEX fk_hotel_id (hotel_id), PRIMARY KEY(chambre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE comments (comment_id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, commenter_id INT NOT NULL, comment VARCHAR(255) NOT NULL, commented_at DATE NOT NULL, updated_at DATE NOT NULL, INDEX fk_commenter_id (commenter_id), INDEX fk_comment_post_id (post_id), PRIMARY KEY(comment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE excursions (excursion_id INT AUTO_INCREMENT NOT NULL, guide_id INT NOT NULL, title VARCHAR(50) NOT NULL, description VARCHAR(255) NOT NULL, date_excursion DATE NOT NULL, date_fin DATE NOT NULL, image LONGBLOB NOT NULL, prix DOUBLE PRECISION NOT NULL, INDEX fk_id_guide (guide_id), PRIMARY KEY(excursion_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE flagged_content (post_id INT NOT NULL, flagger_id INT NOT NULL, flagged_at DATE NOT NULL, INDEX fk_flagger_id (flagger_id), PRIMARY KEY(post_id, flagger_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE guides (guide_id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, email VARCHAR(50) NOT NULL, phone_num VARCHAR(50) NOT NULL, language VARCHAR(50) NOT NULL, experience INT NOT NULL, PRIMARY KEY(guide_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE hotels (hotel_id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adress LONGTEXT DEFAULT NULL, telephone VARCHAR(255) DEFAULT NULL, capacite_totale INT NOT NULL, image_h LONGBLOB NOT NULL, PRIMARY KEY(hotel_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE likes (post_id INT NOT NULL, liker_id INT NOT NULL, INDEX liker_id (liker_id), PRIMARY KEY(post_id, liker_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE offres_voyage (offres_voyage_id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, destination VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, date_depart DATE NOT NULL, date_retour DATE NOT NULL, prix NUMERIC(10, 0) NOT NULL, places_disponibles INT NOT NULL, PRIMARY KEY(offres_voyage_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE posts (Post_id INT AUTO_INCREMENT NOT NULL, Owner_id INT NOT NULL, created_at DATE NOT NULL, updated_at DATE NOT NULL, text_content VARCHAR(255) NOT NULL, INDEX fk_owner_id (Owner_id), PRIMARY KEY(Post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reclamations (reclamation_id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(50) NOT NULL, description VARCHAR(255) NOT NULL, date_reclamation DATE NOT NULL, etat VARCHAR(20) DEFAULT 'en cours', INDEX fk_user_id (user_id), PRIMARY KEY(reclamation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reponses (reponse_id INT AUTO_INCREMENT NOT NULL, reclamation_id INT NOT NULL, contenu VARCHAR(255) NOT NULL, date_reponse DATE NOT NULL, INDEX fk_reclamation_id (reclamation_id), PRIMARY KEY(reponse_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reservation_hotel (reservation_hotel_id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, chambre_id INT NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, status_enu VARCHAR(255) NOT NULL, prix_totale INT NOT NULL, INDEX fk_client_id (client_id), INDEX fk_chambre_id (chambre_id), PRIMARY KEY(reservation_hotel_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reservation_offres_voyage (reservation_id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, offre_id INT NOT NULL, date_reserved DATE NOT NULL, status INT NOT NULL, nbr_place INT NOT NULL, prix DOUBLE PRECISION NOT NULL, INDEX fk_client (client_id), INDEX fk_offre_id (offre_id), PRIMARY KEY(reservation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reservation_packs (reservation_packs_id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, pack_id INT NOT NULL, date_reservation DATE NOT NULL, statut VARCHAR(255) NOT NULL, prix_total NUMERIC(10, 0) NOT NULL, INDEX fk_packs_client_id (client_id), INDEX fk_pack_id (pack_id), PRIMARY KEY(reservation_packs_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE users (user_id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, email VARCHAR(50) NOT NULL, password VARCHAR(255) NOT NULL, phone_num INT NOT NULL, address VARCHAR(150) NOT NULL, role INT DEFAULT 0, photo LONGBLOB DEFAULT NULL, compte INT DEFAULT 0, PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE chambres
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE comments
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE excursions
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE flagged_content
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE guides
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE hotels
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE likes
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE offres_voyage
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE posts
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reclamations
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reponses
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reservation_hotel
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reservation_offres_voyage
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reservation_packs
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE users
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
