<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Nette\PhpGenerator\ClassType;

/**
 * @commandValueSkip
 */
class DateTimeType implements PropTypeInterface
{
    /** @var string */
    private $name;

    public function __construct(string $name, ?string $defaultValue = null)
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
    public function addProperty(ClassType $classType)
    {
        $prop = $classType
            ->addProperty($this->name)
            ->setVisibility('protected');
        $prop->setComment("@var \\DateTime|null");
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
        $method->setReturnNullable(true);
        $method->setComment("\n@return \\DateTime|null\n");
        $method->setReturnType('DateTime');
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
            ->setNullable(true)
        ;
        $method->setComment("\n@param \\DateTime|null $$this->name \n");
        $method->addComment("@return void \n");
        $parameter->setTypeHint('DateTime');
    }

    /**
     * {@inheritdoc}
     */
    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager, array $options)
    {
        $field = $XMLElement->addChild('field');
        $field->addAttribute('name', $this->name);
        $field->addAttribute('type', 'datetime');
        $field->addAttribute('nullable', 'true');
    }
}
