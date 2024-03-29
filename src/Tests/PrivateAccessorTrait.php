<?php

declare(strict_types=1);

namespace Bonn\Maker\Tests;

trait PrivateAccessorTrait
{
    public function getPrivateProperty($className, $propertyName): \ReflectionProperty
    {
        $reflector = new \ReflectionClass($className);
        $property = $reflector->getProperty($propertyName);
        $property->setAccessible(true);

        return $property;
    }
}
