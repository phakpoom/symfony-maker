<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator\Sylius;

use Bonn\Maker\Model\Code;
use Bonn\Maker\Utils\NameResolver;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

class SyliusResourceYamlConfigGenerator extends AbstractSyliusGenerator implements SyliusResourceGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
        parent::configurationOptions($resolver);

        $resolver->setDefaults([
            'resource_prefix_name' => null,
            'resource_dir' => null,
        ]);

        $resolver
            ->setRequired('class')
            ->setRequired('resource_prefix_name')
            ->setRequired('resource_dir')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateWithResolvedOptions(array $options)
    {
        $this->ensureClassExists($options['class']);

        $className = NameResolver::resolveOnlyClassName($options['class']);
        $reflection = new \ReflectionClass($options['class']);
        $resourceName = $options['resource_prefix_name'] . '.' . NameResolver::camelToUnderScore($className);
        $arr = [];
        $arr['sylius_resource']['resources'][$resourceName] = [];
        $resourceArr = &$arr['sylius_resource']['resources'][$resourceName];
        $resourceArr['classes']['model'] = $options['class'];
        $resourceArr['classes']['interface'] = $options['class'] . 'Interface';

        if (in_array('Sylius\\Component\\Resource\\Model\\TranslatableInterface', $reflection->getInterfaceNames())) {
            $resourceArr['translation']['classes']['model'] = $options['class'] . 'Translation';
            $resourceArr['translation']['classes']['interface'] = $options['class'] . 'TranslationInterface';
        }

        $this->manager->persist(new Code(Yaml::dump($arr, 10), $this->resolveConfigFileName($options['class'], $options['resource_dir'])));
    }

    /**
     * {@inheritdoc}
     */
    public function resolveConfigFileName(string $className, string $dir): string
    {
        $className = NameResolver::resolveOnlyClassName($className);

        return NameResolver::replaceDoubleSlash($dir . '/app/sylius_resource/' . NameResolver::camelToUnderScore($className) . '.yml');
    }
}
