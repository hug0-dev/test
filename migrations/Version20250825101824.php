<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250825101824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE affectation (id INT AUTO_INCREMENT NOT NULL, ouvrier_id INT DEFAULT NULL, equipe_id INT NOT NULL, chantier_id INT NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, nom VARCHAR(50) DEFAULT NULL, INDEX IDX_F4DD61D34E853A9E (ouvrier_id), INDEX IDX_F4DD61D36D861B89 (equipe_id), INDEX IDX_F4DD61D3D0C0049D (chantier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chantier (id INT AUTO_INCREMENT NOT NULL, chef_chantier_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, chantier_prerequis LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', effectif_requis INT NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, image VARCHAR(255) DEFAULT NULL, INDEX IDX_636F27F622456F8F (chef_chantier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competence (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, actif TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipe (id INT AUTO_INCREMENT NOT NULL, chef_equipe_id INT DEFAULT NULL, nom_equipe VARCHAR(255) NOT NULL, competance_equipe LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', nombre INT NOT NULL, INDEX IDX_2449BA15BEF74F87 (chef_equipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, equipe_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, nom_ouvrier VARCHAR(100) DEFAULT NULL, role_ouvrier VARCHAR(50) DEFAULT NULL, INDEX IDX_8D93D6496D861B89 (equipe_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_competence (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, competence_id INT NOT NULL, attribuee_par_id INT DEFAULT NULL, niveau VARCHAR(20) NOT NULL, date_attribution DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_33B3AE93A76ED395 (user_id), INDEX IDX_33B3AE9315761DAB (competence_id), INDEX IDX_33B3AE93DFDEFEC2 (attribuee_par_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D34E853A9E FOREIGN KEY (ouvrier_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D36D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D3D0C0049D FOREIGN KEY (chantier_id) REFERENCES chantier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chantier ADD CONSTRAINT FK_636F27F622456F8F FOREIGN KEY (chef_chantier_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE equipe ADD CONSTRAINT FK_2449BA15BEF74F87 FOREIGN KEY (chef_equipe_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user_competence ADD CONSTRAINT FK_33B3AE93A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_competence ADD CONSTRAINT FK_33B3AE9315761DAB FOREIGN KEY (competence_id) REFERENCES competence (id)');
        $this->addSql('ALTER TABLE user_competence ADD CONSTRAINT FK_33B3AE93DFDEFEC2 FOREIGN KEY (attribuee_par_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE affectation DROP FOREIGN KEY FK_F4DD61D34E853A9E');
        $this->addSql('ALTER TABLE affectation DROP FOREIGN KEY FK_F4DD61D36D861B89');
        $this->addSql('ALTER TABLE affectation DROP FOREIGN KEY FK_F4DD61D3D0C0049D');
        $this->addSql('ALTER TABLE chantier DROP FOREIGN KEY FK_636F27F622456F8F');
        $this->addSql('ALTER TABLE equipe DROP FOREIGN KEY FK_2449BA15BEF74F87');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496D861B89');
        $this->addSql('ALTER TABLE user_competence DROP FOREIGN KEY FK_33B3AE93A76ED395');
        $this->addSql('ALTER TABLE user_competence DROP FOREIGN KEY FK_33B3AE9315761DAB');
        $this->addSql('ALTER TABLE user_competence DROP FOREIGN KEY FK_33B3AE93DFDEFEC2');
        $this->addSql('DROP TABLE affectation');
        $this->addSql('DROP TABLE chantier');
        $this->addSql('DROP TABLE competence');
        $this->addSql('DROP TABLE equipe');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_competence');
    }
}
