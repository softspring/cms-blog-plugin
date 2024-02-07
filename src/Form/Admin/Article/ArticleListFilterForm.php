<?php

namespace Softspring\CmsBlogPlugin\Form\Admin\Article;

use Softspring\CmsBlogPlugin\Model\AuthorInterface;
use Softspring\CmsBundle\Form\Admin\Content\ContentListFilterForm;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticleListFilterForm extends ContentListFilterForm
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('author', EntityType::class, [
            'class' => AuthorInterface::class,
            'em' => $this->em,
            'choice_label' => 'displayName',
            'required' => false,
        ]);
    }
}
