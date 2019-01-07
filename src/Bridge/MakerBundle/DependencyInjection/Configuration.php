<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bonn_maker');

        $rootNode
            ->children()
                ->scalarNode('namespace_prefix')->defaultNull()->end()
                ->scalarNode('bundle_root_dir')->defaultNull()->end()
                ->scalarNode('project_source_dir')->defaultNull()->end()
                ->scalarNode('config_dir')->defaultValue('Resources/config')->end()
                ->scalarNode('factory_dir')->defaultValue('Factory')->end()
                ->scalarNode('repository_dir')->defaultValue('Doctrine/ORM')->end()
                ->scalarNode('doctrine_mapping_dir_name')->defaultValue('Resources/config/doctrine/model')->end()
                ->scalarNode('model_dir_name')->defaultValue('Model')->end()
                ->scalarNode('cache_dir')->defaultNull()->end()
                ->scalarNode('cache_max_keep_versions')->defaultValue(20)->end()
                ->scalarNode('writer_dev')->defaultFalse()->end()
            ->end();

        return $treeBuilder;
    }
}
