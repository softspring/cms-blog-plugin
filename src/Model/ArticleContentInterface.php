<?php

namespace Softspring\CmsBlogBundle\Model;

use Softspring\CmsBundle\Model\ContentInterface;

interface ArticleContentInterface extends ContentInterface
{
    public function getPublishedAt(): ?\DateTime;

    public function setPublishedAt(\DateTime $publishedAt = null): void;
}
