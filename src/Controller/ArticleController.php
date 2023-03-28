<?php

namespace Softspring\CmsBlogBundle\Controller;

use Softspring\CmsBlogBundle\Form\ArticleListFilterForm;
use Softspring\CmsBundle\Manager\ContentManagerInterface;
use Softspring\Component\DoctrinePaginator\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends AbstractController
{
    protected ContentManagerInterface $contentManager;

    public function __construct(ContentManagerInterface $contentManager)
    {
        $this->contentManager = $contentManager;
    }

    public function listBlock(Request $request): Response
    {
        $url = $request->headers->get('x-original-uri');
        $query = [];
        parse_str(parse_url($url, PHP_URL_QUERY), $query);
        foreach ($query as $k => $v) {
            $request->query->set($k, $v);
        }

        $repo = $this->contentManager->getRepository('article');

        $qb = $repo->createQueryBuilder('a');
        $qb->andWhere('a.publishedVersion IS NOT NULL');
        $qb->andWhere('a.publishedAt <= '.time());
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
            'query' => $query,
            'articles' => Paginator::queryPaginatedFilterForm($form, $request),
            'filterForm' => $form->createView(),
        ];

        return $this->render('@block/article_list/render.html.twig', $viewData);
    }
}
