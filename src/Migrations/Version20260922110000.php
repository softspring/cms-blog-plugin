<?php

declare(strict_types=1);

namespace Softspring\CmsBlogPlugin\Migrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260922110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Normalize blog article content identifiers';
    }

    public function up(Schema $schema): void
    {
        if ($this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            $this->addSql('ALTER TABLE cms_content_blog_article ALTER COLUMN id TYPE VARCHAR(36)');

            return;
        }

        $this->addSql('ALTER TABLE cms_content_blog_article DROP FOREIGN KEY FK_7BB3D8EFBF396750');
        $this->addSql('ALTER TABLE cms_content_blog_article CHANGE id id VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE cms_content_blog_article ADD CONSTRAINT FK_7BB3D8EFBF396750 FOREIGN KEY (id) REFERENCES cms_content (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        if ($this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            $this->addSql('ALTER TABLE cms_content_blog_article ALTER COLUMN id TYPE CHAR(36)');

            return;
        }

        $this->addSql('ALTER TABLE cms_content_blog_article DROP FOREIGN KEY FK_7BB3D8EFBF396750');
        $this->addSql('ALTER TABLE cms_content_blog_article CHANGE id id CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE cms_content_blog_article ADD CONSTRAINT FK_7BB3D8EFBF396750 FOREIGN KEY (id) REFERENCES cms_content (id) ON DELETE CASCADE');
    }
}
