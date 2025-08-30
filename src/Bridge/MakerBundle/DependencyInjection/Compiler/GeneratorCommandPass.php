<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\DependencyInjection\Compiler;

use Bonn\Maker\Bridge\MakerBundle\Command\AbstractGenerateCommand;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class GeneratorCommandPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('console.command') as $id => $tags) {
            $definition = $container->getDefinition($id);
            if (!is_a($definition->getClass(), AbstractGenerateCommand::class, true)) {
                continue;
            }

            $definition->addMethodCall('setConfigs', [$container->getParameter('bonn_maker.configs')]);
            $definition->addMethodCall('setManager', [new Reference('bonn_maker.manager.code_manager')]);
        }
    }
}
