<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241104185537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add project table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) DEFAULT NULL, subtitle VARCHAR(255) DEFAULT NULL, description LONGTEXT NOT NULL, is_personal TINYINT(1) DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_2FB3D0EE5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projects_skills (project_id INT NOT NULL, skill_id INT NOT NULL, INDEX IDX_14C58A8F166D1F9C (project_id), INDEX IDX_14C58A8F5585C142 (skill_id), PRIMARY KEY(project_id, skill_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE projects_skills ADD CONSTRAINT FK_14C58A8F166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE projects_skills ADD CONSTRAINT FK_14C58A8F5585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE skill CHANGE is_hard_skill is_hard_skill TINYINT(1) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5E3DE4775E237E06 ON skill (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projects_skills DROP FOREIGN KEY FK_14C58A8F166D1F9C');
        $this->addSql('ALTER TABLE projects_skills DROP FOREIGN KEY FK_14C58A8F5585C142');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE projects_skills');
        $this->addSql('DROP INDEX UNIQ_5E3DE4775E237E06 ON skill');
        $this->addSql('ALTER TABLE skill CHANGE is_hard_skill is_hard_skill TINYINT(1) NOT NULL');
    }
}
