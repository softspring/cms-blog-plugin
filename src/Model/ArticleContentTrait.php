<?php

namespace Softspring\CmsBlogPlugin\Model;

use DateTime;

trait ArticleContentTrait
{
    protected ?int $publishedAt = null;

    protected ?array $tags = [];

    public function getPublishedAt(): ?DateTime
    {
        return $this->publishedAt ? DateTime::createFromFormat('U', "{$this->publishedAt}") : null;
    }

    public function setPublishedAt(?DateTime $publishedAt = null): void
    {
        $this->publishedAt = $publishedAt instanceof DateTime ? (int) $publishedAt->format('U') : null;
    }

    public function getTags(): array
    {
        return $this->tags ?? [];
    }

    public function setTags(array $tags): void
    {
        $normalizedTags = [];
        $seenTags = [];

        foreach ($tags as $tag) {
            if (!is_scalar($tag)) {
                continue;
            }

            $normalizedTag = preg_replace('/\s+/', ' ', trim((string) $tag)) ?: '';

            if ('' === $normalizedTag) {
                continue;
            }

            $normalizedKey = mb_strtolower($normalizedTag);

            if (isset($seenTags[$normalizedKey])) {
                continue;
            }

            $seenTags[$normalizedKey] = true;
            $normalizedTags[] = $normalizedTag;
        }

        $this->tags = $normalizedTags;
    }
}
