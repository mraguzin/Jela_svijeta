<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210904162402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_3F207042C2AC5D3 (translatable_id), UNIQUE INDEX category_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dish (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_957D8CB812469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dish_tag (dish_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_64FF4A98148EB0CB (dish_id), INDEX IDX_64FF4A98BAD26311 (tag_id), PRIMARY KEY(dish_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dish_ingredient (dish_id INT NOT NULL, ingredient_id INT NOT NULL, INDEX IDX_77196056148EB0CB (dish_id), INDEX IDX_77196056933FE08C (ingredient_id), PRIMARY KEY(dish_id, ingredient_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ingredient (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ingredient_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_C1A8BF62C2AC5D3 (translatable_id), UNIQUE INDEX ingredient_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE language (id VARCHAR(3) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_A8A03F8F2C2AC5D3 (translatable_id), UNIQUE INDEX tag_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_translation ADD CONSTRAINT FK_3F207042C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dish ADD CONSTRAINT FK_957D8CB812469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE dish_tag ADD CONSTRAINT FK_64FF4A98148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dish_tag ADD CONSTRAINT FK_64FF4A98BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dish_ingredient ADD CONSTRAINT FK_77196056148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dish_ingredient ADD CONSTRAINT FK_77196056933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ingredient_translation ADD CONSTRAINT FK_C1A8BF62C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES ingredient (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_translation ADD CONSTRAINT FK_A8A03F8F2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_translation DROP FOREIGN KEY FK_3F207042C2AC5D3');
        $this->addSql('ALTER TABLE dish DROP FOREIGN KEY FK_957D8CB812469DE2');
        $this->addSql('ALTER TABLE dish_tag DROP FOREIGN KEY FK_64FF4A98148EB0CB');
        $this->addSql('ALTER TABLE dish_ingredient DROP FOREIGN KEY FK_77196056148EB0CB');
        $this->addSql('ALTER TABLE dish_ingredient DROP FOREIGN KEY FK_77196056933FE08C');
        $this->addSql('ALTER TABLE ingredient_translation DROP FOREIGN KEY FK_C1A8BF62C2AC5D3');
        $this->addSql('ALTER TABLE dish_tag DROP FOREIGN KEY FK_64FF4A98BAD26311');
        $this->addSql('ALTER TABLE tag_translation DROP FOREIGN KEY FK_A8A03F8F2C2AC5D3');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_translation');
        $this->addSql('DROP TABLE dish');
        $this->addSql('DROP TABLE dish_tag');
        $this->addSql('DROP TABLE dish_ingredient');
        $this->addSql('DROP TABLE ingredient');
        $this->addSql('DROP TABLE ingredient_translation');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_translation');
    }
}
