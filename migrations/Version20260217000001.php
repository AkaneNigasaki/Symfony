<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260217000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Teacher and Subject management system';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users (
            id INT AUTO_INCREMENT NOT NULL,
            email VARCHAR(180) NOT NULL,
            roles JSON NOT NULL,
            password VARCHAR(255) NOT NULL,
            first_name VARCHAR(100) DEFAULT NULL,
            last_name VARCHAR(100) DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE teachers (
            id INT AUTO_INCREMENT NOT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            email VARCHAR(191) NOT NULL,
            phone_number VARCHAR(20) DEFAULT NULL,
            specialization VARCHAR(255) DEFAULT NULL,
            hire_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\',
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            UNIQUE INDEX UNIQ_ED071FF6E7927C74 (email),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        $this->addSql('CREATE TABLE subjects (
            id INT AUTO_INCREMENT NOT NULL,
            teacher_id INT DEFAULT NULL,
            code VARCHAR(50) NOT NULL,
            name VARCHAR(255) NOT NULL,
            description LONGTEXT DEFAULT NULL,
            coefficient INT NOT NULL,
            hours_per_week INT NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            UNIQUE INDEX UNIQ_AB25991777153098 (code),
            INDEX IDX_AB2599141807E1D (teacher_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        $this->addSql('ALTER TABLE subjects ADD CONSTRAINT FK_AB2599141807E1D FOREIGN KEY (teacher_id) REFERENCES teachers (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE subjects DROP FOREIGN KEY FK_AB2599141807E1D');
        $this->addSql('DROP TABLE subjects');
        $this->addSql('DROP TABLE teachers');
        $this->addSql('DROP TABLE users');
    }
}
