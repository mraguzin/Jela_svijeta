<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210906052930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dish_ingredient DROP FOREIGN KEY FK_77196056148EB0CB');
        $this->addSql('ALTER TABLE dish_tag DROP FOREIGN KEY FK_64FF4A98148EB0CB');
        $this->addSql('ALTER TABLE dish_translation DROP FOREIGN KEY FK_6678768A2C2AC5D3');
        $this->addSql('CREATE TABLE meal (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9EF68E9C12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meal_tag (meal_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_78E3E97639666D6 (meal_id), INDEX IDX_78E3E97BAD26311 (tag_id), PRIMARY KEY(meal_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meal_ingredient (meal_id INT NOT NULL, ingredient_id INT NOT NULL, INDEX IDX_FCC3CEFA639666D6 (meal_id), INDEX IDX_FCC3CEFA933FE08C (ingredient_id), PRIMARY KEY(meal_id, ingredient_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meal_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, description VARCHAR(2047) NOT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_B99343E72C2AC5D3 (translatable_id), UNIQUE INDEX meal_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE meal ADD CONSTRAINT FK_9EF68E9C12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE meal_tag ADD CONSTRAINT FK_78E3E97639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meal_tag ADD CONSTRAINT FK_78E3E97BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meal_ingredient ADD CONSTRAINT FK_FCC3CEFA639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meal_ingredient ADD CONSTRAINT FK_FCC3CEFA933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meal_translation ADD CONSTRAINT FK_B99343E72C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES meal (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE dish');
        $this->addSql('DROP TABLE dish_ingredient');
        $this->addSql('DROP TABLE dish_tag');
        $this->addSql('DROP TABLE dish_translation');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal_tag DROP FOREIGN KEY FK_78E3E97639666D6');
        $this->addSql('ALTER TABLE meal_ingredient DROP FOREIGN KEY FK_FCC3CEFA639666D6');
        $this->addSql('ALTER TABLE meal_translation DROP FOREIGN KEY FK_B99343E72C2AC5D3');
        $this->addSql('CREATE TABLE dish (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_957D8CB812469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE dish_ingredient (dish_id INT NOT NULL, ingredient_id INT NOT NULL, INDEX IDX_77196056148EB0CB (dish_id), INDEX IDX_77196056933FE08C (ingredient_id), PRIMARY KEY(dish_id, ingredient_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE dish_tag (dish_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_64FF4A98148EB0CB (dish_id), INDEX IDX_64FF4A98BAD26311 (tag_id), PRIMARY KEY(dish_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE dish_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, description VARCHAR(2047) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, locale VARCHAR(5) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX dish_translation_unique_translation (translatable_id, locale), INDEX IDX_6678768A2C2AC5D3 (translatable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE dish ADD CONSTRAINT FK_957D8CB812469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dish_ingredient ADD CONSTRAINT FK_77196056148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dish_ingredient ADD CONSTRAINT FK_77196056933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dish_tag ADD CONSTRAINT FK_64FF4A98148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dish_tag ADD CONSTRAINT FK_64FF4A98BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dish_translation ADD CONSTRAINT FK_6678768A2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES dish (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('DROP TABLE meal');
        $this->addSql('DROP TABLE meal_tag');
        $this->addSql('DROP TABLE meal_ingredient');
        $this->addSql('DROP TABLE meal_translation');
    }
}
