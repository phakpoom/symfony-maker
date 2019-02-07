<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator;

use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Model\Code;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractGenerator implements GeneratorInterface
{
    /** @var CodeManagerInterface */
    protected $manager;

    public function setManager(CodeManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param array $options
     * @return mixed
     */
    abstract protected function generateWithResolvedOptions(array $options);

    public function configurationOptions(OptionsResolver $resolver)
    {
        // setting option
    }

    public function generate($options = [])
    {
        $optionResolver = new OptionsResolver();
        $this->configurationOptions($optionResolver);

        $options = $optionResolver->resolve($options);

        $this->generateWithResolvedOptions($options);
    }

    protected function ensureClassExists(string $class)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class %s do not exists', $class));
        }
    }
}
