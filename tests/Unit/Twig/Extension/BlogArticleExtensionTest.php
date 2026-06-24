<?php

declare(strict_types=1);

namespace Softspring\CmsBlogPlugin\Tests\Unit\Twig\Extension;

use Closure;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Softspring\CmsBlogPlugin\Entity\ArticleContent;
use Softspring\CmsBlogPlugin\Twig\Extension\BlogArticleExtension;
use Softspring\CmsBundle\Manager\ContentManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\TwigFunction;

class BlogArticleExtensionTest extends TestCase
{
    public function testItRegistersRelatedArticlesFunction(): void
    {
        $extension = new BlogArticleExtension($this->createStub(ContentManagerInterface::class), new RequestStack());

        self::assertCount(1, $extension->getFunctions());
        self::assertInstanceOf(TwigFunction::class, $extension->getFunctions()[0]);
        self::assertSame('sfs_cms_related_blog_articles', $extension->getFunctions()[0]->getName());
    }

    public function testItReturnsEmptyResultForBlankTag(): void
    {
        $contentManager = $this->createMock(ContentManagerInterface::class);
        $contentManager->expects(self::never())->method('getRepository');

        $extension = new BlogArticleExtension($contentManager, new RequestStack());

        self::assertSame([], $extension->getRelatedBlogArticles('   '));
    }

    public function testItBuildsRelatedArticlesQuery(): void
    {
        $results = [new ArticleContent()];
        $query = $this->createStub(Query::class);
        $query->method('getResult')->willReturn($results);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects(self::exactly(5))->method('andWhere')->willReturnSelf();
        $queryBuilder->expects(self::exactly(4))->method('setParameter')->willReturnSelf();
        $queryBuilder->expects(self::once())->method('addOrderBy')->with('a.name', 'DESC')->willReturnSelf();
        $queryBuilder->expects(self::once())->method('setMaxResults')->with(1)->willReturnSelf();
        $queryBuilder->expects(self::once())->method('getQuery')->willReturn($query);

        $repository = $this->createStub(EntityRepository::class);
        $repository->method('createQueryBuilder')->with('a')->willReturn($queryBuilder);

        $contentManager = $this->createStub(ContentManagerInterface::class);
        $contentManager->method('getRepository')->with('article')->willReturn($repository);

        $requestStack = new RequestStack();
        $request = Request::create('/blog');
        $request->setLocale('es');
        Closure::fromCallable([$requestStack, 'push'])($request);

        $currentContent = new ArticleContentWithId();
        $currentContent->setId('current-article');

        $extension = new BlogArticleExtension($contentManager, $requestStack);

        self::assertSame($results, $extension->getRelatedBlogArticles(' CMS ', $currentContent, 0, 'name_desc'));
    }
}

class ArticleContentWithId extends ArticleContent
{
    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
