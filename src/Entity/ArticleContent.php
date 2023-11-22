<?php

namespace Softspring\CmsBlogPlugin\Entity;

use Softspring\CmsBlogPlugin\Model\ArticleContentInterface;
use Softspring\CmsBlogPlugin\Model\ArticleContentTrait;
use Softspring\CmsBundle\Entity\Content;
use Softspring\CmsBundle\Model\ContentVersionInterface;

class ArticleContent extends Content implements ArticleContentInterface
{
    use ArticleContentTrait;

    public function setPublishedVersion(?ContentVersionInterface $publishedVersion): void
    {
        $this->publishedVersion = $publishedVersion;

        if (!$this->getPublishedAt()) {
            $this->setPublishedAt(new \DateTime('now'));
        }
    }
}
