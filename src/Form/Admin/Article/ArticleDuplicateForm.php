<?php

namespace Softspring\CmsBlogPlugin\Form\Admin\Article;

use Doctrine\ORM\EntityManagerInterface;
use Softspring\CmsBlogPlugin\Form\Type\ArticleTagsType;
use Softspring\CmsBlogPlugin\Model\ArticleAuthorInterface;
use Softspring\CmsBundle\Form\Admin\Content\ContentDuplicateForm;
use Softspring\CmsBundle\Form\Type\UserType;
use Softspring\CmsBundle\Translator\TranslatableContext;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticleDuplicateForm extends ContentDuplicateForm
{
    public function __construct(TranslatableContext $translatableContext, EntityManagerInterface $em)
    {
        parent::__construct($translatableContext, $em);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('publishedAt', DateTimeType::class, [
            'required' => false,
            'widget' => 'single_text',
        ]);

        $builder->add('tags', ArticleTagsType::class);

        $dataClass = $options['content_config']['entity_class'] ?? $options['data_class'] ?? null;

        if (is_string($dataClass) && is_a($dataClass, ArticleAuthorInterface::class, true)) {
            $builder->add('author', UserType::class, [
                'required' => true,
            ]);
        }
    }
}
