<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Utils\NameResolver;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;

/**
 * @commandValueRequired
 * @commandValueDescription Enter Interface of collection
 */
class CollectionType implements PropTypeInterface, NamespaceModifyableInterface, ConstructResolveInterface
{
    use PropTypeTrait;

    protected string $name;

    protected ?string $fullInterfaceName;

    protected string $interfaceName;

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
        return 'collection (1-m)';
    }

    /**
     * {@inheritdoc}
     */
    public function resolveConstruct(Method $construct): void
    {
        $construct->addBody('$this->' . $this->name . ' = new ArrayCollection();');
    }

    /**
     * {@inheritdoc}
     */
    public function addProperty(ClassType $classType): void
    {
        $prop = $classType
            ->addProperty($this->name)
            ->setVisibility('protected')
            ->setType($this->doctrineCollectionClass)
        ;
        $prop->setComment("@var Collection<int, $this->interfaceName>");
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

        $method->setReturnNullable(false);
        $classType->isInterface() && $method->setComment("\n@return Collection<int, $this->interfaceName>\n");
        $method->setReturnType('Doctrine\\Common\\Collections\\Collection');
        $classType->isClass() && $method->setBody('return $this->' . $this->name . ';');
    }

    /**
     * {@inheritdoc}
     */
    public function addSetter(ClassType | InterfaceType $classType): void
    {
        if ('s' !== substr($this->name, -1, 1)) {
            $this->name = $this->name . 's';
        }

        // has
        $singleName = substr($this->name, 0, strlen($this->name) - 1);
        $method = $classType->addMethod('has' . ucfirst($singleName))->setVisibility('public');
        $classType->isClass() && $method->setBody('return $this->' . $this->name . '->contains($' . $singleName . ');');
        $method->setReturnType('bool')->addParameter($singleName)->setType($this->fullInterfaceName);

        // add
        $method = $classType->addMethod('add' . ucfirst($singleName))->setReturnType('void')->setVisibility('public');
        $classType->isClass() && $method
            ->addBody('if (!$this->has' . ucfirst($singleName) . '($' . $singleName . ')) {')
            ->addBody("\t" . '$this->' . $this->name . '->add($' . $singleName . ');')
            ->addBody("\t" . '//$' . $singleName . '->setXXX($this);')
            ->addBody('}');
        $method->addParameter($singleName)->setType($this->fullInterfaceName);

        // remove
        $method = $classType->addMethod('remove' . ucfirst($singleName))->setReturnType('void')->setVisibility('public');
        $classType->isClass() &&$method
            ->addBody('if ($this->has' . ucfirst($singleName) . '($' . $singleName . ')) {')
            ->addBody("\t" . '$this->' . $this->name . '->removeElement($' . $singleName . ');')
            ->addBody("\t" . '//$' . $singleName . '->setXXX(null);')
            ->addBody('}');
        $method->addParameter($singleName)->setType($this->fullInterfaceName);
    }

    /**
     * {@inheritdoc}
     */
    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager, array $options): void
    {
        $onlyClassName = NameResolver::resolveOnlyClassName($className);
        $field = $XMLElement->addChild('one-to-many');
        $field->addAttribute('field', $this->name);
        $field->addAttribute('target-entity', $this->fullInterfaceName);
        $field->addAttribute('mapped-by', lcfirst($onlyClassName));
        $field->addAttribute('fetch', 'EXTRA_LAZY');
        $field->addAttribute('orphan-removal', 'true');
        $cascade = $field->addChild('cascade');
        $cascade->addChild('cascade-persist');
    }

    /**
     * {@inheritdoc}
     */
    public function modify(PhpNamespace $classNameSpace, PhpNamespace $interfaceNameSpace): void
    {
        $classNameSpace->addUse($this->fullInterfaceName);
        $classNameSpace->addUse('Doctrine\\Common\\Collections\\Collection');
        $classNameSpace->addUse('Doctrine\\Common\\Collections\\ArrayCollection');
        $interfaceNameSpace->addUse($this->fullInterfaceName);
        $interfaceNameSpace->addUse('Doctrine\\Common\\Collections\\Collection');
    }
}
