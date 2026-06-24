<?php

declare(strict_types=1);

namespace Softspring\CmsBlogPlugin\Tests\Unit\Data\EntityTransformer;

use DateTime;
use PHPUnit\Framework\TestCase;
use Softspring\CmsBlogPlugin\Data\EntityTransformer\ArticleEntityTransformer;
use Softspring\CmsBlogPlugin\Entity\ArticleContent;
use Softspring\CmsBundle\Manager\ContentManagerInterface;
use Softspring\CmsBundle\Manager\RouteManagerInterface;
use Softspring\CmsBundle\Manager\SiteManagerInterface;
use Softspring\CmsDataPlugin\Data\DataTransformer;
use Softspring\CmsDataPlugin\Data\Exception\InvalidElementException;
use Softspring\MediaBundle\EntityManager\MediaManagerInterface;
use stdClass;

class ArticleEntityTransformerTest extends TestCase
{
    public function testItOnlySupportsArticles(): void
    {
        $transformer = $this->createTransformer();

        self::assertTrue($transformer->supports('article'));
        self::assertFalse($transformer->supports('page'));
    }

    public function testItExportsArticlePublicationDate(): void
    {
        $article = new ArticleContent();
        $article->setName('Article');
        $article->setDefaultLocale('en');
        $article->setLocales(['en', 'es']);
        $article->setExtraData([]);
        $article->setIndexing([]);
        $article->setPublishedAt(new DateTime('2026-06-24 11:45:30 UTC'));

        $export = $this->createTransformer()->export($article, $files, null, 'article');

        self::assertSame('Article', $export['article']['name']);
        self::assertSame('11:45:30 24-06-2026', $export['article']['published_at']);
    }

    public function testItRejectsNonArticleElements(): void
    {
        $this->expectException(InvalidElementException::class);

        $this->createTransformer()->export(new stdClass(), $files, null, 'article');
    }

    private function createTransformer(): ArticleEntityTransformer
    {
        return new ArticleEntityTransformer(
            $this->createStub(ContentManagerInterface::class),
            $this->createStub(RouteManagerInterface::class),
            $this->createStub(MediaManagerInterface::class),
            $this->createStub(SiteManagerInterface::class),
            $this->createStub(DataTransformer::class),
        );
    }
}
