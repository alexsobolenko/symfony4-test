<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210926215918 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Add tables authors and books';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `authors` (`id` CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', `name` VARCHAR(255) NOT NULL, PRIMARY KEY(`id`)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `books` (`id` CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', `name` VARCHAR(255) NOT NULL, `price` DOUBLE PRECISION NOT NULL, `author_id` CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_4A1B2A92CDFA7D68 (`author_id`), PRIMARY KEY(`id`)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `books` ADD CONSTRAINT FK_4A1B2A92CDFA7D68 FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `books` DROP FOREIGN KEY FK_4A1B2A92CDFA7D68');
        $this->addSql('DROP TABLE `authors`');
        $this->addSql('DROP TABLE `books`');
    }
}
