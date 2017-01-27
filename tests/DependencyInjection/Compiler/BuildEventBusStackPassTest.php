<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBusBundle\DependencyInjection\Compiler;

use Innmind\EventBusBundle\{
    DependencyInjection\Compiler\BuildEventBusStackPass,
    ContainerAwareEventBus,
    Factory\ContainerAwareEventBusFactory
};
use Innmind\EventBus\{
    EventBusInterface,
    QueueableEventBus,
    ClassName\InheritanceExtractor
};
use Symfony\Component\DependencyInjection\{
    Compiler\CompilerPassInterface,
    ContainerBuilder,
    Definition,
    Reference
};

class BuildEventBusStackPassTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CompilerPassInterface::class,
            new BuildEventBusStackPass
        );
    }

    public function testProcess()
    {
        $container = new ContainerBuilder;
        $mock = $this->createMock(EventBusInterface::class);
        $bus1 = new class($mock, 'foo') implements EventBusInterface {
            public static $called = false;
            private $bus;

            public function __construct(EventBusInterface $bus, string $whatever)
            {
                $this->bus = $bus;
            }

            public function dispatch($event): EventBusInterface
            {
                $this->bus->dispatch($event);
                self::$called = true;

                return $this;
            }
        };
        $bus2 = new class('bar', $mock) implements EventBusInterface {
            public static $called = false;
            private $bus;

            public function __construct(string $whatever, EventBusInterface $bus)
            {
                $this->bus = $bus;
            }

            public function dispatch($event): EventBusInterface
            {
                $this->bus->dispatch($event);
                self::$called = true;

                return $this;
            }
        };
        $container->setDefinition(
            'command_bus.queue',
            (new Definition(QueueableEventBus::class, [null]))->addTag(
                'innmind_event_bus',
                ['alias' => 'queue']
            )
        );
        $container->setDefinition(
            'command_bus.first',
            (new Definition(get_class($bus1), [null, 'foo']))->addTag(
                'innmind_event_bus',
                ['alias' => 'first']
            )
        );
        $container->setDefinition(
            'command_bus.second',
            (new Definition(get_class($bus2), ['bar', null]))->addTag(
                'innmind_event_bus',
                ['alias' => 'second']
            )
        );
        $container->setDefinition(
            'command_bus.default',
            (new Definition(
                ContainerAwareEventBus::class,
                [
                    new Reference('service_container'),
                    ['stdClass' => ['listener']],
                    new Reference('extractor'),
                ]
            ))
                ->setFactory([ContainerAwareEventBusFactory::class, 'make'])
                ->addTag('innmind_event_bus', ['alias' => 'default'])
        );
        $container->setDefinition(
            'extractor',
            new Definition(InheritanceExtractor::class)
        );
        $container->set(
            'listener',
            function($event) {}
        );
        $container->setParameter(
            'innmind_event_bus.stack',
            ['queue', 'first', 'second', 'default']
        );

        $this->assertNull((new BuildEventBusStackPass)->process($container));
        $this->assertSame(
            'command_bus.queue',
            (string) $container->getAlias('innmind_event_bus')
        );
        $this->assertInstanceOf(
            Reference::class,
            $container->getDefinition('command_bus.queue')->getArgument(0)
        );
        $this->assertInstanceOf(
            Reference::class,
            $container->getDefinition('command_bus.first')->getArgument(0)
        );
        $this->assertInstanceOf(
            Reference::class,
            $container->getDefinition('command_bus.second')->getArgument(1)
        );
        $this->assertSame(
            'command_bus.first',
            (string) $container->getDefinition('command_bus.queue')->getArgument(0)
        );
        $this->assertSame(
            'command_bus.second',
            (string) $container->getDefinition('command_bus.first')->getArgument(0)
        );
        $this->assertSame(
            'command_bus.default',
            (string) $container->getDefinition('command_bus.second')->getArgument(1)
        );
        $this->assertSame(
            $container->get('innmind_event_bus'),
            $container->get('innmind_event_bus')->dispatch(new \stdClass)
        );
        $this->assertTrue($bus1::$called);
        $this->assertTrue($bus2::$called);
    }

    /**
     * @expectedException Innmind\EventBusBundle\Exception\LogicException
     */
    public function testThrowWhenStackIsNotDefined()
    {
        (new BuildEventBusStackPass)->process(new ContainerBuilder);
    }

    public function testProcessWithOneElementInTheStack()
    {
        $container = new ContainerBuilder;
        $container->setParameter('innmind_event_bus.stack', ['default']);
        $container->setDefinition(
            'command_bus.default',
            (new Definition(
                ContainerAwareEventBus::class,
                [
                    new Reference('service_container'),
                    ['stdClass' => ['listener']]
                ]
            ))
                ->setFactory([ContainerAwareEventBusFactory::class, 'make'])
                ->addTag('innmind_event_bus', ['alias' => 'default'])
        );

        $this->assertNull((new BuildEventBusStackPass)->process($container));
        $this->assertSame(
            'command_bus.default',
            (string) $container->getAlias('innmind_event_bus')
        );
    }

    /**
     * @expectedException Innmind\EventBusBundle\Exception\LogicException
     */
    public function testThrowWhenMissingAlias()
    {
        $container = new ContainerBuilder;
        $container->setParameter('innmind_event_bus.stack', ['default']);
        $container->setDefinition(
            'command_bus.default',
            (new Definition(
                ContainerAwareEventBus::class,
                [
                    new Reference('service_container'),
                    ['stdClass' => ['listener']]
                ]
            ))
                ->addTag('innmind_event_bus')
        );

        (new BuildEventBusStackPass)->process($container);
    }

    /**
     * @expectedException Innmind\EventBusBundle\Exception\LogicException
     * @expectedExceptionMessageRegExp /^Missing argument type hinted with EventBusInterface for "class@anonymous.+"$/
     */
    public function testThrowWhenNoEventBusTypeHint()
    {
        $container = new ContainerBuilder;
        $bus1 = new class implements EventBusInterface {
            public function __construct()
            {
            }

            public function dispatch($event): EventBusInterface
            {
                $this->bus->dispatch($event);
                self::$called = true;

                return $this;
            }
        };
        $container->setDefinition(
            'command_bus.queue',
            (new Definition(QueueableEventBus::class, [null]))->addTag(
                'innmind_event_bus',
                ['alias' => 'queue']
            )
        );
        $container->setDefinition(
            'command_bus.first',
            (new Definition(get_class($bus1), []))->addTag(
                'innmind_event_bus',
                ['alias' => 'first']
            )
        );
        $container->setDefinition(
            'command_bus.default',
            (new Definition(
                ContainerAwareEventBus::class,
                [
                    new Reference('service_container'),
                    []
                ]
            ))
                ->setFactory([ContainerAwareEventBusFactory::class, 'make'])
                ->addTag('innmind_event_bus', ['alias' => 'default'])
        );
        $container->setParameter(
            'innmind_event_bus.stack',
            ['queue', 'first', 'default']
        );

        (new BuildEventBusStackPass)->process($container);
    }
}
