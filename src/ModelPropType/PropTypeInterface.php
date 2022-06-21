<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\ClassType;

interface PropTypeInterface
{
    public static function getTypeName(): string;

    public function addProperty(ClassType $classType): void;

    public function addGetter(ClassType | InterfaceType $classType): void;

    public function addSetter(ClassType | InterfaceType $classType): void;

    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager, array $options): void;
}
