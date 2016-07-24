<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBusBundle\Factory;

use Innmind\EventBusBundle\{
    ContainerAwareEventBus,
    Factory\ContainerAwareEventBusFactory
};
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareEventBusFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testMake()
    {
        $bus = ContainerAwareEventBusFactory::make(
            $this->createMock(ContainerInterface::class),
            ['stdClass' => ['foo']]
        );

        $this->assertInstanceOf(ContainerAwareEventBus::class, $bus);
    }
}
