<?php

namespace Softspring\CmsBlogPlugin;

use Softspring\CmsBlogPlugin\Model\ArticleContentInterface;
use Softspring\CmsBundle\Plugin\SfsCmsPlugin;

class SfsCmsBlogPlugin extends SfsCmsPlugin
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    protected function getTargetEntities(): array
    {
        return [
            [
                'parameterName' => 'sfs_cms_blog.article.class',
                'interface' => ArticleContentInterface::class,
                'required' => true,
            ]
        ];
    }

    protected function getTargetEntitiesMappings(): array
    {
        $basePath = realpath(__DIR__.'/../config/doctrine-mapping/');

        return [
            "$basePath/entities" => 'Softspring\CmsBlogPlugin\Entity',
        ];
    }
}
