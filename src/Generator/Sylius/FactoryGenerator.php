<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator\Sylius;

use Bonn\Maker\Model\Code;
use Bonn\Maker\Utils\NameResolver;
use Bonn\Maker\Utils\PhpDoctypeCode;
use Nette\PhpGenerator\PhpNamespace;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactoryGenerator extends AbstractSyliusGenerator
{
    /** @var SyliusResourceGeneratorInterface */
    private $syliusConfigGenerator;

    public function __construct(SyliusResourceGeneratorInterface $syliusConfigGenerator)
    {
        $this->syliusConfigGenerator = $syliusConfigGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
        parent::configurationOptions($resolver);

        $resolver
            ->setRequired('class')
            ->setRequired('namespace')
            ->setRequired('factory_dir')
            ->setRequired('resource_dir')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateWithResolvedOptions(array $options)
    {
        $this->ensureClassExists($options['class']);

        $className = NameResolver::resolveOnlyClassName($options['class']);

        $factoryFileLocate = NameResolver::replaceDoubleSlash($options['factory_dir'] . '/' . $className . 'Factory.php');
        $factoryInterfaceFileLocate = NameResolver::replaceDoubleSlash($options['factory_dir'] . '/' . $className . 'FactoryInterface.php');

        $classNamespace = new PhpNamespace($options['namespace']);
        $interfaceNamespace = new PhpNamespace($options['namespace']);

        $factoryClass = $classNamespace->addClass($className . 'Factory');
        $classNamespace->addUse('Sylius\\Component\\Resource\\Factory\\FactoryInterface');
        $classNamespace->addUse($options['class'] . 'Interface');
        $factoryClass->addProperty('className')->setComment("\n @var string \n");
        $factoryClass->addMethod('__construct')
            ->setVisibility('public')->setBody('$this->className = $className;')
            ->addParameter('className')->setType('string');

        $interfaceName = NameResolver::resolveOnlyClassName($options['class']) . 'Interface';
        $factoryClass->addMethod('createNew')
            ->setVisibility('public')->setBody('return new $this->className();')
            ->setComment("\n @return " . $interfaceName . " \n");

        $factoryClass->addMethod('createWithSomething')
            ->setVisibility('public')->setBody(
                '$object = $this->createNew(); ' . "\n\n" .
                '// do stuff' . "\n\n" .
                'return $object;'
            )
            ->setReturnType($options['class'] . 'Interface')
            ->setComment("\n @return " . $interfaceName . " \n");

        $factoryInterfaceClass = $interfaceNamespace->addInterface($className . 'FactoryInterface');
        $interfaceNamespace->addUse('Sylius\\Component\\Resource\\Factory\\FactoryInterface');
        $factoryInterfaceClass->addExtend('Sylius\\Component\\Resource\\Factory\\FactoryInterface');

        $factoryClass->addImplement($interfaceNamespace->getName() . '\\' . $factoryInterfaceClass->getName());

        $this->manager->persist(new Code(PhpDoctypeCode::render($classNamespace->__toString()), $factoryFileLocate));
        $this->manager->persist(new Code(PhpDoctypeCode::render($interfaceNamespace->__toString()), $factoryInterfaceFileLocate));

        $configFileName = $this->syliusConfigGenerator->resolveConfigFileName($options['class'], $options['resource_dir']);

        $this->appendSyliusResourceConfig($configFileName, 'factory', $classNamespace->getName() . '\\' . current($classNamespace->getClasses())->getName());
    }
}
