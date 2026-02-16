<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260215223043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE schools (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, email VARCHAR(191) DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE assignments ADD CONSTRAINT FK_308A50DD591CC992 FOREIGN KEY (course_id) REFERENCES courses (id)');
        $this->addSql('ALTER TABLE classrooms ADD school_id INT NOT NULL');
        $this->addSql('ALTER TABLE classrooms ADD CONSTRAINT FK_95F95DC2C32A47EE FOREIGN KEY (school_id) REFERENCES schools (id)');
        $this->addSql('CREATE INDEX IDX_95F95DC2C32A47EE ON classrooms (school_id)');
        $this->addSql('ALTER TABLE courses ADD school_id INT NOT NULL');
        $this->addSql('ALTER TABLE courses ADD CONSTRAINT FK_A9A55A4C41807E1D FOREIGN KEY (teacher_id) REFERENCES teachers (id)');
        $this->addSql('ALTER TABLE courses ADD CONSTRAINT FK_A9A55A4C6278D5A8 FOREIGN KEY (classroom_id) REFERENCES classrooms (id)');
        $this->addSql('ALTER TABLE courses ADD CONSTRAINT FK_A9A55A4CC32A47EE FOREIGN KEY (school_id) REFERENCES schools (id)');
        $this->addSql('CREATE INDEX IDX_A9A55A4CC32A47EE ON courses (school_id)');
        $this->addSql('ALTER TABLE enrollments ADD CONSTRAINT FK_CCD8C132CB944F1A FOREIGN KEY (student_id) REFERENCES students (id)');
        $this->addSql('ALTER TABLE enrollments ADD CONSTRAINT FK_CCD8C132591CC992 FOREIGN KEY (course_id) REFERENCES courses (id)');
        $this->addSql('ALTER TABLE grades ADD CONSTRAINT FK_3AE36110CB944F1A FOREIGN KEY (student_id) REFERENCES students (id)');
        $this->addSql('ALTER TABLE grades ADD CONSTRAINT FK_3AE36110D19302F8 FOREIGN KEY (assignment_id) REFERENCES assignments (id)');
        $this->addSql('ALTER TABLE grades ADD CONSTRAINT FK_3AE361108F7DB25B FOREIGN KEY (enrollment_id) REFERENCES enrollments (id)');
        $this->addSql('ALTER TABLE grades ADD CONSTRAINT FK_3AE36110C814BC2E FOREIGN KEY (graded_by_id) REFERENCES teachers (id)');
        $this->addSql('ALTER TABLE schedules ADD CONSTRAINT FK_313BDC8E591CC992 FOREIGN KEY (course_id) REFERENCES courses (id)');
        $this->addSql('ALTER TABLE schedules ADD CONSTRAINT FK_313BDC8E6278D5A8 FOREIGN KEY (classroom_id) REFERENCES classrooms (id)');
        $this->addSql('ALTER TABLE students ADD school_id INT NOT NULL');
        $this->addSql('ALTER TABLE students ADD CONSTRAINT FK_A4698DB2C32A47EE FOREIGN KEY (school_id) REFERENCES schools (id)');
        $this->addSql('CREATE INDEX IDX_A4698DB2C32A47EE ON students (school_id)');
        $this->addSql('ALTER TABLE teachers ADD school_id INT NOT NULL');
        $this->addSql('ALTER TABLE teachers ADD CONSTRAINT FK_ED071FF6C32A47EE FOREIGN KEY (school_id) REFERENCES schools (id)');
        $this->addSql('CREATE INDEX IDX_ED071FF6C32A47EE ON teachers (school_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE schools');
        $this->addSql('ALTER TABLE assignments DROP FOREIGN KEY FK_308A50DD591CC992');
        $this->addSql('ALTER TABLE classrooms DROP FOREIGN KEY FK_95F95DC2C32A47EE');
        $this->addSql('DROP INDEX IDX_95F95DC2C32A47EE ON classrooms');
        $this->addSql('ALTER TABLE classrooms DROP school_id');
        $this->addSql('ALTER TABLE courses DROP FOREIGN KEY FK_A9A55A4C41807E1D');
        $this->addSql('ALTER TABLE courses DROP FOREIGN KEY FK_A9A55A4C6278D5A8');
        $this->addSql('ALTER TABLE courses DROP FOREIGN KEY FK_A9A55A4CC32A47EE');
        $this->addSql('DROP INDEX IDX_A9A55A4CC32A47EE ON courses');
        $this->addSql('ALTER TABLE courses DROP school_id');
        $this->addSql('ALTER TABLE enrollments DROP FOREIGN KEY FK_CCD8C132CB944F1A');
        $this->addSql('ALTER TABLE enrollments DROP FOREIGN KEY FK_CCD8C132591CC992');
        $this->addSql('ALTER TABLE grades DROP FOREIGN KEY FK_3AE36110CB944F1A');
        $this->addSql('ALTER TABLE grades DROP FOREIGN KEY FK_3AE36110D19302F8');
        $this->addSql('ALTER TABLE grades DROP FOREIGN KEY FK_3AE361108F7DB25B');
        $this->addSql('ALTER TABLE grades DROP FOREIGN KEY FK_3AE36110C814BC2E');
        $this->addSql('ALTER TABLE schedules DROP FOREIGN KEY FK_313BDC8E591CC992');
        $this->addSql('ALTER TABLE schedules DROP FOREIGN KEY FK_313BDC8E6278D5A8');
        $this->addSql('ALTER TABLE students DROP FOREIGN KEY FK_A4698DB2C32A47EE');
        $this->addSql('DROP INDEX IDX_A4698DB2C32A47EE ON students');
        $this->addSql('ALTER TABLE students DROP school_id');
        $this->addSql('ALTER TABLE teachers DROP FOREIGN KEY FK_ED071FF6C32A47EE');
        $this->addSql('DROP INDEX IDX_ED071FF6C32A47EE ON teachers');
        $this->addSql('ALTER TABLE teachers DROP school_id');
    }
}
