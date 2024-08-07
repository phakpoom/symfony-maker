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
        $treeBuilder = new TreeBuilder('bonn_maker');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('namespace_prefix')->defaultNull()->end()
                ->scalarNode('sylius_resource_name_prefix')->defaultNull()->end()
                ->scalarNode('bundle_root_dir')->defaultNull()->end()
                ->scalarNode('project_source_dir')->defaultNull()->end()
                ->scalarNode('config_dir')->defaultValue('Resources/config')->end()
                ->scalarNode('service_import_dir')->defaultValue('services')->end()
                ->scalarNode('factory_dir')->defaultValue('Factory')->end()
                ->scalarNode('form_type_dir')->defaultValue('Form/Type')->end()
                ->scalarNode('event_listener_dir')->defaultValue('EventListener')->end()
                ->scalarNode('resource_event_listener_dir')->defaultValue('EventListener')->end()
                ->scalarNode('doctrine_event_listener_dir')->defaultValue('EventListener')->end()
                ->scalarNode('twig_extension_dir')->defaultValue('Twig/Extension')->end()
                ->scalarNode('state_callback_dir')->defaultValue('StateCallback')->end()
                ->scalarNode('validator_dir')->defaultValue('Validator/Constraints')->end()
                ->scalarNode('validator_config_dir')->defaultValue('validator')->end()
                ->scalarNode('translations_dir')->defaultValue('translations')->end()
                ->scalarNode('command_dir')->defaultValue('Command')->end()
                ->scalarNode('repository_dir')->defaultValue('Doctrine/ORM')->end()
                ->scalarNode('controller_dir')->defaultValue('Controller')->end()
                ->scalarNode('doctrine_mapping_dir_name')->defaultValue('Resources/config/doctrine/model')->end()
                ->scalarNode('grid_dir')->defaultValue('Resources/config/grid')->end()
                ->scalarNode('routing_dir')->defaultValue('Resources/config/route')->end()
                ->scalarNode('model_dir_name')->defaultValue('Model')->end()
                ->scalarNode('cache_dir')->defaultNull()->end()
                ->scalarNode('cache_max_keep_versions')->defaultValue(20)->end()
                ->scalarNode('writer_dev')->defaultFalse()->end()
            ->end();

        return $treeBuilder;
    }
}
