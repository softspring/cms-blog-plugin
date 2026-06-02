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
    protected const INTERNAL_QUERY_PARAMS = ['_locale', '_site', 'ignore_errors', 'isolate_request', 'cms-edit', 'current_route'];

    public function __construct(protected ContentManagerInterface $contentManager, protected ?LocaleSwitcher $localeSwitcher = null)
    {
    }

    public function listBlock(Request $request): Response
    {
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
        $qb->addOrderBy('a.publishedAt', 'DESC');

        $form = $this->createForm(ArticleListFilterForm::class, [], [
            'method' => 'GET',
            'em' => $this->contentManager->getEntityManager(),
            'query_builder' => $qb,
        ])->handleRequest($request);

        $text = $form->get('text')->getData();

        if ($text) {
            $qb->andWhere("LOWER(JSON_VALUE(a.extraData, '$.title.{$request->getLocale()}')) LIKE LOWER('%$text%')");
        }

        $viewData = [
            'query' => $this->cleanPublicQuery($request->query->all()),
            'articles' => Paginator::queryPaginatedFilterForm($form, $request),
            'filterForm' => $form->createView(),
        ];

        return $this->render('@block/article_list/render.html.twig', $viewData);
    }

    public function latestBlock(Request $request): Response
    {
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
}
