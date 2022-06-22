<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;

/**
 * @commandValueDescription Enter true|false (default false)
 */
class BooleanType implements PropTypeInterface
{
    use PropTypeTrait;

    private string $name;
    private ?bool $defaultValue;

    public function __construct(string $name, ?string $defaultValue = null)
    {
        $this->name = $name;
        $this->defaultValue = 'true' === $defaultValue;
    }

    public static function getTypeName(): string
    {
        return 'boolean';
    }

    public function addProperty(ClassType $classType): void
    {
        $prop = $classType
            ->addProperty($this->name)
            ->setVisibility('protected');
        if (null !== $this->defaultValue) {
            $prop->setValue($this->defaultValue);
        }

        $prop->setType('bool');
    }

    public function addGetter(ClassType | InterfaceType $classType): void
    {
        $method = $classType
            ->addMethod('is' . ucfirst($this->name))
            ->setVisibility('public')
        ;
        $method->setReturnNullable(false);
        $method->setReturnType('bool');
        $classType->isClass() && $method->setBody('return $this->' . $this->name . ';');
    }

    public function addSetter(ClassType | InterfaceType $classType): void
    {
        $method = $classType
            ->addMethod('set' . ucfirst($this->name))
            ->setReturnType('void')
            ->setVisibility('public')
        ;

        $classType->isClass() && $method->setBody('$this->' . $this->name . ' = $' . $this->name . ';');
        $parameter = $method
            ->addParameter($this->name)
            ->setNullable(false)
        ;

        $parameter->setType('bool');
    }

    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager, array $options): void
    {
        $field = $XMLElement->addChild('field');
        $field->addAttribute('name', $this->name);
        $field->addAttribute('type', 'boolean');
    }
}
