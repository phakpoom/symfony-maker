<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension as BaseExtension;

class Extension extends BaseExtension
{
    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'bonn_maker';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // auto detect bundle folder

        if (null === $config['bundle_root_dir']) {
            $finder = new Finder();
            $sourceDir = $container->getParameter('kernel.project_dir') . '/src/';
            /** @var \SplFileInfo $folder */
            foreach ($finder->directories()->in($sourceDir)->depth('< 3')->name('Bundle') as $folder) {
                $config['bundle_root_dir'] = $folder->getRealPath();
            }

            if (null === $config['bundle_root_dir']) {
                $config['bundle_root_dir'] = $sourceDir;
            }
        }

        $config['cache_dir'] = $config['cache_dir'] ?: $container->getParameter('kernel.cache_dir') . '/bonn-symfony-maker/';
        $config['project_source_dir'] = $config['project_source_dir'] ?: $container->getParameter('kernel.project_dir') . '/src/';

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('bonn_maker.configs', $config);

        $cacheDef = $container->getDefinition('bonn_maker.cache.generated_model');
        $cacheDef->replaceArgument(0, [
            'max_keep_versions' => $config['cache_max_keep_versions'],
            'cache_dir' => $config['cache_dir'],
        ]);
        $container->setDefinition('bonn_maker.cache.generated_model', $cacheDef);

        if (true === $config['writer_dev']) {
            $cacheDef = $container->getDefinition('bonn_maker.manager.code_manager');
            $cacheDef->replaceArgument(0, new Reference('bonn_maker.writer.echo'));
            $container->setDefinition('bonn_maker.manager.code_manager', $cacheDef);
        }
    }
}
