<?php

namespace Softspring\CmsBlogPlugin\Form\Type;

use Softspring\CmsBlogPlugin\Manager\ArticleTagManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogArticleTagType extends AbstractType
{
    public function __construct(protected ArticleTagManager $articleTagManager)
    {
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $tags = $this->articleTagManager->getExistingTags();
        $choices = array_combine($tags, $tags) ?: [];

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => null,
            'choices' => $choices,
            'choice_translation_domain' => false,
        ]);
    }
}
