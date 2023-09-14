<?php

namespace Softspring\CmsBlogBundle\DependencyInjection;

use Scienta\DoctrineJsonFunctions\Query\AST\Functions\Mariadb\JsonValue;
use Scienta\DoctrineJsonFunctions\Query\AST\Functions\Mysql\JsonExtract;
use Scienta\DoctrineJsonFunctions\Query\AST\Functions\Mysql\JsonSearch;
use Softspring\CmsBlogBundle\Entity\ArticleContent;
use Softspring\CmsBlogBundle\Model\ArticleContentInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SfsCmsBlogExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config/services'));

        // configure article classes
        $container->setParameter('sfs_cms_blog.article.class', $config['article']['class']);

        $this->processDataClasses($container);

        // load services
        $loader->load('services.yaml');
    }

    protected function processDataClasses(ContainerBuilder $container): void
    {
        $superClassList = [];

        if (ArticleContent::class !== $container->getParameter('sfs_cms_blog.article.class')) {
            $superClassList[] = ArticleContent::class;
        }

        $container->setParameter('sfs_cms_blog.convert_superclass_list', $superClassList);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $doctrineConfig = [
            'orm' => [
                'dql' => [
                    'string_functions' => [
                        'JSON_EXTRACT' => JsonExtract::class,
                        'JSON_SEARCH' => JsonSearch::class,
                        'JSON_VALUE' => JsonValue::class,
                    ],
                ],
            ],
        ];

        // add a default config to force load target_entities, will be overwritten by ResolveDoctrineTargetEntityPass
        $doctrineConfig['orm']['resolve_target_entities'][ArticleContentInterface::class] = ArticleContent::class;

        // disable auto-mapping for this bundle to prevent mapping errors
        $doctrineConfig['orm']['mappings']['SfsCmsBlogBundle'] = [
            'is_bundle' => true,
            'mapping' => true,
        ];

        $container->prependExtensionConfig('doctrine', $doctrineConfig);

        $cmsConfig = [
            'collections' => [
                'vendor/softspring/cms-blog-bundle/cms',
            ],
        ];

        $container->prependExtensionConfig('sfs_cms', $cmsConfig);

        $container->prependExtensionConfig('doctrine_migrations', [
            'migrations_paths' => [
                'Softspring\CmsBlogBundle\Migrations' => '@SfsCmsBlogBundle/src/Migrations',
            ],
        ]);

        //        $version = InstalledVersions::getVersion('softspring/cms-bundle');
        //        if (str_ends_with($version, '-dev')) {
        //            $version = InstalledVersions::getPrettyVersion('softspring/cms-bundle');
        //        }
        //        $container->prependExtensionConfig('twig', [
        //            'globals' => [
        //                'sfs_cms_bundle' => [
        //                    'version' => $version,
        //                    'version_branch' => str_ends_with($version, '-dev') ? str_replace('.x-dev', '', $version) : false,
        //                ],
        //            ],
        //            'paths' => [
        //                '%kernel.project_dir%/vendor/softspring/polymorphic-form-type/templates' => 'SfsPolymorphicFormType',
        //            ],
        //        ]);
    }
}
