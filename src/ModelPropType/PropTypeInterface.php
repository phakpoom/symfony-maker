<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Nette\PhpGenerator\ClassType;

interface PropTypeInterface
{
    public static function getTypeName(): string;

    public function addProperty(ClassType $classType);

    public function addGetter(ClassType $classType);

    public function addSetter(ClassType $classType);

    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager);
}
