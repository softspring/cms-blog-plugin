<?php

declare(strict_types=1);

namespace Softspring\CmsBlogPlugin\Tests\Unit\Entity;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Softspring\CmsBlogPlugin\Entity\ArticleContent;
use Softspring\CmsBundle\Model\ContentVersionInterface;
use Softspring\CmsBundle\Model\VersionInterface;

class ArticleContentTest extends TestCase
{
    public function testItClearsPublishedVersion(): void
    {
        $article = new ArticleContent();

        $article->setPublishedVersion(null);

        self::assertNull($article->getPublishedVersion());
    }

    public function testItRejectsNonContentVersions(): void
    {
        $article = new ArticleContent();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Published version must be an instance of ContentVersionInterface.');

        $article->setPublishedVersion($this->createStub(VersionInterface::class));
    }

    public function testItSetsPublishedAtWhenPublishingAContentVersion(): void
    {
        $article = new ArticleContent();
        $version = $this->createStub(ContentVersionInterface::class);

        $article->setPublishedVersion($version);

        self::assertSame($version, $article->getPublishedVersion());
        self::assertNotNull($article->getPublishedAt());
    }
}
