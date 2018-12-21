<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator\Sylius;

use Bonn\Maker\Manager\CodeManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractSyliusGenerator implements SyliusGeneratorInterface
{
    /**
     * @var CodeManagerInterface
     */
    protected $manager;

    public function __construct(CodeManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param array $options
     */
    abstract protected function _generateWithResolvedOptions(array $options);

    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => null,
            'resource_name' => null,
            'resource_dir' => null
        ]);

        $resolver
            ->setRequired('class')
            ->setRequired('resource_name')
            ->setRequired('resource_dir')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($options = [])
    {
        $optionResolver = new OptionsResolver();
        $this->configurationOptions($optionResolver);

        $options = $optionResolver->resolve($options);

        $this->ensureClassExists($options['class']);

        $this->_generateWithResolvedOptions($options);
    }

    /**
     * @param string $class
     */
    protected function ensureClassExists(string $class)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf("Class %s do not exists", $class));
        }
    }
}
