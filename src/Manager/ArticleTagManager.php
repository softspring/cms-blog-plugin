<?php

namespace Softspring\CmsBlogPlugin\Manager;

use Softspring\CmsBundle\Manager\ContentManagerInterface;

class ArticleTagManager
{
    public function __construct(protected ContentManagerInterface $contentManager)
    {
    }

    public function getExistingTags(): array
    {
        $rows = $this->contentManager->getRepository('article')->createQueryBuilder('a')
            ->select('a.tags')
            ->andWhere('a.tags IS NOT NULL')
            ->getQuery()
            ->getScalarResult();

        $tags = [];
        $seenTags = [];

        foreach ($rows as $row) {
            $rowTags = $row['tags'] ?? null;

            if (is_string($rowTags)) {
                $decodedTags = json_decode($rowTags, true);
                $rowTags = is_array($decodedTags) ? $decodedTags : [];
            }

            if (!is_array($rowTags)) {
                continue;
            }

            foreach ($rowTags as $tag) {
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

                $seenTags[$normalizedKey] = $normalizedTag;
                $tags[] = $normalizedTag;
            }
        }

        natcasesort($tags);

        return array_values($tags);
    }

    public function canonicalizeTags(array $tags): array
    {
        $existingTags = [];

        foreach ($this->getExistingTags() as $existingTag) {
            $existingTags[mb_strtolower($existingTag)] = $existingTag;
        }

        $canonicalTags = [];

        foreach ($tags as $tag) {
            if (!is_scalar($tag)) {
                continue;
            }

            $normalizedTag = preg_replace('/\s+/', ' ', trim((string) $tag)) ?: '';

            if ('' === $normalizedTag) {
                continue;
            }

            $normalizedKey = mb_strtolower($normalizedTag);
            $canonicalTags[] = $existingTags[$normalizedKey] ?? $normalizedTag;
            $existingTags[$normalizedKey] = $existingTags[$normalizedKey] ?? $normalizedTag;
        }

        return $canonicalTags;
    }
}
