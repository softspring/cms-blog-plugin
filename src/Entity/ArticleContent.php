<?php

namespace Softspring\CmsBlogPlugin\Entity;

use DateTime;
use InvalidArgumentException;
use Softspring\CmsBlogPlugin\Model\ArticleAuthorInterface;
use Softspring\CmsBlogPlugin\Model\ArticleAuthorTrait;
use Softspring\CmsBlogPlugin\Model\ArticleContentInterface;
use Softspring\CmsBlogPlugin\Model\ArticleContentTrait;
use Softspring\CmsBundle\Entity\Content;
use Softspring\CmsBundle\Model\ContentVersionInterface;
use Softspring\CmsBundle\Model\VersionInterface;

class ArticleContent extends Content implements ArticleContentInterface, ArticleAuthorInterface
{
    use ArticleContentTrait;
    use ArticleAuthorTrait;

    public function setPublishedVersion(?VersionInterface $publishedVersion): void
    {
        if (null === $publishedVersion) {
            $this->publishedVersion = null;

            return;
        }

        if (!$publishedVersion instanceof ContentVersionInterface) {
            throw new InvalidArgumentException('Published version must be an instance of ContentVersionInterface.');
        }

        $this->publishedVersion = $publishedVersion;

        if (!$this->getPublishedAt()) {
            $this->setPublishedAt(new DateTime('now'));
        }
    }
}
