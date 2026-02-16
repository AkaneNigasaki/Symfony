<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260215221005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE assignments (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, due_date DATETIME NOT NULL, max_points INT NOT NULL, created_at DATETIME NOT NULL, course_id INT NOT NULL, INDEX IDX_308A50DD591CC992 (course_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE classrooms (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, capacity INT NOT NULL, type VARCHAR(50) NOT NULL, location VARCHAR(255) DEFAULT NULL, facilities JSON DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE courses (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, credits INT NOT NULL, max_students INT NOT NULL, semester VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, teacher_id INT NOT NULL, classroom_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_A9A55A4C77153098 (code), INDEX IDX_A9A55A4C41807E1D (teacher_id), INDEX IDX_A9A55A4C6278D5A8 (classroom_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE enrollments (id INT AUTO_INCREMENT NOT NULL, enrollment_date DATETIME NOT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, student_id INT NOT NULL, course_id INT NOT NULL, INDEX IDX_CCD8C132CB944F1A (student_id), INDEX IDX_CCD8C132591CC992 (course_id), UNIQUE INDEX unique_student_course (student_id, course_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE grades (id INT AUTO_INCREMENT NOT NULL, points NUMERIC(10, 2) NOT NULL, max_points INT NOT NULL, percentage NUMERIC(5, 2) NOT NULL, letter_grade VARCHAR(2) NOT NULL, graded_at DATETIME NOT NULL, comments LONGTEXT DEFAULT NULL, student_id INT NOT NULL, assignment_id INT DEFAULT NULL, enrollment_id INT DEFAULT NULL, graded_by_id INT NOT NULL, INDEX IDX_3AE36110CB944F1A (student_id), INDEX IDX_3AE36110D19302F8 (assignment_id), UNIQUE INDEX UNIQ_3AE361108F7DB25B (enrollment_id), INDEX IDX_3AE36110C814BC2E (graded_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE schedules (id INT AUTO_INCREMENT NOT NULL, day_of_week SMALLINT NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, effective_from DATE NOT NULL, effective_to DATE DEFAULT NULL, created_at DATETIME NOT NULL, course_id INT NOT NULL, classroom_id INT NOT NULL, INDEX IDX_313BDC8E591CC992 (course_id), INDEX IDX_313BDC8E6278D5A8 (classroom_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE assignments ADD CONSTRAINT FK_308A50DD591CC992 FOREIGN KEY (course_id) REFERENCES courses (id)');
        $this->addSql('ALTER TABLE courses ADD CONSTRAINT FK_A9A55A4C41807E1D FOREIGN KEY (teacher_id) REFERENCES teachers (id)');
        $this->addSql('ALTER TABLE courses ADD CONSTRAINT FK_A9A55A4C6278D5A8 FOREIGN KEY (classroom_id) REFERENCES classrooms (id)');
        $this->addSql('ALTER TABLE enrollments ADD CONSTRAINT FK_CCD8C132CB944F1A FOREIGN KEY (student_id) REFERENCES students (id)');
        $this->addSql('ALTER TABLE enrollments ADD CONSTRAINT FK_CCD8C132591CC992 FOREIGN KEY (course_id) REFERENCES courses (id)');
        $this->addSql('ALTER TABLE grades ADD CONSTRAINT FK_3AE36110CB944F1A FOREIGN KEY (student_id) REFERENCES students (id)');
        $this->addSql('ALTER TABLE grades ADD CONSTRAINT FK_3AE36110D19302F8 FOREIGN KEY (assignment_id) REFERENCES assignments (id)');
        $this->addSql('ALTER TABLE grades ADD CONSTRAINT FK_3AE361108F7DB25B FOREIGN KEY (enrollment_id) REFERENCES enrollments (id)');
        $this->addSql('ALTER TABLE grades ADD CONSTRAINT FK_3AE36110C814BC2E FOREIGN KEY (graded_by_id) REFERENCES teachers (id)');
        $this->addSql('ALTER TABLE schedules ADD CONSTRAINT FK_313BDC8E591CC992 FOREIGN KEY (course_id) REFERENCES courses (id)');
        $this->addSql('ALTER TABLE schedules ADD CONSTRAINT FK_313BDC8E6278D5A8 FOREIGN KEY (classroom_id) REFERENCES classrooms (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assignments DROP FOREIGN KEY FK_308A50DD591CC992');
        $this->addSql('ALTER TABLE courses DROP FOREIGN KEY FK_A9A55A4C41807E1D');
        $this->addSql('ALTER TABLE courses DROP FOREIGN KEY FK_A9A55A4C6278D5A8');
        $this->addSql('ALTER TABLE enrollments DROP FOREIGN KEY FK_CCD8C132CB944F1A');
        $this->addSql('ALTER TABLE enrollments DROP FOREIGN KEY FK_CCD8C132591CC992');
        $this->addSql('ALTER TABLE grades DROP FOREIGN KEY FK_3AE36110CB944F1A');
        $this->addSql('ALTER TABLE grades DROP FOREIGN KEY FK_3AE36110D19302F8');
        $this->addSql('ALTER TABLE grades DROP FOREIGN KEY FK_3AE361108F7DB25B');
        $this->addSql('ALTER TABLE grades DROP FOREIGN KEY FK_3AE36110C814BC2E');
        $this->addSql('ALTER TABLE schedules DROP FOREIGN KEY FK_313BDC8E591CC992');
        $this->addSql('ALTER TABLE schedules DROP FOREIGN KEY FK_313BDC8E6278D5A8');
        $this->addSql('DROP TABLE assignments');
        $this->addSql('DROP TABLE classrooms');
        $this->addSql('DROP TABLE courses');
        $this->addSql('DROP TABLE enrollments');
        $this->addSql('DROP TABLE grades');
        $this->addSql('DROP TABLE schedules');
    }
}
