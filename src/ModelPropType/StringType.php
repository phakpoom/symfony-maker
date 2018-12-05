<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Nette\PhpGenerator\ClassType;

class StringType implements PropTypeInterface
{
    /** @var string */
    private $name;

    /** @var string|null */
    private $defaultValue;

    /** @var bool */
    private $nullable;

    public function __construct(string $name, ?string $defaultValue = null)
    {
        $this->name = $name;
        $this->defaultValue = $defaultValue;
        $this->nullable = null === $defaultValue;
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return 'string';
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
        if (!$this->nullable) {
            $prop->setComment("\n@var string\n");

            return;
        }
        $prop->setComment("\n@var string|null\n");
    }

    /**
     * {@inheritdoc}
     */
    public function addGetter(ClassType $classType)
    {
        $method = $classType
            ->addMethod('get' . ucfirst($this->name))
            ->setVisibility('public')
        ;

        $method->setReturnNullable($this->nullable);
        $method->setReturnType('string');
        $method
            ->setBody('return $this->' . $this->name . ';');
        if ($this->nullable) {
            $method->setComment("\n@return string|null\n");

            return;
        }
        $method->setComment("\n@return string\n");
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
        $method->setReturnType('void');

        $method
            ->addParameter($this->name)
            ->setNullable($this->nullable)
            ->setTypeHint('string');

        if ($this->nullable) {
            $method->setComment("\n@param string|null $$this->name \n");
        } else {
            $method->setComment("\n@param string $$this->name\n");
        }

        $method->addComment("@return void \n");
    }

    /**
     * {@inheritdoc}
     */
    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager, array $options)
    {
        $field = $XMLElement->addChild('field');
        $field->addAttribute('name', $this->name);
        $field->addAttribute('type', 'string');
        if ($this->nullable) {
            $field->addAttribute('nullable', 'true');
        }
    }
}
