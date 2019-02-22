<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator\Sylius;

use Bonn\Maker\Utils\NameResolver;

class SyliusResourceServiceNameResolver implements SyliusResourceServiceNameResolverInterface
{
    /** @var string|null */
    protected $prefix;

    public function __construct(?string $prefix = null)
    {
        $this->prefix = $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function getModelParameter(string $className): string
    {
        return $this->resolve($className, 'model') . '.class';
    }

    /**
     * {@inheritdoc}
     */
    public function getController(string $className): string
    {
        return $this->resolve($className, 'controller');
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(string $className): string
    {
        return $this->resolve($className, 'repository');
    }

    /**
     * {@inheritdoc}
     */
    public function getFactory(string $className): string
    {
        return $this->resolve($className, 'factory');
    }

    /**
     * {@inheritdoc}
     */
    public function getManager(string $className): string
    {
        return $this->resolve($className, 'manager');
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefix(string $className): string
    {
        $prefix = $this->prefix;
        if (empty($this->prefix)) {
            $prefix =  NameResolver::camelToUnderScore(explode('\\', $className)[0]);
        }

        return $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceName(string $className): string
    {
        return NameResolver::camelToUnderScore(NameResolver::resolveOnlyClassName($className));
    }

    /**
     * @param string $key
     * @return string
     */
    private function resolve(string $className, string $key): string
    {
        return sprintf("{$this->getPrefix($className)}.%s.{$this->getResourceName($className)}", $key);
    }
}
