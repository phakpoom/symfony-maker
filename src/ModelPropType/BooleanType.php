<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Nette\PhpGenerator\ClassType;

/**
 * @commandValueDescription Enter true|false (default false)
 */
class BooleanType implements PropTypeInterface
{
    /** @var string */
    private $name;

    /** @var bool|null */
    private $defaultValue;

    public function __construct(string $name, ?string $defaultValue = null)
    {
        $this->name = $name;
        $this->defaultValue = 'true' === $defaultValue;
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return 'boolean';
    }

    /**
     * {@inheritdoc}
     */
    public function addProperty(ClassType $classType)
    {
        $prop = $classType
            ->addProperty($this->name)
            ->setVisibility('protected');
        $prop->setValue($this->defaultValue);
        $prop->setComment("\n@var bool\n");
    }

    /**
     * {@inheritdoc}
     */
    public function addGetter(ClassType $classType)
    {
        $method = $classType
            ->addMethod('is' . ucfirst($this->name))
            ->setVisibility('public')
        ;
        $method->setReturnNullable(false);
        $method->setComment("\n@return bool\n");
        $method->setReturnType('bool');
        $method
            ->setBody('return $this->' . $this->name . ';');
    }

    /**
     * {@inheritdoc}
     */
    public function addSetter(ClassType $classType)
    {
        $method = $classType
            ->addMethod('set' . ucfirst($this->name))
            ->setReturnType('void')
            ->setVisibility('public')
            ->setBody('$this->' . $this->name . ' = $' . $this->name . ';');
        $parameter = $method
            ->addParameter($this->name)
            ->setNullable(false)
        ;
        $method->setComment("\n@param bool $$this->name \n");
        $method->addComment("@return void\n");
        $parameter->setTypeHint('bool');
    }

    /**
     * {@inheritdoc}
     */
    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager)
    {
        $field = $XMLElement->addChild('field');
        $field->addAttribute('name', $this->name);
        $field->addAttribute('type', 'boolean');
    }
}
