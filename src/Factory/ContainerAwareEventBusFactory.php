<?php
declare(strict_types = 1);

namespace Innmind\EventBusBundle\Factory;

use Innmind\EventBusBundle\ContainerAwareEventBus;
use Innmind\Immutable\{
    Map,
    SetInterface,
    Set
};
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContainerAwareEventBusFactory
{
    public static function make(
        ContainerInterface $container,
        array $services
    ): ContainerAwareEventBus {
        $map = new Map('string', SetInterface::class);

        foreach ($services as $class => $listeners) {
            $set = new Set('string');

            foreach ($listeners as $listener) {
                $set = $set->add($listener);
            }

            $map = $map->put($class, $set);
        }

        return new ContainerAwareEventBus($container, $map);
    }
}
