<?php

namespace Softspring\CmsBlogBundle\Form;

use Softspring\CmsBlogBundle\Entity\ArticleContent;
use Softspring\Component\DoctrinePaginator\Form\PaginatorForm;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleListFilterForm extends PaginatorForm
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'class' => ArticleContent::class,
            'rpp_default_value' => 10,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('text', TextType::class, [
            'mapped' => false,
        ]);
    }
}
