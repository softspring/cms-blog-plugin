<?php

declare(strict_types=1);

namespace Softspring\CmsBlogPlugin\Tests\Unit\Manager;

use DateTime;
use PHPUnit\Framework\TestCase;
use Softspring\CmsBlogPlugin\Entity\ArticleContent;
use Softspring\CmsBlogPlugin\Manager\ArticleEntityDuplicator;
use Softspring\CmsBlogPlugin\Model\AuthorInterface;
use Softspring\CmsBundle\Model\ContentInterface;

class ArticleEntityDuplicatorTest extends TestCase
{
    public function testItOnlySupportsArticleContents(): void
    {
        $duplicator = new ArticleEntityDuplicator();

        self::assertTrue($duplicator->supports(new ArticleContent()));
        self::assertFalse($duplicator->supports($this->createStub(ContentInterface::class)));
    }

    public function testItCopiesBlogFields(): void
    {
        $author = $this->createStub(AuthorInterface::class);
        $publishedAt = new DateTime('2026-06-24 10:30:00 UTC');
        $oldContent = new ArticleContent();
        $oldContent->setAuthor($author);
        $oldContent->setPublishedAt($publishedAt);
        $oldContent->setTags(['CMS', 'Blog']);
        $newContent = new ArticleContent();

        (new ArticleEntityDuplicator())->duplicateData($oldContent, $newContent);

        self::assertSame($author, $newContent->getAuthor());
        self::assertEquals($publishedAt, $newContent->getPublishedAt());
        self::assertSame(['CMS', 'Blog'], $newContent->getTags());
    }
}
