<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator\Sylius;

use Bonn\Maker\Model\Code;
use Bonn\Maker\Utils\NameResolver;
use Bonn\Maker\Utils\PhpDoctypeCode;
use Nette\PhpGenerator\PhpNamespace;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RepositoryGenerator extends AbstractSyliusGenerator
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
            ->setRequired('repository_dir')
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
        $repositoryFileLocate = NameResolver::replaceDoubleSlash($options['repository_dir'] . '/' . $className . 'Repository.php');
        $repositoryInterfaceFileLocate = NameResolver::replaceDoubleSlash($options['repository_dir'] . '/' . $className . 'RepositoryInterface.php');

        $classNamespace = new PhpNamespace($options['namespace']);
        $interfaceNamespace = new PhpNamespace($options['namespace']);

        $repositoryClass = $classNamespace->addClass($className . 'Repository');

        $classNamespace->addUse('Sylius\\Bundle\\ResourceBundle\\Doctrine\\ORM\\EntityRepository');
        $repositoryClass->addExtend('Sylius\\Bundle\\ResourceBundle\\Doctrine\\ORM\\EntityRepository');
        $repositoryInterfaceClass = $interfaceNamespace->addInterface($className . 'RepositoryInterface');
        $interfaceNamespace->addUse('Sylius\\Component\\Resource\\Repository\\RepositoryInterface');
        $repositoryInterfaceClass->addExtend('Sylius\\Component\\Resource\\Repository\\RepositoryInterface');

        $repositoryClass->addImplement($interfaceNamespace->getName() . '\\' . $repositoryInterfaceClass->getName());

        $this->manager->persist(new Code(PhpDoctypeCode::render($classNamespace->__toString()), $repositoryFileLocate));
        $this->manager->persist(new Code(PhpDoctypeCode::render($interfaceNamespace->__toString()), $repositoryInterfaceFileLocate));

        $configFileName = $this->syliusConfigGenerator->resolveConfigFileName($options['class'], $options['resource_dir']);

        $this->appendSyliusResourceConfig($configFileName, 'repository', $classNamespace->getName() . '\\' . current($classNamespace->getClasses())->getName());
    }
}
