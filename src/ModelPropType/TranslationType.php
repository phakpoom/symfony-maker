<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Generator\DoctrineXmlMappingGenerator;
use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Model\Code;
use Bonn\Maker\Utils\NameResolver;
use Bonn\Maker\Utils\PhpDoctypeCode;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Webmozart\Assert\Assert;

/**
 * @commandValueSkip
 */
class TranslationType implements PropTypeInterface, NamespaceModifyableInterface, ManagerAwareInterface
{
    use ManagerAwareTrait;

    /** @var string */
    private $name;

    public function __construct(string $name, string $value = null)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return 'translation';
    }

    /**
     * {@inheritdoc}
     */
    public function addProperty(ClassType $classType)
    {
        // nothing
    }

    /**
     * {@inheritdoc}
     */
    public function addGetter(ClassType $classType)
    {
        $method = $classType
            ->addMethod('get' . ucfirst($this->name))
            ->setVisibility('public');
        $method->setReturnNullable(true);
        $method->setReturnType('string');
        $method
            ->setBody('return $this->getTranslation()->get' . ucfirst($this->name) . '();')
            ->setComment("\n@return string|null\n");
    }

    /**
     * {@inheritdoc}
     */
    public function addSetter(ClassType $classType)
    {
        $method = $classType
            ->addMethod('set' . ucfirst($this->name))
            ->setVisibility('public')
            ->setBody('$this->getTranslation()->set' . ucfirst($this->name) . "($$this->name);");
        $method->setReturnType('void');
        $method
            ->addParameter($this->name)
            ->setNullable(true)
            ->setTypeHint('string');
        $method->setComment("\n@param string|null $$this->name \n");
        $method->addComment("@return void \n");
    }

    /**
     * {@inheritdoc}
     */
    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager, array $options)
    {
        $fullClassName = $className;
        $onlyClassName = NameResolver::resolveOnlyClassName($fullClassName);
        $translationMappingLocaled = $options['doctrine_mapping_dir'] . '/' . $onlyClassName . 'Translation.orm.xml';
        /** @var \SimpleXMLElement $xml */
        if (isset($codeManager->getCodes()[$translationMappingLocaled])) {
            $xml = $codeManager->getCodes()[$translationMappingLocaled]->getExtra()['doctrine_mapping_xml'];
            $mappedSuper = $xml->{'mapped-superclass'};
        } else {
            $xml = DoctrineXmlMappingGenerator::createDoctrineMappingXml();
            $mappedSuper = $xml->addChild('mapped-superclass');
            $id = $mappedSuper->addChild('id');
            $id->addAttribute('name', 'id');
            $id->addAttribute('type', 'integer');
            $id->addChild('generator')->addAttribute('strategy', 'AUTO');
            $mappedSuper->addAttribute('name', $fullClassName . 'Translation');
            $mappedSuper->addAttribute('table', strtolower(explode('\\', $fullClassName)[0]) . '_' . NameResolver::camelToUnderScore($onlyClassName) . '_translation');
        }

        $propType = new StringType($this->name);
        $propType->addDoctrineMapping($fullClassName, $mappedSuper, $codeManager, $options);

        $dom = DoctrineXmlMappingGenerator::createDomWithRoot($xml);
        $codeManager->persist(new Code($dom->saveXML(), $translationMappingLocaled, [
            'doctrine_mapping_xml' => $xml,
        ]));
    }

    /**
     * {@inheritdoc}
     */
    public function modify(PhpNamespace $classNameSpace, PhpNamespace $interfaceNameSpace): void
    {
        /** @var ClassType $classType */
        $classType = current($classNameSpace->getClasses());

        // ClassTranslation.php
        $translationClass = $this->manager->getCodes()[$this->options['model_dir'] . '/' . $this->getTranslationClassName($classNameSpace, $classType) . '.php'] ?? null;
        if (null === $translationClass) {
            $classType->addTrait('Sylius\\Component\\Resource\\Model\\TranslatableTrait', ['__construct as protected initializeTranslationsCollection']);
            $classNameSpace->addUse('Sylius\\Component\\Resource\\Model\\TranslatableTrait');
            $classNameSpace->addUse('Sylius\\Component\\Resource\\Model\\TranslationInterface');
            $classType->addComment('@method ' . $this->getTranslationInterfaceName($classNameSpace, $classType) . ' getTranslation()');
            $classType->getMethod('__construct')->addBody('$this->initializeTranslationsCollection();');
            $classNameSpace->addUse($this->getTranslationClassName($classNameSpace, $classType, true));
            $translationClassNameSpace = new PhpNamespace($classNameSpace->getName());
            $translationClassNameSpace->addUse('Sylius\\Component\\Resource\\Model\\AbstractTranslation');
            $translationClass = $translationClassNameSpace->addClass($this->getTranslationClassName($classNameSpace, $classType));
            $translationClass->addExtend('Sylius\\Component\\Resource\\Model\\AbstractTranslation');
            $propType = new IntegerType('id');
            $propType->addProperty($translationClass);
            $propType->addGetter($translationClass);
        } else {
            $translationClassNameSpace = $translationClass->getExtra()['namespace'];
            Assert::isInstanceOf($translationClassNameSpace, PhpNamespace::class);
            $translationClass = current($translationClassNameSpace->getClasses());
        }

        $propType = new StringType($this->name);
        $propType->addProperty($translationClass);
        $propType->addGetter($translationClass);
        $propType->addSetter($translationClass);

        // move createTranslation to bottom
        // Add createTranslation method
        $classType->removeMethod('createTranslation');
        $method = $classType
            ->addMethod('createTranslation')
            ->setVisibility('protected')
            ->setReturnType('Sylius\\Component\\Resource\\Model\\TranslationInterface');
        $method->setBody('return new ' . $classType->getName() . 'Translation();');
        $method->setComment("\n @return " . $this->getTranslationInterfaceName($classNameSpace, $classType) . "\n");

        foreach ($translationClass->getMethods() as $method) {
            $method->setComment("\n{@inheritdoc}\n");
        }

        $this->manager->persist(new Code(PhpDoctypeCode::render($translationClassNameSpace->__toString()),
            $this->getOption()['model_dir'] . "/{$translationClass->getName()}.php", [
                'namespace' => $translationClassNameSpace,
            ]));

        // ClassTranslationInterface.php
        $translationInterfaceClass = $this->manager->getCodes()[$this->options['model_dir'] . '/' . $this->getTranslationInterfaceName($interfaceNameSpace, $classType) . '.php'] ?? null;
        if (null === $translationInterfaceClass) {
            // Add implement Sylius\Component\Resource\Model\TranslatableInterface
            $interfaceType = current($interfaceNameSpace->getClasses());
            $interfaceType->addExtend('Sylius\\Component\\Resource\\Model\\TranslatableInterface');
            $interfaceNameSpace->addUse('Sylius\\Component\\Resource\\Model\\TranslatableInterface');

            $translationInterfaceNameSpace = new PhpNamespace($interfaceNameSpace->getName());
            $translationInterfaceNameSpace->addUse('Sylius\\Component\\Resource\\Model\\TranslationInterface');
            $translationInterfaceNameSpace->addUse('Sylius\\Component\\Resource\\Model\\ResourceInterface');
            $translationInterfaceClass = $translationInterfaceNameSpace->addInterface($this->getTranslationInterfaceName($interfaceNameSpace, $classType));
            $translationInterfaceClass->addExtend('Sylius\\Component\\Resource\\Model\\TranslationInterface');
            $translationInterfaceClass->addExtend('Sylius\\Component\\Resource\\Model\\ResourceInterface');
        } else {
            $translationInterfaceNameSpace = $translationInterfaceClass->getExtra()['namespace'];
            Assert::isInstanceOf($translationInterfaceNameSpace, PhpNamespace::class);
            $translationInterfaceClass = current($translationInterfaceNameSpace->getClasses());
        }

        $propType = new StringType($this->name);
        $propType->addGetter($translationInterfaceClass);
        $propType->addSetter($translationInterfaceClass);

        foreach ($translationInterfaceClass->getMethods() as $method) {
            $method->setBody(null);
        }

        $this->manager->persist(new Code(PhpDoctypeCode::render($translationInterfaceNameSpace->__toString()),
            $this->getOption()['model_dir'] . "/{$translationInterfaceClass->getName()}.php", [
                'namespace' => $translationInterfaceNameSpace,
            ]));
    }

    /**
     * @param bool $isFull
     *
     * @return string
     */
    private function getTranslationInterfaceName(PhpNamespace $namespace, ClassType $classType, $isFull = false)
    {
        $className = str_replace('Interface', '', $classType->getName());
        $className = $className . 'TranslationInterface';

        return $isFull ? $namespace->getName() . '\\' . $className : $className;
    }

    /**
     * @param bool $isFull
     *
     * @return string
     */
    private function getTranslationClassName(PhpNamespace $namespace, ClassType $classType, $isFull = false)
    {
        $className = $classType->getName() . 'Translation';

        return $isFull ? $namespace->getName() . '\\' . $className : $className;
    }
}
