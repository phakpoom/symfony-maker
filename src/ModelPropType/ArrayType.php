<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;

/**
 * @commandValueSkip
 */
class ArrayType implements PropTypeInterface
{
    private string $name;

    private ?array $defaultValue;

    public function __construct(string $name, ?array $defaultValue = null)
    {
        $this->name = $name;
        $this->defaultValue = [];
    }

    public static function getTypeName(): string
    {
        return 'array';
    }

    public function addProperty(ClassType $classType): void
    {
        $prop = $classType
            ->addProperty($this->name)
            ->setVisibility('protected')
            ->setType('array');
        if (null !== $this->defaultValue) {
            $prop->setValue($this->defaultValue);
        }
    }

    public function addGetter(ClassType|InterfaceType $classType): void
    {
        $method = $classType
            ->addMethod('get' . ucfirst($this->name))
            ->setVisibility('public');
        $method->setReturnNullable(false)
            ->setReturnType('array')
            ->setBody('return (array) $this->' . $this->name . ';');
    }

    public function addSetter(ClassType|InterfaceType $classType): void
    {
        $method = $classType
            ->addMethod('set' . ucfirst($this->name))
            ->setReturnType('void')
            ->setVisibility('public');
        $classType->isClass() && $method->setBody('$this->' . $this->name . ' = $' . $this->name . ';');
        $method
            ->addParameter($this->name)
            ->setNullable(false)
            ->setType('array');
    }

    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager, array $options): void
    {
        $field = $XMLElement->addChild('field');
        $field->addAttribute('name', $this->name);
        $field->addAttribute('type', 'array');
    }
}
