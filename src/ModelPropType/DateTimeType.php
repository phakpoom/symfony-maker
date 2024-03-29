<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;

/**
 * @commandValueSkip
 */
class DateTimeType implements PropTypeInterface
{
    /** @var string */
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return 'datetime';
    }

    /**
     * {@inheritdoc}
     */
    public function addProperty(ClassType $classType): void
    {
        $classType
            ->addProperty($this->name)
            ->setVisibility('protected')
            ->setNullable(true)
            ->setType('DateTime')
        ;
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
        $method->setReturnNullable(true);
        $method->setReturnType('DateTime');
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
        $method->addParameter($this->name)->setNullable(true)->setType('DateTime');
    }

    /**
     * {@inheritdoc}
     */
    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager, array $options): void
    {
        $field = $XMLElement->addChild('field');
        $field->addAttribute('name', $this->name);
        $field->addAttribute('type', 'datetime');
        $field->addAttribute('nullable', 'true');
    }
}
