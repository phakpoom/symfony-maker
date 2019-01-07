<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator\Sylius;

use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Utils\NameResolver;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
}
