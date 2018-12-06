<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Utils\NameResolver;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;

/**
 * @commandValueRequired
 * @commandValueDescription Enter Interface of collection
 */
class CollectionType implements PropTypeInterface, NamespaceModifyableInterface, ConstructResolveInterface
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
    public function addProperty(ClassType $classType)
    {
        $prop = $classType
            ->addProperty($this->name)
            ->setVisibility('protected');
        $prop->setComment("\n@var Collection|$this->interfaceName[]\n");
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
        $method->setReturnNullable(false);
        $method->setComment("\n@return Collection|$this->interfaceName[]\n");
        $method->setReturnType('Doctrine\\Common\\Collections\\Collection');
        $method
            ->setBody('return $this->' . $this->name . ';');
    }

    /**
     * {@inheritdoc}
     */
    public function addSetter(ClassType $classType)
    {
        if ('s' !== substr($this->name, -1, 1)) {
            $this->name = $this->name . 's';
        }

        // has
        $singleName = substr($this->name, 0, strlen($this->name) - 1);
        $method = $classType
            ->addMethod('has' . ucfirst($singleName));
        $method
            ->setVisibility('public')
            ->setBody('return $this->' . $this->name . '->contains($' . $singleName . ');');
        $parameter = $method
            ->setReturnType('bool')
            ->addParameter($singleName);
        $method->setComment("\n @param " . $this->interfaceName . " $$singleName");
        $method->addComment("\n@return bool");
        $parameter->setTypeHint($this->fullInterfaceName);

        // add
        $method = $classType
            ->addMethod('add' . ucfirst($singleName));
        $method
            ->setReturnType('void')
            ->setVisibility('public')
            ->addBody('if (!$this->has' . ucfirst($singleName) . '($' . $singleName . ')) {')
            ->addBody("\t" . '$this->' . $this->name . '->add($' . $singleName . ');')
            ->addBody("\t" . '//$' . $singleName . '->setXXX($this);')
            ->addBody('}');
        $parameter = $method
            ->addParameter($singleName);
        $method
            ->setComment("\n @param " . $this->interfaceName . " $$singleName" . "\n");
        $method->addComment("@return void\n");
        $parameter->setTypeHint($this->fullInterfaceName);

        // remove
        $method = $classType
            ->addMethod('remove' . ucfirst($singleName));
        $method
            ->setReturnType('void')
            ->setVisibility('public')
            ->addBody('if ($this->has' . ucfirst($singleName) . '($' . $singleName . ')) {')
            ->addBody("\t" . '$this->' . $this->name . '->removeElement($' . $singleName . ');')
            ->addBody("\t" . '//$' . $singleName . '->setXXX(null);')
            ->addBody('}');
        $parameter = $method
            ->addParameter($singleName);
        $method->setComment("\n @param " . $this->interfaceName . " $$singleName" . "\n");
        $method->addComment("@return void\n");
        $parameter->setTypeHint($this->fullInterfaceName);
    }

    /**
     * {@inheritdoc}
     */
    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager, array $options)
    {
        $onlyClassName = NameResolver::resolveOnlyClassName($className);
        $field = $XMLElement->addChild('one-to-many');
        $field->addAttribute('field', $this->name);
        $field->addAttribute('target-entity', $this->fullInterfaceName);
        $field->addAttribute('mapped-by', lcfirst($onlyClassName));
        $field->addAttribute('fetch', 'EXTRA_LAZY');
        $field->addAttribute('orphan-removal', 'true');
        $cascade = $field->addChild('cascade');
        $cascade->addChild('cascade-all');
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
