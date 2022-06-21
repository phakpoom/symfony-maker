<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Utils\NameResolver;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType as NetteInterfaceType;
use Nette\PhpGenerator\PhpNamespace;

/**
 * @commandValueRequired
 * @commandValueDescription Enter full interface class name
 */
class InterfaceType implements PropTypeInterface, NamespaceModifyableInterface
{
    /** @var string */
    private $name;

    /** @var string */
    private $fullInterfaceName;

    /** @var string */
    private $interfaceName;

    /**
     * @param string $interfaceName
     */
    public function __construct(string $name, string $interfaceName = null)
    {
        $this->name = $name;
        $this->fullInterfaceName = $interfaceName;
        $this->interfaceName = NameResolver::resolveOnlyClassName($this->fullInterfaceName);
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return 'interface (m-1)';
    }

    /**
     * {@inheritdoc}
     */
    public function addProperty(ClassType $classType): void
    {
        $prop = $classType
            ->addProperty($this->name)
            ->setVisibility('protected');
        $prop->setComment("@var $this->interfaceName|null");
    }

    /**
     * {@inheritdoc}
     */
    public function addGetter(ClassType | NetteInterfaceType $classType): void
    {
        $method = $classType
            ->addMethod('get' . ucfirst($this->name))
            ->setVisibility('public')
        ;
        $method->setReturnNullable(true);
        $method->setComment("\n@return $this->interfaceName|null\n");
        $method->setReturnType($this->fullInterfaceName);
        $method
            ->setBody('return $this->' . $this->name . ';');
    }

    /**
     * {@inheritdoc}
     */
    public function addSetter(ClassType | NetteInterfaceType $classType): void
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
        $method->setComment("\n@param $this->interfaceName|null $$this->name \n");
        $method->addComment("@return void \n");
        $parameter->setTypeHint($this->fullInterfaceName);
    }

    /**
     * {@inheritdoc}
     */
    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager, array $options): void
    {
        $field = $XMLElement->addChild('many-to-one');
        $field->addAttribute('field', $this->name);
        $field->addAttribute('target-entity', $this->fullInterfaceName);
        $join = $field->addChild('join-column');
        $join->addAttribute('name', NameResolver::camelToUnderScore(str_replace('Interface', '', $this->interfaceName)) . '_id');
        $join->addAttribute('referenced-column-name', 'id');
        $join->addAttribute('on-delete', 'SET NULL');
        $join->addAttribute('nullable', 'true');
    }

    /**
     * {@inheritdoc}
     */
    public function modify(PhpNamespace $classNameSpace, PhpNamespace $interfaceNameSpace): void
    {
        $classNameSpace->addUse($this->fullInterfaceName);
        $interfaceNameSpace->addUse($this->fullInterfaceName);
    }
}
