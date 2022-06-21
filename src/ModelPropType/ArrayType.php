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
    /** @var string */
    private $name;

    /** @var string|null */
    private $defaultValue;

    public function __construct(string $name, ?string $defaultValue = null)
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
            ->setVisibility('protected');
        if (null !== $this->defaultValue) {
            $prop->setValue($this->defaultValue);
        }

        $prop->setComment('@var array');
    }

    public function addGetter(ClassType | InterfaceType $classType): void
    {
        $method = $classType
            ->addMethod('get' . ucfirst($this->name))
            ->setVisibility('public');
        $method->setReturnNullable(false);
        $method->setReturnType('array');
        $method
            ->setBody('return $this->' . $this->name . ';')
            ->setComment("\n@return array\n");
    }

    public function addSetter(ClassType | InterfaceType $classType): void
    {
        $method = $classType
            ->addMethod('set' . ucfirst($this->name))
            ->setReturnType('void')
            ->setVisibility('public')
            ->setBody('$this->' . $this->name . ' = $' . $this->name . ';');
        $method
            ->addParameter($this->name)
            ->setNullable(false)
            ->setTypeHint('array');
        $method->setComment("\n@param array $$this->name \n");
        $method->addComment("@return void \n");
    }

    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager, array $options): void
    {
        $field = $XMLElement->addChild('field');
        $field->addAttribute('name', $this->name);
        $field->addAttribute('type', 'array');
    }
}
