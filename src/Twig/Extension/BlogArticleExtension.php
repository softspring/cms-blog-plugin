<?php

namespace Softspring\CmsBlogPlugin\Twig\Extension;

use Softspring\CmsBlogPlugin\Entity\ArticleContent;
use Softspring\CmsBundle\Manager\ContentManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BlogArticleExtension extends AbstractExtension
{
    public function __construct(protected ContentManagerInterface $contentManager, protected RequestStack $requestStack)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sfs_cms_related_blog_articles', $this->getRelatedBlogArticles(...)),
        ];
    }

    public function getRelatedBlogArticles(string $tag, mixed $content = null, int $limit = 3, string $order = 'published_at_desc'): array
    {
        $tag = trim($tag);
        $locale = $this->requestStack->getCurrentRequest()?->getLocale() ?: 'en';

        if ('' === $tag) {
            return [];
        }

        $repo = $this->contentManager->getRepository('article');
        $qb = $repo->createQueryBuilder('a');

        $qb->andWhere('a.publishedVersion IS NOT NULL');
        $qb->andWhere('a.publishedAt <= :publishedAt')->setParameter('publishedAt', time());
        $qb->andWhere('a.locales LIKE :locale')->setParameter('locale', '%"'.$locale.'"%');
        $qb->andWhere('a.tags LIKE :tag')->setParameter('tag', '%"'.$tag.'"%');

        if ($content instanceof ArticleContent && null !== $content->getId()) {
            $qb->andWhere('a.id != :current')->setParameter('current', $content->getId());
        }

        match ($order) {
            'published_at_asc' => $qb->addOrderBy('a.publishedAt', 'ASC'),
            'name_asc' => $qb->addOrderBy('a.name', 'ASC'),
            'name_desc' => $qb->addOrderBy('a.name', 'DESC'),
            default => $qb->addOrderBy('a.publishedAt', 'DESC'),
        };

        $qb->setMaxResults(max(1, $limit));

        return $qb->getQuery()->getResult();
    }
}
