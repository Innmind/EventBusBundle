<?php
declare(strict_types = 1);

namespace Innmind\EventBusBundle;

use Innmind\EventBusBundle\DependencyInjection\Compiler\BuildEventBusStackPass;
use Symfony\Component\{
    HttpKernel\Bundle\Bundle,
    DependencyInjection\ContainerBuilder
};

final class InnmindEventBusBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new BuildEventBusStackPass);
    }
}
