<?php

declare(strict_types=1);

namespace Softspring\CmsBlogPlugin\Tests\Unit\Form\Type;

use PHPUnit\Framework\TestCase;
use Softspring\CmsBlogPlugin\Form\Type\BlogArticleTagType;
use Softspring\CmsBlogPlugin\Manager\ArticleTagManager;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogArticleTagTypeTest extends TestCase
{
    public function testItConfiguresChoicesFromExistingTags(): void
    {
        $tagManager = $this->createStub(ArticleTagManager::class);
        $tagManager->method('getExistingTags')->willReturn(['CMS', 'Symfony']);
        $type = new BlogArticleTagType($tagManager);
        $resolver = new OptionsResolver();

        $type->configureOptions($resolver);
        $options = $resolver->resolve();

        self::assertFalse($options['required']);
        self::assertNull($options['placeholder']);
        self::assertSame(['CMS' => 'CMS', 'Symfony' => 'Symfony'], $options['choices']);
        self::assertFalse($options['choice_translation_domain']);
    }

    public function testItUsesChoiceTypeParent(): void
    {
        $type = new BlogArticleTagType($this->createStub(ArticleTagManager::class));

        self::assertSame(ChoiceType::class, $type->getParent());
    }
}
