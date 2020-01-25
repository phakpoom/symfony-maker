<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator;

use Bonn\Maker\Model\Code;
use Bonn\Maker\Utils\NameResolver;
use Symfony\Component\Finder\Finder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

class TranslationGenerator extends AbstractGenerator implements GeneratorInterface
{
    public const EXCLUDES = [
        'id',
    ];

    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('translation_dir')
            ->setRequired('full_class_name');
    }

    /**
     * {@inheritdoc}
     */
    protected function generateWithResolvedOptions(array $options)
    {
        $resourcePrefix = NameResolver::resolveResourcePrefix($options['full_class_name']);
        $className = NameResolver::resolveOnlyClassName($options['full_class_name']);
        $resourceName = NameResolver::camelToUnderScore($className);

        $messageFiles = (new Finder())->in($options['translation_dir'])->name('messages.*');

        /** @var \SplFileInfo $file */
        foreach ($messageFiles as $file) {
            $arr = Yaml::parseFile($file->getRealPath());

            $reflectionClass = new \ReflectionClass($options['full_class_name']);

            $props = [];
            foreach ($reflectionClass->getProperties() as $property) {
                $name = NameResolver::camelToUnderScore($property->getName());
                if (in_array($name, self::EXCLUDES)) {
                    continue;
                }

                $props[$name] = $arr[$resourcePrefix][$resourceName]['ui'][$name] ?? '';
            }

            $arr[$resourcePrefix][$resourceName]['ui'] = $props;

            foreach (['grid', 'form', 'other'] as $k) {
                if (isset($arr[$resourcePrefix][$resourceName][$k])) {
                    continue;
                }

                $arr[$resourcePrefix][$resourceName][$k] = [];
            }

            $this->manager->persist(new Code(Yaml::dump($arr, 4), $file->getRealPath()));
        }
    }
}
