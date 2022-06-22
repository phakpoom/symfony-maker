<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;

class StringType implements PropTypeInterface
{
    private string $name;
    private ?string $defaultValue;
    private bool $nullable;

    public function __construct(string $name, ?string $defaultValue = null)
    {
        $this->name = $name;
        $this->defaultValue = $defaultValue;
        $this->nullable = null === $defaultValue;
    }

    public static function getTypeName(): string
    {
        return 'string';
    }

    public function addProperty(ClassType $classType): void
    {
        $prop = $classType
            ->addProperty($this->name)
            ->setVisibility('protected')
            ->setNullable($this->nullable)
            ->setType('string')
        ;
        if (null !== $this->defaultValue) {
            $prop->setValue($this->defaultValue);
        }
    }

    public function addGetter(ClassType | InterfaceType $classType): void
    {
        $method = $classType
            ->addMethod('get' . ucfirst($this->name))
            ->setVisibility('public')
        ;

        $method->setReturnNullable($this->nullable);
        $method->setReturnType('string');
        $classType->isClass() && $method->setBody('return $this->' . $this->name . ';');
    }

    public function addSetter(ClassType | InterfaceType $classType): void
    {
        $method = $classType
            ->addMethod('set' . ucfirst($this->name))
            ->setVisibility('public');
        $classType->isClass() && $method->setBody('$this->' . $this->name . ' = $' . $this->name . ';');
        $method->setReturnType('void');

        $method
            ->addParameter($this->name)
            ->setNullable($this->nullable)
            ->setType('string');
    }

    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager, array $options): void
    {
        $field = $XMLElement->addChild('field');
        $field->addAttribute('name', $this->name);
        $field->addAttribute('type', 'string');
        if ($this->nullable) {
            $field->addAttribute('nullable', 'true');
        }
    }
}
