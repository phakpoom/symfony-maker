<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;

class IntegerType implements PropTypeInterface
{
    /** @var string */
    private $name;

    /** @var int|null */
    private $defaultValue;

    /** @var bool */
    private $nullable;

    public function __construct(string $name, ?string $defaultValue = null)
    {
        $this->name = $name;
        $this->defaultValue = null !== $defaultValue ? (int) $defaultValue : null;
        $this->nullable = null === $defaultValue;
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return 'int';
    }

    /**
     * {@inheritdoc}
     */
    public function addProperty(ClassType $classType): void
    {
        $prop = $classType
            ->addProperty($this->name)
            ->setVisibility('protected');

        if (null !== $this->defaultValue) {
            $prop->setValue($this->defaultValue);
        }

        $prop->setNullable($this->nullable);
        $prop->setType('int');
    }

    /**
     * {@inheritdoc}
     */
    public function addGetter(ClassType | InterfaceType $classType): void
    {
        $method = $classType
            ->addMethod('get' . ucfirst($this->name))
            ->setVisibility('public');

        $method->setReturnNullable($this->nullable);
        $method->setReturnType('int');
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
            ->setVisibility('public');

        $classType->isClass() && $method->setBody('$this->' . $this->name . ' = $' . $this->name . ';');

        $method
            ->addParameter($this->name)
            ->setNullable($this->nullable)
            ->setType('int');
    }

    /**
     * {@inheritdoc}
     */
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
