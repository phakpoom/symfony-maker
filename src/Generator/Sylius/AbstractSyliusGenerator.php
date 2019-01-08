<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator\Sylius;

use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Model\Code;
use Bonn\Maker\Utils\NameResolver;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractSyliusGenerator
{
    /** @var CodeManagerInterface */
    protected $manager;

    public function __construct(CodeManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    abstract protected function _generateWithResolvedOptions(array $options);

    public function configurationOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => null,
        ]);

        $resolver
            ->setRequired('class')
        ;
    }

    public function generate($options = [])
    {
        $optionResolver = new OptionsResolver();
        $this->configurationOptions($optionResolver);

        $options = $optionResolver->resolve($options);

        $this->ensureClassExists($options['class']);

        $this->_generateWithResolvedOptions($options);
    }

    protected function ensureClassExists(string $class)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class %s do not exists', $class));
        }
    }

    protected function appendSyliusResourceConfig(string $configFileName, string $key, string $className): void
    {
        if (!file_exists($configFileName)) {
            return;
        }

        $config = Yaml::parse(file_get_contents($configFileName));

        $c = &$config;
        $c['sylius_resource']['resources'][array_keys($c['sylius_resource']['resources'])[0]]['classes'][$key]
            = $className;

        $this->manager->persist(new Code(Yaml::dump($c, 10), $configFileName));
    }
}
