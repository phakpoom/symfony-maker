<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator\Sylius;

use Bonn\Maker\Generator\GeneratorInterface;
use Bonn\Maker\Model\Code;
use Bonn\Maker\Utils\NameResolver;
use Bonn\Maker\Utils\PhpDoctypeCode;
use Nette\PhpGenerator\PhpNamespace;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ControllerGenerator extends AbstractSyliusGenerator implements GeneratorInterface
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
        $resolver
            ->setRequired('class')
            ->setRequired('namespace')
            ->setRequired('resource_dir')
            ->setRequired('controller_dir')
        ;
    }

    protected function generateWithResolvedOptions(array $options)
    {
        $this->ensureClassExists($options['class']);

        $className = NameResolver::resolveOnlyClassName($options['class']);

        $fileLocate = NameResolver::replaceDoubleSlash($options['controller_dir'] . '/' . $className . 'Controller.php');

        $classNamespace = new PhpNamespace($options['namespace']);

        $controllerClass = $classNamespace->addClass($className . 'Controller');

        $classNamespace->addUse('Sylius\\Bundle\\ResourceBundle\\Controller\\ResourceController');
        $controllerClass->setExtends('Sylius\\Bundle\\ResourceBundle\\Controller\\ResourceController');

        $this->manager->persist(new Code(PhpDoctypeCode::render($classNamespace->__toString()), $fileLocate));

        $configFileName = $this->syliusConfigGenerator->resolveConfigFileName($options['class'], $options['resource_dir']);

        $this->appendSyliusResourceConfig($configFileName, 'controller', $classNamespace->getName() . '\\' . $controllerClass->getName());
    }
}
