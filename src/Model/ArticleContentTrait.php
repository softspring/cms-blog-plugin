<?php

namespace Softspring\CmsBlogPlugin\Model;

use DateTime;

trait ArticleContentTrait
{
    protected ?int $publishedAt = null;

    public function getPublishedAt(): ?DateTime
    {
        return $this->publishedAt ? DateTime::createFromFormat('U', "{$this->publishedAt}") : null;
    }

    public function setPublishedAt(?DateTime $publishedAt = null): void
    {
        $this->publishedAt = $publishedAt ? (int) $publishedAt->format('U') : null;
    }
}
