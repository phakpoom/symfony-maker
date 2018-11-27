<?php

namespace Bonn\Maker\Bridge\MakerBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PropTypePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $types = [];

        foreach ($container->findTaggedServiceIds('bonn_maker.prop_type') as $id => $tags) {
            $types[] = $container->getDefinition($id)->getClass();
        }

        if (empty($types)) {
            return;
        }

        $propTypeConverterDef = $container->getDefinition('bonn_maker.converter.prop_type');
        $propTypeConverterDef->replaceArgument(0, $types);
        $container->setDefinition('bonn_maker.converter.prop_type', $propTypeConverterDef);
    }
}
