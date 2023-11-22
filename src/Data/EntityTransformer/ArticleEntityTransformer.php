<?php

namespace Softspring\CmsBlogPlugin\Data\EntityTransformer;

use Softspring\CmsBlogPlugin\Model\ArticleContentInterface;
use Softspring\CmsBundle\Data\EntityTransformer\ContentEntityTransformer;
use Softspring\CmsBundle\Data\Exception\InvalidElementException;
use Softspring\CmsBundle\Data\ReferencesRepository;
use Softspring\CmsBundle\Model\ContentInterface;

class ArticleEntityTransformer extends ContentEntityTransformer
{
    public function supports(string $type, $data = null): bool
    {
        if ('article' === $type) {
            return true;
        }

        return false;
    }

    public function export(object $element, &$files = [], object $contentVersion = null, string $contentType = null): array
    {
        if (!$element instanceof ArticleContentInterface) {
            throw new InvalidElementException(sprintf('%s dumper requires that $element to be an instance of %s, %s given.', get_called_class(), ArticleContentInterface::class, get_class($element)));
        }

        $export = parent::export($element, $files, $contentVersion, $contentType);

        $export['article']['published_at'] = $element->getPublishedAt()?->format('H:i:s d-m-Y');

        return $export;
    }

    public function import(array $data, ReferencesRepository $referencesRepository, array $options = []): ContentInterface
    {
        /** @var ArticleContentInterface $article */
        $article = parent::import($data, $referencesRepository, $options);

        $article->setPublishedAt($data['article']['published_at'] ? new \DateTime($data['article']['published_at']) : null);

        return $article;
    }
}
