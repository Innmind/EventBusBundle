<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBusBundle;

use Innmind\EventBusBundle\InnmindEventBusBundle;
use Symfony\Component\{
    HttpKernel\Bundle\Bundle,
    DependencyInjection\ContainerBuilder
};

class InnmindEventBusBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $bundle = new InnmindEventBusBundle;
        $container = new ContainerBuilder;

        $this->assertInstanceOf(Bundle::class, $bundle);
        $this->assertNull($bundle->build($container));
    }
}
