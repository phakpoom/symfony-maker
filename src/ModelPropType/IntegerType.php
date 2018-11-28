<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Nette\PhpGenerator\ClassType;

class IntegerType implements PropTypeInterface
{
    /** @var string */
    private $name;

    /** @var int|null */
    private $defaultValue;

    public function __construct(string $name, ?string $defaultValue = null)
    {
        $this->name = $name;
        $this->defaultValue = null !== $defaultValue ? (int) $defaultValue : null;
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
    public function addProperty(ClassType $classType)
    {
        $prop = $classType
            ->addProperty($this->name)
            ->setVisibility('protected');

        $prop->setValue($this->defaultValue);

        if (null !== $this->defaultValue) {
            $prop->setComment("\n@var int\n");

            return;
        }

        $prop->setComment("\n@var int|null\n");
    }

    /**
     * {@inheritdoc}
     */
    public function addGetter(ClassType $classType)
    {
        $method = $classType
            ->addMethod('get' . ucfirst($this->name))
            ->setVisibility('public');
        $isNullable = null === $this->defaultValue;
        $method->setReturnNullable($isNullable);
        $method->setReturnType('int');
        $method
            ->setBody('return $this->' . $this->name . ';');

        if ($isNullable) {
            $method->setComment("\n@return int|null\n");

            return;
        }

        $method->setComment("\n@return int\n");
    }

    /**
     * {@inheritdoc}
     */
    public function addSetter(ClassType $classType)
    {
        $method = $classType
            ->addMethod('set' . ucfirst($this->name))
            ->setVisibility('public')
            ->setBody('$this->' . $this->name . ' = $' . $this->name . ';');
        $isNullable = null === $this->defaultValue;
        $method
            ->addParameter($this->name)
            ->setNullable($isNullable)
            ->setTypeHint('int');
        if ($isNullable) {
            $method->setComment("\n@param int|null $$this->name \n");

            return;
        }
        $method->setComment("\n@param int $$this->name\n");
    }

    /**
     * {@inheritdoc}
     */
    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager)
    {
        $field = $XMLElement->addChild('field');
        $field->addAttribute('name', $this->name);
        $field->addAttribute('type', 'integer');
    }
}
