<?php

declare(strict_types=1);

namespace Softspring\CmsBlogPlugin\Tests\Unit\Form\Type;

use PHPUnit\Framework\TestCase;
use Softspring\CmsBlogPlugin\Form\Type\ArticleTagsType;
use Softspring\CmsBlogPlugin\Manager\ArticleTagManager;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleTagsTypeTest extends TestCase
{
    public function testItRegistersModelTransformer(): void
    {
        $tagManager = $this->createStub(ArticleTagManager::class);
        $tagManager->method('canonicalizeTags')->willReturnCallback(fn (array $tags): array => array_map('strtoupper', $tags));
        $type = new ArticleTagsType($tagManager);
        $builder = $this->createMock(FormBuilderInterface::class);

        $builder->expects(self::once())
            ->method('addModelTransformer')
            ->with(self::callback(function (CallbackTransformer $transformer): bool {
                self::assertSame('["cms","blog"]', $transformer->transform(['cms', 'blog']));
                self::assertSame([], $transformer->reverseTransform(null));
                self::assertSame([], $transformer->reverseTransform(''));
                self::assertSame([], $transformer->reverseTransform('0'));
                self::assertSame([], $transformer->reverseTransform('{invalid'));
                self::assertSame(['CMS', 'BLOG'], $transformer->reverseTransform('["cms","blog"]'));

                return true;
            }));

        $type->buildForm($builder, []);
    }

    public function testItConfiguresOptionsAndViewVariables(): void
    {
        $tagManager = $this->createStub(ArticleTagManager::class);
        $tagManager->method('getExistingTags')->willReturn(['CMS', 'Symfony']);
        $type = new ArticleTagsType($tagManager);
        $resolver = new OptionsResolver();

        $type->configureOptions($resolver);
        $options = $resolver->resolve();

        self::assertFalse($options['required']);
        self::assertSame('[]', $options['empty_data']);
        self::assertSame(['CMS', 'Symfony'], $options['tag_suggestions']);

        $view = new FormView();
        $type->buildView($view, $this->createStub(FormInterface::class), $options);

        self::assertSame(['CMS', 'Symfony'], $view->vars['tag_suggestions']);
    }

    public function testItUsesHiddenTypeParent(): void
    {
        $type = new ArticleTagsType($this->createStub(ArticleTagManager::class));

        self::assertSame(HiddenType::class, $type->getParent());
    }
}
