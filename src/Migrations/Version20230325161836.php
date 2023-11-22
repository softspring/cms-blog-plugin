<?php

declare(strict_types=1);

namespace Softspring\CmsBlogPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230325161836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add blog article published date field';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cms_content_blog_article ADD published_at INT UNSIGNED DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cms_content_blog_article DROP published_at');
    }
}
