<?php

namespace Softspring\CmsBlogPlugin\Form\Admin\Article;

use Softspring\CmsBlogPlugin\Model\ArticleAuthorInterface;
use Softspring\CmsBlogPlugin\Form\Type\ArticleTagsType;
use Softspring\CmsBundle\Form\Admin\Content\ContentUpdateForm;
use Softspring\CmsBundle\Form\Type\UserType;
use Softspring\CmsBundle\Translator\TranslatableContext;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticleUpdateForm extends ContentUpdateForm
{
    public function __construct(TranslatableContext $translatableContext)
    {
        parent::__construct($translatableContext);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('publishedAt', DateTimeType::class, [
            'required' => false,
            'widget' => 'single_text',
        ]);

        $builder->add('tags', ArticleTagsType::class);

        $dataClass = $options['data_class'] ?? null;

        if (is_string($dataClass) && is_a($dataClass, ArticleAuthorInterface::class, true)) {
            $builder->add('author', UserType::class, [
                'required' => true,
            ]);
        }
    }
}
