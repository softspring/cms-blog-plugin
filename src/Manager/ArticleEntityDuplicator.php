<?php

namespace Softspring\CmsBlogPlugin\Manager;

use Softspring\CmsBlogPlugin\Entity\ArticleContent;
use Softspring\CmsBundle\Manager\ContentEntityDuplicatorInterface;
use Softspring\CmsBundle\Model\ContentInterface;

class ArticleEntityDuplicator implements ContentEntityDuplicatorInterface
{
    public function supports(ContentInterface $content): bool
    {
        return $content instanceof ArticleContent;
    }

    /**
     * @param ArticleContent $oldContent
     * @param ArticleContent $newContent
     */
    public function duplicateData(ContentInterface $oldContent, ContentInterface $newContent): void
    {
        $newContent->setAuthor($oldContent->getAuthor());
        $newContent->setPublishedAt($oldContent->getPublishedAt());
    }
}
