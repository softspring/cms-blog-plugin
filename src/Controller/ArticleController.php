<?php

namespace Softspring\CmsBlogPlugin\Controller;

use Softspring\CmsBlogPlugin\Form\ArticleListFilterForm;
use Softspring\CmsBundle\Manager\ContentManagerInterface;
use Softspring\Component\DoctrinePaginator\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\LocaleSwitcher;

class ArticleController extends AbstractController
{
    protected const INTERNAL_QUERY_PARAMS = ['_locale', '_site', 'ignore_errors', 'isolate_request', 'cms-edit', 'current_route', 'show_filter_form', 'title', 'tag', 'limit', 'article_order', 'current_article'];

    public function __construct(protected ContentManagerInterface $contentManager, protected ?LocaleSwitcher $localeSwitcher = null)
    {
    }

    public function listBlock(Request $request): Response
    {
        $locale = $this->setLocale($request);
        $showFilterForm = $this->getBooleanQuery($request, 'show_filter_form', true);
        $tag = trim((string) $request->query->get('tag', ''));
        $limit = $this->getPositiveIntegerQuery($request, 'limit');
        $articleOrder = (string) $request->query->get('article_order', 'published_at_desc');
        $currentArticle = (string) $request->query->get('current_article', '');
        [$orderField, $orderDirection] = $this->getArticleOrder($articleOrder);

        $query = $this->cleanPublicQuery($this->getPublicQuery($request));
        $request->query->replace($query);

        $repo = $this->contentManager->getRepository('article');

        $qb = $repo->createQueryBuilder('a');
        $qb->andWhere('a.publishedVersion IS NOT NULL');
        $qb->andWhere('a.publishedAt <= :publishedAt')->setParameter('publishedAt', time());
        // tricky way to filter by locale, avoiding json functions
        $qb->andWhere('a.locales LIKE :locale')->setParameter('locale', '%"'.$locale.'"%');

        if ('' !== $tag) {
            $qb->andWhere('a.tags LIKE :tag')->setParameter('tag', '%"'.$tag.'"%');
        }

        if ('' !== $currentArticle) {
            $qb->andWhere('a.id != :currentArticle')->setParameter('currentArticle', $currentArticle);
        }

        $form = $this->createForm(ArticleListFilterForm::class, [], [
            'method' => 'GET',
            'em' => $this->contentManager->getEntityManager(),
            'query_builder' => $qb,
            'rpp_valid_values' => [10],
            'rpp_default_value' => 10,
            'order_valid_fields' => ['publishedAt', 'name'],
            'order_default_value' => $orderField,
            'order_direction_default_value' => $orderDirection,
        ])->handleRequest($request);

        $text = $form->get('text')->getData();

        if ($showFilterForm && $text) {
            $searchLocale = preg_replace('/[^A-Za-z0-9_-]/', '', $request->getLocale());
            $qb->andWhere("LOWER(JSON_VALUE(a.extraData, '$.title.$searchLocale')) LIKE LOWER(:searchText)");
            $qb->setParameter('searchText', '%'.$text.'%');
        }

        if ($limit) {
            $qb->addOrderBy("a.$orderField", strtoupper($orderDirection));
            $qb->setMaxResults($limit);
        }

        $viewData = [
            'query' => $query,
            'articles' => $limit ? $qb->getQuery()->getResult() : Paginator::queryPaginatedFilterForm($form, $request),
        ];

        if ($showFilterForm) {
            $viewData['filterForm'] = $form->createView();
        }

        return $this->render('@block/article_list/render.html.twig', $viewData);
    }

    /**
     * @deprecated Remove as soon as posible
     */
    public function latestBlock(Request $request): Response
    {
        trigger_deprecation('softspring/cms-blog-plugin', '6.0', 'The "article_latest_list" block is deprecated, use the "article_link_list" module instead.');

        $locale = $this->setLocale($request);

        $query = $this->getPublicQuery($request);
        foreach ($query as $k => $v) {
            $request->query->set($k, $v);
        }

        $repo = $this->contentManager->getRepository('article');

        $qb = $repo->createQueryBuilder('a');
        $qb->andWhere('a.publishedVersion IS NOT NULL');
        $qb->andWhere('a.publishedAt <= '.time());
        // tricky way to filter by locale, avoiding json functions
        $qb->andWhere('a.locales LIKE :locale')->setParameter('locale', '%"'.$locale.'"%');

        if ($request->query->has('current')) {
            $qb->andWhere('a.id != :current')->setParameter('current', $request->query->get('current'));
        }

        $maxResults = $request->query->get('maxResults', 5);

        $qb->addOrderBy('a.publishedAt', 'DESC');
        $qb->setMaxResults($maxResults);

        $viewData = [
            'articles' => $qb->getQuery()->getResult(),
        ];

        return $this->render('@block/article_latest_list/render.html.twig', $viewData);
    }

    public function linkListBlock(Request $request): Response
    {
        $locale = $this->setLocale($request);
        $title = $this->getLocalizedStringQuery($request, 'title', $locale);
        $tag = trim((string) $request->query->get('tag', ''));
        $limit = $this->getPositiveIntegerQuery($request, 'limit') ?? 5;
        $articleOrder = (string) $request->query->get('article_order', 'published_at_desc');
        $currentArticle = (string) $request->query->get('current_article', '');
        [$orderField, $orderDirection] = $this->getArticleOrder($articleOrder);

        $repo = $this->contentManager->getRepository('article');

        $qb = $repo->createQueryBuilder('a');
        $qb->andWhere('a.publishedVersion IS NOT NULL');
        $qb->andWhere('a.publishedAt <= :publishedAt')->setParameter('publishedAt', time());
        // tricky way to filter by locale, avoiding json functions
        $qb->andWhere('a.locales LIKE :locale')->setParameter('locale', '%"'.$locale.'"%');

        if ('' !== $tag) {
            $qb->andWhere('a.tags LIKE :tag')->setParameter('tag', '%"'.$tag.'"%');
        }

        if ('' !== $currentArticle) {
            $qb->andWhere('a.id != :currentArticle')->setParameter('currentArticle', $currentArticle);
        }

        $qb->addOrderBy("a.$orderField", strtoupper($orderDirection));

        $qb->setMaxResults($limit);

        return $this->render('@block/article_link_list/render.html.twig', [
            'articles' => $qb->getQuery()->getResult(),
            'title' => $title,
        ]);
    }

    public function headerDataBlock(string $article, Request $request): Response
    {
        $this->setLocale($request);

        $article = $this->contentManager->getRepository('article')->findOneById($article);

        return $this->render('@block/article_header_data/render.html.twig', [
            'article' => $article,
        ]);
    }

    public function setLocale(Request $request): string
    {
        $locale = $request->query->get('_locale', $request->getLocale() ?: 'en');
        $this->localeSwitcher && $this->localeSwitcher->setLocale($locale);
        $request->setLocale($locale);

        return $locale;
    }

    protected function getPublicQuery(Request $request): array
    {
        $url = $request->headers->get('x-original-uri') ?: $request->server->get('SFS_CMS_REQUEST_URI') ?: $request->getRequestUri();
        $query = [];
        parse_str(parse_url($url, PHP_URL_QUERY) ?: '', $query);

        return $query ?: $request->query->all();
    }

    protected function cleanPublicQuery(array $query): array
    {
        foreach (self::INTERNAL_QUERY_PARAMS as $param) {
            unset($query[$param]);
        }

        return $query;
    }

    protected function getBooleanQuery(Request $request, string $name, bool $default): bool
    {
        if (!$request->query->has($name)) {
            return $default;
        }

        return filter_var($request->query->get($name), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
    }

    protected function getPositiveIntegerQuery(Request $request, string $name): ?int
    {
        $value = $request->query->get($name);

        if (null === $value || '' === $value) {
            return null;
        }

        return max(1, (int) $value);
    }

    protected function getLocalizedStringQuery(Request $request, string $name, string $locale): string
    {
        $query = $request->query->all();
        $value = $query[$name] ?? '';

        if (is_array($value)) {
            return trim((string) ($value[$locale] ?? reset($value) ?: ''));
        }

        return trim((string) $request->query->get($name, ''));
    }

    protected function getArticleOrder(string $order): array
    {
        return match ($order) {
            'published_at_asc' => ['publishedAt', 'asc'],
            'name_asc' => ['name', 'asc'],
            'name_desc' => ['name', 'desc'],
            default => ['publishedAt', 'desc'],
        };
    }
}
