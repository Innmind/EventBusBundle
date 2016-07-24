<?php
declare(strict_types = 1);

namespace Innmind\EventBusBundle\DependencyInjection;

use Symfony\Component\{
    HttpKernel\DependencyInjection\Extension,
    DependencyInjection\ContainerBuilder,
    DependencyInjection\Loader,
    Config\FileLocator
};

final class InnmindEventBusExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');
        $config = $this->processConfiguration(
            new Configuration,
            $configs
        );

        $container->setParameter(
            'innmind_event_bus.stack',
            $config['stack']
        );
    }
}
