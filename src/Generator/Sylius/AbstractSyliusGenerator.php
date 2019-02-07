<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator\Sylius;

use Bonn\Maker\Generator\AbstractGenerator;
use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Model\Code;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractSyliusGenerator extends AbstractGenerator
{
    public function configurationOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => null,
        ]);

        $resolver
            ->setRequired('class')
        ;
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
