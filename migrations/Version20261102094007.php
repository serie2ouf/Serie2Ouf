<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261102094007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id INT IDENTITY NOT NULL, date DATETIME2(6) NOT NULL, un_utilisateur_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_6EEAA67DB0141749 ON commande (un_utilisateur_id)');
        $this->addSql('CREATE TABLE ligne_de_commande (id INT IDENTITY NOT NULL, quantite INT NOT NULL, une_commande_id INT, un_produit_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_7982ACE611108EE1 ON ligne_de_commande (une_commande_id)');
        $this->addSql('CREATE INDEX IDX_7982ACE66E34096C ON ligne_de_commande (un_produit_id)');
        $this->addSql('CREATE TABLE [user] (id INT IDENTITY NOT NULL, email NVARCHAR(180) NOT NULL, roles VARCHAR(MAX) NOT NULL, password NVARCHAR(255) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON [user] (email) WHERE email IS NOT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DB0141749 FOREIGN KEY (un_utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE ligne_de_commande ADD CONSTRAINT FK_7982ACE611108EE1 FOREIGN KEY (une_commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE ligne_de_commande ADD CONSTRAINT FK_7982ACE66E34096C FOREIGN KEY (un_produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE categorie ADD lib_cat NVARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA db_accessadmin');
        $this->addSql('CREATE SCHEMA db_backupoperator');
        $this->addSql('CREATE SCHEMA db_datareader');
        $this->addSql('CREATE SCHEMA db_datawriter');
        $this->addSql('CREATE SCHEMA db_ddladmin');
        $this->addSql('CREATE SCHEMA db_denydatareader');
        $this->addSql('CREATE SCHEMA db_denydatawriter');
        $this->addSql('CREATE SCHEMA db_owner');
        $this->addSql('CREATE SCHEMA db_securityadmin');
        $this->addSql('ALTER TABLE commande DROP CONSTRAINT FK_6EEAA67DB0141749');
        $this->addSql('ALTER TABLE ligne_de_commande DROP CONSTRAINT FK_7982ACE611108EE1');
        $this->addSql('ALTER TABLE ligne_de_commande DROP CONSTRAINT FK_7982ACE66E34096C');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE ligne_de_commande');
        $this->addSql('DROP TABLE [user]');
        $this->addSql('ALTER TABLE categorie DROP COLUMN lib_cat');
    }
}
