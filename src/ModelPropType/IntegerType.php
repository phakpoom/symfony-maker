<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;

class IntegerType implements PropTypeInterface
{
    private string $name;
    private ?float $defaultValue;
    private bool $nullable;

    public function __construct(string $name, ?string $defaultValue = null)
    {
        $this->name = $name;
        $this->defaultValue = null !== $defaultValue ? (int) $defaultValue : null;
        $this->nullable = null === $defaultValue;
    }

    public static function getTypeName(): string
    {
        return 'int';
    }

    public function addProperty(ClassType $classType): void
    {
        $prop = $classType
            ->addProperty($this->name)
            ->setVisibility('protected');

        if (null !== $this->defaultValue) {
            $prop->setValue((int) $this->defaultValue);
        }

        $prop->setNullable($this->nullable);
        $prop->setType('int');
    }

    public function addGetter(ClassType | InterfaceType $classType): void
    {
        $method = $classType
            ->addMethod('get' . ucfirst($this->name))
            ->setVisibility('public');

        $method->setReturnNullable($this->nullable);
        $method->setReturnType('int');
        $classType->isClass() && $method->setBody('return $this->' . $this->name . ';');
    }

    public function addSetter(ClassType | InterfaceType $classType): void
    {
        $method = $classType
            ->addMethod('set' . ucfirst($this->name))
            ->setReturnType('void')
            ->setVisibility('public');

        $classType->isClass() && $method->setBody('$this->' . $this->name . ' = $' . $this->name . ';');

        $method
            ->addParameter($this->name)
            ->setNullable($this->nullable)
            ->setType('int');
    }

    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager, array $options): void
    {
        $field = $XMLElement->addChild('field');
        $field->addAttribute('name', $this->name);
        $field->addAttribute('type', 'integer');
        if ($this->nullable) {
            $field->addAttribute('nullable', 'true');
        }
    }
}
