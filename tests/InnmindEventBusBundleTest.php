<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBusBundle;

use Innmind\EventBusBundle\{
    InnmindEventBusBundle,
    DependencyInjection\Compiler\BuildEventBusStackPass,
    DependencyInjection\Compiler\RegisterListenersPass
};
use Symfony\Component\{
    HttpKernel\Bundle\Bundle,
    DependencyInjection\ContainerBuilder
};
use PHPUnit\Framework\TestCase;

class InnmindEventBusBundleTest extends TestCase
{
    public function testBuild()
    {
        $bundle = new InnmindEventBusBundle;
        $container = new ContainerBuilder;

        $this->assertInstanceOf(Bundle::class, $bundle);
        $this->assertNull($bundle->build($container));
        $passes = $container
            ->getCompilerPassConfig()
            ->getBeforeOptimizationPasses();
        $this->assertSame(2, count($passes));
        $this->assertInstanceOf(
            BuildEventBusStackPass::class,
            $passes[0]
        );
        $this->assertInstanceOf(
            RegisterListenersPass::class,
            $passes[1]
        );
    }
}
