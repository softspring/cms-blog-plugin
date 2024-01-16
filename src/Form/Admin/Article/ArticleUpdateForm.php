<?php

namespace Softspring\CmsBlogPlugin\Form\Admin\Article;

use Softspring\CmsBlogPlugin\Model\ArticleAuthorInterface;
use Softspring\CmsBundle\Form\Admin\Content\ContentUpdateForm;
use Softspring\CmsBundle\Form\Type\UserType;
use Softspring\CmsBundle\Manager\ContentManagerInterface;
use Softspring\CmsBundle\Translator\TranslatableContext;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticleUpdateForm extends ContentUpdateForm
{
    public function __construct(protected ContentManagerInterface $contentManager, TranslatableContext $translatableContext)
    {
        parent::__construct($translatableContext);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $entityClass = new \ReflectionClass($this->contentManager->getTypeClass($options['content_config']['_id']));

        parent::buildForm($builder, $options);

        $builder->add('publishedAt', DateTimeType::class, [
            'required' => false,
            'widget' => 'single_text',
        ]);

        if ($entityClass->implementsInterface(ArticleAuthorInterface::class)) {
            $builder->add('author', UserType::class, [
                'required' => true,
            ]);
        }
    }
}
