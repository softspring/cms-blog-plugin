<?php

declare(strict_types=1);

namespace Softspring\CmsBlogPlugin\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Softspring\CmsBlogPlugin\DependencyInjection\Configuration;
use Softspring\CmsBlogPlugin\Entity\ArticleContent;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public function testItRequiresAuthorClass(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        (new Processor())->processConfiguration(new Configuration(), []);
    }

    public function testItProvidesDefaultArticleClass(): void
    {
        $config = (new Processor())->processConfiguration(new Configuration(), [
            [
                'author' => [
                    'class' => AuthorFixture::class,
                ],
            ],
        ]);

        self::assertSame(ArticleContent::class, $config['article']['class']);
        self::assertSame(AuthorFixture::class, $config['author']['class']);
    }
}

class AuthorFixture
{
}
