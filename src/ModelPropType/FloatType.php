<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Nette\PhpGenerator\ClassType;

class FloatType implements PropTypeInterface
{
    /** @var string */
    private $name;

    /** @var float|null */
    private $defaultValue;

    /** @var bool */
    private $nullable;

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
    public function addProperty(ClassType $classType)
    {
        $prop = $classType
            ->addProperty($this->name)
            ->setVisibility('protected');
        if (null !== $this->defaultValue) {
            $prop->setValue($this->defaultValue);
        }

        if (!$this->nullable) {
            $prop->setComment('@var float');

            return;
        }
        $prop->setComment('@var float|null');
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
        $method->setReturnType('float');
        $method
            ->setBody('return $this->' . $this->name . ';');
        if ($this->nullable) {
            $method->setComment("\n@return float|null\n");

            return;
        }
        $method->setComment("\n@return float\n");
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

        $method
            ->addParameter($this->name)
            ->setNullable($this->nullable)
            ->setTypeHint('float');

        if ($this->nullable) {
            $method->setComment("\n@param float|null $$this->name \n");
        } else {
            $method->setComment("\n@param float $$this->name\n");
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
        $field->addAttribute('type', 'float');
        if ($this->nullable) {
            $field->addAttribute('nullable', 'true');
        }
    }
}
