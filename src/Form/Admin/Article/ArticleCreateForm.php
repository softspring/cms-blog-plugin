<?php

namespace Softspring\CmsBlogBundle\Form\Admin\Article;

use Softspring\CmsBundle\Form\Admin\Content\ContentCreateForm;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticleCreateForm extends ContentCreateForm
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('publishedAt', DateTimeType::class, [
            'required' => false,
        ]);
    }
}
