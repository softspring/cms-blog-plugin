<?php

declare(strict_types=1);

namespace Softspring\CmsBlogPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\Migrations\AbstractMigration;

final class Version20260525090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add tags field to blog articles';
    }

    public function up(Schema $schema): void
    {
        if ($this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            $this->addSql('ALTER TABLE cms_content_blog_article ADD tags JSON DEFAULT NULL');
            $this->addSql("UPDATE cms_content_blog_article SET tags = '[]' WHERE tags IS NULL");

            return;
        }

        $this->addSql("ALTER TABLE cms_content_blog_article ADD tags LONGTEXT DEFAULT NULL COMMENT '(DC2Type:json)'");
        $this->addSql("UPDATE cms_content_blog_article SET tags = '[]' WHERE tags IS NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cms_content_blog_article DROP tags');
    }
}
