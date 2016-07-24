<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBusBundle\DependencyInjection;

use Innmind\EventBusBundle\{
    DependencyInjection\InnmindEventBusExtension,
    InnmindEventBusBundle
};
use Symfony\Component\{
    HttpKernel\DependencyInjection\Extension,
    DependencyInjection\ContainerBuilder
};

class InnmindEventBusExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $container = new ContainerBuilder;
        $extension = new InnmindEventBusExtension;

        $this->assertInstanceOf(Extension::class, $extension);
        $this->assertNull($extension->load(
            [[
                'stack' => ['foo']
            ]],
            $container
        ));

        (new InnmindEventBusBundle)->build($container);
        $container->compile();

        $this->assertTrue($container->hasParameter('innmind_event_bus.stack'));
        $this->assertSame(
            ['foo'],
            $container->getParameter('innmind_event_bus.stack')
        );
    }
}
