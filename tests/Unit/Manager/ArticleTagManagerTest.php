<?php

declare(strict_types=1);

namespace Softspring\CmsBlogPlugin\Tests\Unit\Manager;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Softspring\CmsBlogPlugin\Manager\ArticleTagManager;
use Softspring\CmsBundle\Manager\ContentManagerInterface;

class ArticleTagManagerTest extends TestCase
{
    public function testItReturnsNormalizedExistingTags(): void
    {
        $manager = new ArticleTagManager($this->createContentManager([
            ['tags' => '[" Symfony ","cms", "CMS", "", 123]'],
            ['tags' => [' Blog ', 'symfony', ['invalid'], 'News']],
            ['tags' => 'invalid-json'],
            ['tags' => null],
        ]));

        self::assertSame(['123', 'Blog', 'cms', 'News', 'Symfony'], $manager->getExistingTags());
    }

    public function testItCanonicalizesTagsUsingExistingCasing(): void
    {
        $manager = new ArticleTagManager($this->createContentManager([
            ['tags' => ['Symfony', 'CMS']],
        ]));

        self::assertSame(
            ['Symfony', 'CMS', 'New tag', 'Symfony'],
            $manager->canonicalizeTags([' symfony ', 'cms', 'New   tag', ['invalid'], '', 'SYMFONY'])
        );
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    private function createContentManager(array $rows): ContentManagerInterface
    {
        $query = $this->createStub(Query::class);
        $query->method('getScalarResult')->willReturn($rows);

        $queryBuilder = $this->createStub(QueryBuilder::class);
        $queryBuilder->method('select')->willReturnSelf();
        $queryBuilder->method('andWhere')->willReturnSelf();
        $queryBuilder->method('getQuery')->willReturn($query);

        $repository = $this->createStub(EntityRepository::class);
        $repository->method('createQueryBuilder')->willReturn($queryBuilder);

        $contentManager = $this->createStub(ContentManagerInterface::class);
        $contentManager->method('getRepository')->willReturn($repository);

        return $contentManager;
    }
}
