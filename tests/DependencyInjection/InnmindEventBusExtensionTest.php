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
use PHPUnit\Framework\TestCase;

class InnmindEventBusExtensionTest extends TestCase
{
    public function testLoad()
    {
        $container = new ContainerBuilder;
        $extension = new InnmindEventBusExtension;

        $this->assertInstanceOf(Extension::class, $extension);
        $this->assertNull($extension->load(
            [],
            $container
        ));

        (new InnmindEventBusBundle)->build($container);
        $container->compile();

        $this->assertTrue($container->hasParameter('innmind_event_bus.stack'));
        $this->assertSame(
            ['queue', 'default'],
            $container->getParameter('innmind_event_bus.stack')
        );
        $this->assertSame(
            'innmind_event_bus.queue',
            (string) $container->getAlias('innmind_event_bus')
        );
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The path "innmind_event_bus.stack" should have at least 1 element(s) defined.
     */
    public function testThrowWhenEmptyStack()
    {
        (new InnmindEventBusExtension)->load(
            [[
                'stack' => [],
            ]],
            new ContainerBuilder
        );
    }
}
