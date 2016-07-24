<?php
declare(strict_types = 1);

namespace Innmind\EventBusBundle\DependencyInjection\Compiler;

use Innmind\EventBusBundle\Exception\LogicException;
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Compiler\CompilerPassInterface
};

final class RegisterListenersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $ids = $container->findTaggedServiceIds('innmind_event_bus.listener');
        $services = [];

        foreach ($ids as $id => $tags) {
            foreach ($tags as $tag => $attributes) {
                if (!isset($attributes['listen_to'])) {
                    throw new LogicException;
                }

                if (!isset($services[$attributes['listen_to']])) {
                    $services[$attributes['listen_to']] = [];
                }

                $services[$attributes['listen_to']][] = $id;
            }
        }

        $container
            ->getDefinition('innmind_event_bus.default')
            ->replaceArgument(1, $services);
    }
}
