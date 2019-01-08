<?php

declare(strict_types=1);

namespace Test\Generator\Sylius\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;

class FactoryGeneratorTestFactory implements FactoryGeneratorTestFactoryInterface
{
    /**
     * @var string
     */
    public $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * @var FactoryGeneratorTestInterface
     */
    public function createNew()
    {
        return new $this->className();
    }
}
