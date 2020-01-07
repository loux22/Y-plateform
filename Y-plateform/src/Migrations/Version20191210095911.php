<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191210095911 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE admin (id INT AUTO_INCREMENT NOT NULL, user INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, description_c VARCHAR(255) NOT NULL, date_c DATETIME NOT NULL, member INT NOT NULL, game INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, date_g DATE NOT NULL, is_active TINYINT(1) NOT NULL, nb_download INT NOT NULL, img VARCHAR(100) NOT NULL, description_g VARCHAR(255) NOT NULL, url VARCHAR(100) NOT NULL, note INT NOT NULL, nb_note INT NOT NULL, member INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE member (id INT AUTO_INCREMENT NOT NULL, pseudo VARCHAR(50) NOT NULL, phone VARCHAR(10) NOT NULL, address VARCHAR(50) NOT NULL, postal VARCHAR(5) NOT NULL, city VARCHAR(30) NOT NULL, date_m DATE NOT NULL, is_active_m TINYINT(1) NOT NULL, ipadress VARCHAR(15) NOT NULL, level TINYINT(1) NOT NULL, user INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(30) NOT NULL, lastname VARCHAR(30) NOT NULL, mail VARCHAR(50) NOT NULL, password VARCHAR(50) NOT NULL, avatar VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE member');
        $this->addSql('DROP TABLE user');
    }
}
