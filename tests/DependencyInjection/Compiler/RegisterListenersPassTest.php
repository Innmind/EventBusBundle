<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBusBundle\DependencyInjection\Compiler;

use Innmind\EventBusBundle\DependencyInjection\{
    Compiler\RegisterListenersPass,
    InnmindEventBusExtension
};
use Symfony\Component\DependencyInjection\{
    Compiler\CompilerPassInterface,
    ContainerBuilder,
    Definition
};

class RegisterListenersPassTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CompilerPassInterface::class,
            new RegisterListenersPass
        );
    }

    public function testProcess()
    {
        $container = new ContainerBuilder;
        (new InnmindEventBusExtension)->load([], $container);
        $container->setDefinition(
            'foo',
            (new Definition('stdClass'))->addTag(
                'innmind_event_bus.listener',
                ['listen_to' => 'bar']
            )
        );

        $this->assertNull((new RegisterListenersPass)->process($container));
        $listeners = $container
            ->getDefinition('innmind_event_bus.default')
            ->getArgument(1);
        $this->assertCount(1, $listeners);
        $this->assertSame(['bar' => ['foo']], $listeners);
    }

    /**
     * @expectedException Innmind\EventBusBundle\Exception\LogicException
     */
    public function testThrowWhenMissingListenToAttribute()
    {
        $container = new ContainerBuilder;
        (new InnmindEventBusExtension)->load([], $container);
        $container->setDefinition(
            'foo',
            (new Definition('stdClass'))->addTag(
                'innmind_event_bus.listener'
            )
        );

        (new RegisterListenersPass)->process($container);
    }
}
