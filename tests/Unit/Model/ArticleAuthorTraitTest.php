<?php

declare(strict_types=1);

namespace Softspring\CmsBlogPlugin\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use Softspring\CmsBlogPlugin\Model\ArticleAuthorTrait;
use Softspring\CmsBlogPlugin\Model\AuthorInterface;

class ArticleAuthorTraitTest extends TestCase
{
    public function testItStoresAuthor(): void
    {
        $article = new ArticleAuthorTraitFixture();
        $author = new AuthorFixture();

        $article->setAuthor($author);

        self::assertSame($author, $article->getAuthor());
    }

    public function testItClearsAuthor(): void
    {
        $article = new ArticleAuthorTraitFixture();

        $article->setAuthor(new AuthorFixture());
        $article->setAuthor(null);

        self::assertNull($article->getAuthor());
    }
}

class ArticleAuthorTraitFixture
{
    use ArticleAuthorTrait;
}

class AuthorFixture implements AuthorInterface
{
    public function getDisplayName(): string
    {
        return 'Author';
    }

    public function getAvatarUrl(): string
    {
        return '/avatar.jpg';
    }
}
