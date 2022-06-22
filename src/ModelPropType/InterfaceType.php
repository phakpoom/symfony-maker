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
    private string $name;
    private ?string $fullInterfaceName;
    private string $interfaceName;

    public function __construct(string $name, string $interfaceName = null)
    {
        $this->name = $name;
        $this->fullInterfaceName = $interfaceName;
        $this->interfaceName = NameResolver::resolveOnlyClassName($this->fullInterfaceName);
    }

    public static function getTypeName(): string
    {
        return 'interface (m-1)';
    }

    public function addProperty(ClassType $classType): void
    {
        $classType
            ->addProperty($this->name)
            ->setNullable(true)
            ->setType($this->fullInterfaceName)
            ->setVisibility('protected');
    }

    public function addGetter(ClassType | NetteInterfaceType $classType): void
    {
        $method = $classType
            ->addMethod('get' . ucfirst($this->name))
            ->setVisibility('public')
            ->setReturnNullable(true)
            ->setReturnType($this->fullInterfaceName);

        $classType->isClass() && $method->setBody('return $this->' . $this->name . ';');
    }

    public function addSetter(ClassType | NetteInterfaceType $classType): void
    {
        $method = $classType
            ->addMethod('set' . ucfirst($this->name))
            ->setReturnType('void')
            ->setVisibility('public');
        $classType->isClass() && $method->setBody('$this->' . $this->name . ' = $' . $this->name . ';');
       $method
            ->addParameter($this->name)
            ->setNullable(true)
            ->setType($this->fullInterfaceName);
    }

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
