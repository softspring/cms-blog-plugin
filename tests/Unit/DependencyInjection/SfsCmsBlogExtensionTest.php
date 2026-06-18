<?php

declare(strict_types=1);

namespace Softspring\CmsBlogPlugin\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Softspring\CmsBlogPlugin\DependencyInjection\SfsCmsBlogExtension;
use Softspring\CmsBlogPlugin\Entity\ArticleContent;
use Softspring\CmsBlogPlugin\Model\ArticleContentInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SfsCmsBlogExtensionTest extends TestCase
{
    public function testItLoadsParametersAndServices(): void
    {
        $container = new ContainerBuilder();
        $extension = new SfsCmsBlogExtension();

        $extension->load([
            [
                'author' => [
                    'class' => AuthorFixture::class,
                ],
            ],
        ], $container);

        self::assertSame(ArticleContent::class, $container->getParameter('sfs_cms_blog.article.class'));
        self::assertSame(AuthorFixture::class, $container->getParameter('sfs_cms_blog.author.class'));
        self::assertSame([], $container->getParameter('sfs_cms_blog.convert_superclass_list'));
        self::assertTrue($container->hasDefinition('Softspring\CmsBlogPlugin\Manager\ArticleTagManager'));
    }

    public function testItMarksDefaultArticleAsSuperclassWhenCustomArticleClassIsConfigured(): void
    {
        $container = new ContainerBuilder();
        $extension = new SfsCmsBlogExtension();

        $extension->load([
            [
                'article' => [
                    'class' => CustomArticle::class,
                ],
                'author' => [
                    'class' => AuthorFixture::class,
                ],
            ],
        ], $container);

        self::assertSame([ArticleContent::class], $container->getParameter('sfs_cms_blog.convert_superclass_list'));
    }

    public function testItPrependsDoctrineCmsAndMigrationConfiguration(): void
    {
        $container = new ContainerBuilder();

        (new SfsCmsBlogExtension())->prepend($container);

        $doctrineConfig = $container->getExtensionConfig('doctrine')[0];
        self::assertSame(ArticleContent::class, $doctrineConfig['orm']['resolve_target_entities'][ArticleContentInterface::class]);
        self::assertArrayHasKey('JSON_EXTRACT', $doctrineConfig['orm']['dql']['string_functions']);
        self::assertSame(['vendor/softspring/cms-blog-plugin/cms'], $container->getExtensionConfig('sfs_cms')[0]['collections']);
        self::assertSame(
            ['Softspring\CmsBlogPlugin\Migrations' => '@SfsCmsBlogPlugin/src/Migrations'],
            $container->getExtensionConfig('doctrine_migrations')[0]['migrations_paths']
        );
    }
}

class CustomArticle
{
}
