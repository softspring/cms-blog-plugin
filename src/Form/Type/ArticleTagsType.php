<?php

namespace Softspring\CmsBlogPlugin\Form\Type;

use Softspring\CmsBlogPlugin\Manager\ArticleTagManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleTagsType extends AbstractType
{
    public function __construct(protected ArticleTagManager $articleTagManager)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new CallbackTransformer(
            fn (?array $tags): string => json_encode($tags ?? [], JSON_THROW_ON_ERROR),
            function (?string $tags): array {
                if (empty($tags)) {
                    return [];
                }

                $decodedTags = json_decode($tags, true);

                return is_array($decodedTags) ? $this->articleTagManager->canonicalizeTags($decodedTags) : [];
            }
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['tag_suggestions'] = $options['tag_suggestions'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'empty_data' => '[]',
            'tag_suggestions' => fn (Options $options): array => $this->articleTagManager->getExistingTags(),
        ]);
        $resolver->setAllowedTypes('tag_suggestions', 'array');
    }

    public function getParent(): string
    {
        return HiddenType::class;
    }
}
