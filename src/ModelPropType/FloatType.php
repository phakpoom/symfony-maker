<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;

class FloatType implements PropTypeInterface
{
    private string $name;
    private ?float $defaultValue;
    private bool $nullable;

    public function __construct(string $name, ?string $defaultValue = null)
    {
        $this->name = $name;
        $this->defaultValue = null !== $defaultValue ? (float) $defaultValue : null;
        $this->nullable = null === $defaultValue;
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return 'float';
    }

    /**
     * {@inheritdoc}
     */
    public function addProperty(ClassType $classType): void
    {
        $prop = $classType
            ->addProperty($this->name)
            ->setNullable($this->nullable)
            ->setType('float')
            ->setVisibility('protected');
        if (null !== $this->defaultValue) {
            $prop->setValue($this->defaultValue);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addGetter(ClassType | InterfaceType $classType): void
    {
        $method = $classType
            ->addMethod('get' . ucfirst($this->name))
            ->setVisibility('public')
        ;

        $method->setReturnNullable($this->nullable);
        $method->setReturnType('float');
        $classType->isClass() && $method->setBody('return $this->' . $this->name . ';');
    }

    /**
     * {@inheritdoc}
     */
    public function addSetter(ClassType | InterfaceType $classType): void
    {
        $method = $classType
            ->addMethod('set' . ucfirst($this->name))
            ->setReturnType('void')
            ->setVisibility('public')
        ;

        $classType->isClass() && $method->setBody('$this->' . $this->name . ' = $' . $this->name . ';');

        $method
            ->addParameter($this->name)
            ->setNullable($this->nullable)
            ->setType('float');
    }

    /**
     * {@inheritdoc}
     */
    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager, array $options): void
    {
        $field = $XMLElement->addChild('field');
        $field->addAttribute('name', $this->name);
        $field->addAttribute('type', 'float');
        if ($this->nullable) {
            $field->addAttribute('nullable', 'true');
        }
    }
}
