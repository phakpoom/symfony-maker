<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator\Sylius;

use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Model\Code;
use Bonn\Maker\Utils\NameResolver;
use Bonn\Maker\Utils\PhpDoctypeCode;
use Nette\PhpGenerator\PhpNamespace;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

class FactoryGenerator extends AbstractSyliusGenerator
{
    /** @var SyliusResourceGeneratorInterface  */
    private  $syliusConfigGenerator;

    public function __construct(CodeManagerInterface $manager, SyliusResourceGeneratorInterface $syliusConfigGenerator)
    {
        parent::__construct($manager);

        $this->syliusConfigGenerator = $syliusConfigGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
        parent::configurationOptions($resolver);

        $resolver
            ->setRequired('namespace')
            ->setRequired('factory_dir')
            ->setRequired('resource_dir')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function _generateWithResolvedOptions(array $options)
    {
        $factoryFileLocate = NameResolver::replaceDoubleSlash($options['factory_dir'] . '/' . NameResolver::resolveOnlyClassName($options['class']) . 'Factory.php');
        $factoryInterfaceFileLocate = NameResolver::replaceDoubleSlash($options['factory_dir'] . '/' . NameResolver::resolveOnlyClassName($options['class']) . 'FactoryInterface.php');

        $classNamespace = new PhpNamespace($options['namespace']);
        $interfaceNamespace = new PhpNamespace($options['namespace']);

        $className = NameResolver::resolveOnlyClassName($options['class']);
        $factoryClass = $classNamespace->addClass($className. 'Factory');
        $classNamespace->addUse("Sylius\\Component\\Resource\\Factory\\FactoryInterface");
        $classNamespace->addUse($options['class'] . 'Interface');
        $factoryClass->addProperty('className')->setComment("\n @var string \n");
        $factoryClass->addMethod('__construct')
            ->setVisibility('public')->setBody('$this->className = $className;')
            ->addParameter('className')->setTypeHint('string');
        $factoryClass->addMethod('createNew')
            ->setVisibility('public')->setBody('return new $this->className();')
            ->setComment("\n @var " . NameResolver::resolveOnlyClassName($options['class']) . "Interface \n");
        $factoryInterfaceClass = $interfaceNamespace->addInterface($className. 'FactoryInterface');
        $interfaceNamespace->addUse("Sylius\\Component\\Resource\\Factory\\FactoryInterface");
        $factoryInterfaceClass->addExtend("Sylius\\Component\\Resource\\Factory\\FactoryInterface");

        $factoryClass->addImplement($interfaceNamespace->getName() . '\\' . $factoryInterfaceClass->getName());

        $this->manager->persist(new Code(PhpDoctypeCode::render($classNamespace->__toString()), $factoryFileLocate));
        $this->manager->persist(new Code(PhpDoctypeCode::render($interfaceNamespace->__toString()), $factoryInterfaceFileLocate));

        $configFileName = $this->syliusConfigGenerator->resolveConfigFileName($options['class'], $options['resource_dir']);

        if (!file_exists($configFileName)) {
            return;
        }

        $config = Yaml::parse(file_get_contents($configFileName));

        $c = &$config;
        $c['sylius_resource']['resources'][array_keys($c['sylius_resource']['resources'])[0]]['classes']['factory']
            = $classNamespace->getName() . '\\' .current($classNamespace->getClasses())->getName();

        $this->manager->persist(new Code(Yaml::dump($c, 10), $configFileName));
    }
}
