<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\DependencyInjection\Compiler;

use Bonn\Maker\Generator\AbstractGenerator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class GeneratorServicePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('bonn_maker.generator') as $id => $tags) {
            $definition = $container->getDefinition($id);
            if (!is_a($definition->getClass(), AbstractGenerator::class, true)) {
                continue;
            }

            $definition->addMethodCall('setManager', [new Reference('bonn_maker.manager.code_manager')]);
        }
    }
}
