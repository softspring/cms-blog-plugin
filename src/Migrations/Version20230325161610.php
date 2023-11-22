<?php

declare(strict_types=1);

namespace Softspring\CmsBlogPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230325161610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create blog article structure';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE cms_content_blog_article (id CHAR(36) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cms_content_blog_article ADD CONSTRAINT FK_7BB3D8EFBF396750 FOREIGN KEY (id) REFERENCES cms_content (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cms_content_blog_article DROP FOREIGN KEY FK_7BB3D8EFBF396750');
        $this->addSql('DROP TABLE cms_content_blog_article');
    }
}
