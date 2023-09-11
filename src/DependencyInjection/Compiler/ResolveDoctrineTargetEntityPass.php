<?php

namespace Softspring\CmsBlogBundle\DependencyInjection\Compiler;

use Softspring\CmsBlogBundle\Model\ArticleContentInterface;
use Softspring\Component\DoctrineTargetEntityResolver\DependencyInjection\Compiler\AbstractResolveDoctrineTargetEntityPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ResolveDoctrineTargetEntityPass extends AbstractResolveDoctrineTargetEntityPass
{
    protected function getEntityManagerName(ContainerBuilder $container): string
    {
        return $container->getParameter('sfs_cms.entity_manager_name');
    }

    public function process(ContainerBuilder $container): void
    {
        $this->setTargetEntityFromParameter('sfs_cms_blog.article.class', ArticleContentInterface::class, $container, true);
    }
}
