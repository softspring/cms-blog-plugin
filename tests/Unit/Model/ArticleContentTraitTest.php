<?php

declare(strict_types=1);

namespace Softspring\CmsBlogPlugin\Tests\Unit\Model;

use DateTime;
use PHPUnit\Framework\TestCase;
use Softspring\CmsBlogPlugin\Model\ArticleContentTrait;

class ArticleContentTraitTest extends TestCase
{
    public function testItStoresPublishedAtAsTimestamp(): void
    {
        $article = new ArticleContentTraitFixture();
        $publishedAt = new DateTime('2026-03-14 10:15:00 UTC');

        $article->setPublishedAt($publishedAt);

        self::assertEquals($publishedAt, $article->getPublishedAt());
    }

    public function testItClearsPublishedAt(): void
    {
        $article = new ArticleContentTraitFixture();

        $article->setPublishedAt(new DateTime('2026-03-14 10:15:00 UTC'));
        $article->setPublishedAt();

        self::assertNull($article->getPublishedAt());
    }

    public function testItNormalizesTags(): void
    {
        $article = new ArticleContentTraitFixture();

        $article->setTags([' News ', 'news', 'CMS   Blog', '', false, ['invalid'], 'Cms Blog']);

        self::assertSame(['News', 'CMS Blog'], $article->getTags());
    }

    public function testItReturnsEmptyTagsWhenStorageIsNull(): void
    {
        $article = new ArticleContentTraitFixture();
        $article->forceTags(null);

        self::assertSame([], $article->getTags());
    }
}

class ArticleContentTraitFixture
{
    use ArticleContentTrait;

    public function forceTags(?array $tags): void
    {
        $this->tags = $tags;
    }
}
