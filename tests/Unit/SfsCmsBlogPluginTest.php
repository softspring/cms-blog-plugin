<?php

declare(strict_types=1);

namespace Softspring\CmsBlogPlugin\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Softspring\CmsBlogPlugin\DependencyInjection\Compiler\ResolveDoctrineTargetEntityPass;
use Softspring\CmsBlogPlugin\SfsCmsBlogPlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SfsCmsBlogPluginTest extends TestCase
{
    public function testItReturnsPackagePathAndAlias(): void
    {
        $plugin = new SfsCmsBlogPlugin();

        self::assertSame(\dirname(__DIR__, 2), $plugin->getPath());
        self::assertSame('sfs_cms_blog', SfsCmsBlogPlugin::getAlias());
    }

    public function testItRegistersCompilerPass(): void
    {
        $container = new ContainerBuilder();

        (new SfsCmsBlogPlugin())->build($container);

        $passes = $container->getCompilerPassConfig()->getBeforeOptimizationPasses();
        self::assertNotEmpty(array_filter($passes, fn (object $pass): bool => $pass instanceof ResolveDoctrineTargetEntityPass));
    }
}
