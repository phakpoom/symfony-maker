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
     * @return FactoryGeneratorTestInterface
     */
    public function createNew()
    {
        return new $this->className();
    }

    /**
     * @return FactoryGeneratorTestInterface
     */
    public function createWithSomething(): FactoryGeneratorTestInterface
    {
        $object = $this->createNew();

        // do stuff

        return $object;
    }
}
