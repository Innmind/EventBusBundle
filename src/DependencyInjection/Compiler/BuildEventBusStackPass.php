<?php
declare(strict_types = 1);

namespace Innmind\EventBusBundle\DependencyInjection\Compiler;

use Innmind\EventBusBundle\Exception\LogicException;
use Innmind\EventBus\EventBusInterface;
use Symfony\Component\DependencyInjection\{
    Compiler\CompilerPassInterface,
    ContainerBuilder,
    Definition,
    Reference
};

final class BuildEventBusStackPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('innmind_event_bus.stack')) {
            throw new LogicException;
        }

        $stack = $container->getParameter('innmind_event_bus.stack');
        $services = $this->searchServices($container);

        $container->setAlias(
            'innmind_event_bus',
            $services[$stack[0]]
        );

        if (count($stack) === 1) {
            return;
        }

        for ($i = 0, $count = count($stack) - 1; $i < $count; $i++) {
            $alias = $stack[$i];
            $next = $stack[$i + 1];

            $this->inject(
                $container->getDefinition($services[$alias]),
                $services[$next]
            );
        }
    }

    private function searchServices(ContainerBuilder $container): array
    {
        $services = $container->findTaggedServiceIds('innmind_event_bus');
        $map = [];

        foreach ($services as $id => $tags) {
            foreach ($tags as $tag => $attributes) {
                if (!isset($attributes['alias'])) {
                    throw new LogicException;
                }

                $map[$attributes['alias']] = $id;
            }
        }

        return $map;
    }

    private function inject(
        Definition $definition,
        string $next
    ) {
        $class = $definition->getClass();
        $refl = new \ReflectionMethod($class, '__construct');

        foreach ($refl->getParameters() as $parameter) {
            if ((string) $parameter->getType() !== EventBusInterface::class) {
                continue;
            }

            $definition->replaceArgument(
                $parameter->getPosition(),
                new Reference($next)
            );

            return;
        }

        throw new LogicException(sprintf(
            'Missing argument type hinted with EventBusInterface for "%s"',
            $class
        ));
    }
}
