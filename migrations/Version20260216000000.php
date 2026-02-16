<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260216000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add users table and school owner (user) relation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE schools ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE schools ADD CONSTRAINT FK_9D1728D17E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_9D1728D17E3C61F9 ON schools (owner_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE schools DROP FOREIGN KEY FK_9D1728D17E3C61F9');
        $this->addSql('DROP INDEX IDX_9D1728D17E3C61F9 ON schools');
        $this->addSql('ALTER TABLE schools DROP owner_id');
        $this->addSql('DROP TABLE users');
    }
}
