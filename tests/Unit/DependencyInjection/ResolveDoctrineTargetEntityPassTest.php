<?php

declare(strict_types=1);

namespace Softspring\CmsBlogPlugin\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Softspring\CmsBlogPlugin\DependencyInjection\Compiler\ResolveDoctrineTargetEntityPass;
use Softspring\CmsBlogPlugin\Model\AuthorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ResolveDoctrineTargetEntityPassTest extends TestCase
{
    public function testItRegistersAuthorTargetEntity(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('sfs_cms.entity_manager_name', 'default');
        $container->setParameter('sfs_cms_blog.author.class', TargetEntityAuthorFixture::class);
        $container->setDefinition('doctrine.orm.listeners.resolve_target_entity', new Definition());

        (new ResolveDoctrineTargetEntityPass())->process($container);

        $definition = $container->getDefinition('doctrine.orm.listeners.resolve_target_entity');
        self::assertTrue($definition->hasTag('doctrine.event_subscriber'));
        self::assertSame(
            [['addResolveTargetEntity', [AuthorInterface::class, TargetEntityAuthorFixture::class, []]]],
            $definition->getMethodCalls()
        );
    }
}

class TargetEntityAuthorFixture implements AuthorInterface
{
    public function getDisplayName(): string
    {
        return 'Author';
    }

    public function getAvatarUrl(): string
    {
        return '/author.png';
    }
}
