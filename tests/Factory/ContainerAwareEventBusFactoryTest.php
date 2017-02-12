<?php
declare(strict_types = 1);

namespace Tests\Innmind\EventBusBundle\Factory;

use Innmind\EventBusBundle\{
    ContainerAwareEventBus,
    Factory\ContainerAwareEventBusFactory
};
use Innmind\EventBus\ClassName\ExtractorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PHPUnit\Framework\TestCase;

class ContainerAwareEventBusFactoryTest extends TestCase
{
    public function testMake()
    {
        $bus = ContainerAwareEventBusFactory::make(
            $this->createMock(ContainerInterface::class),
            ['stdClass' => ['foo']],
            $this->createMock(ExtractorInterface::class)
        );

        $this->assertInstanceOf(ContainerAwareEventBus::class, $bus);
    }
}
