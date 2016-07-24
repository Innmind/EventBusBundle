<?php
declare(strict_types = 1);

namespace Innmind\EventBusBundle\DependencyInjection;

use Symfony\Component\Config\Definition\{
    Builder\TreeBuilder,
    ConfigurationInterface
};

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder;
        $root = $treeBuilder->root('innmind_event_bus');

        $root
            ->children()
                ->arrayNode('stack')
                    ->requiresAtLeastOneElement()
                    ->defaultValue(['queue', 'default'])
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
