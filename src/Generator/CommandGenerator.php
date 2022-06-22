<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator;

use Bonn\Maker\Model\Code;
use Bonn\Maker\Utils\NameResolver;
use Bonn\Maker\Utils\PhpDoctypeCode;
use Nette\PhpGenerator\PhpNamespace;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'all_service_file_path' => null,
                'entry_service_file_path' => null,
                'config_dir' => null,
            ])
            ->setRequired('name')
            ->setRequired('namespace')
            ->setRequired('class_dir')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateWithResolvedOptions(array $options)
    {
        $fileLocate = NameResolver::replaceDoubleSlash($options['class_dir'] . '/' . $options['name'] . 'Command.php');
        $resourcePrefix = NameResolver::resolveResourcePrefix($options['namespace']);

        $classNamespace = new PhpNamespace($options['namespace']);
        $classNamespace->addUse(Command::class);
        $classNamespace->addUse(InputInterface::class);
        $classNamespace->addUse(OutputInterface::class);

        $class = $classNamespace->addClass($options['name'] . 'Command');

        $class
            ->addProperty('defaultName', $resourcePrefix . ':' . NameResolver::camelToUnderScore($options['name']))
            ->setStatic(true)
            ->setVisibility('protected')
        ;

        $method = $class->addMethod('__construct');
        $method->setBody(<<<PHP
parent::__construct();
PHP
        );

        $method = $class->addMethod('configure')
            ->setVisibility('protected');
        $method->setBody(<<<PHP
// addArgument, addOption
PHP
        );

        $method = $class->addMethod('execute')
            ->setVisibility('protected')->setReturnType('int');
        $method->addParameter('input')->setType(InputInterface::class);
        $method->addParameter('output')->setType(OutputInterface::class);

        $method->addBody("// do stuff\nreturn 0;");

        $class->setExtends(Command::class);

        $this->manager->persist(new Code(PhpDoctypeCode::render($classNamespace->__toString()), $fileLocate));

        if (null === $options['entry_service_file_path'] || null === $options['all_service_file_path'] || null === $options['config_dir']) {
            return;
        }

        // import service form
        $this->addImportEntryToServiceFile($options['config_dir'], $options['entry_service_file_path'], $options['all_service_file_path']);

        $xml = $this->getConfigXmlFile($options['config_dir'], $options['entry_service_file_path']);

        $resourceName = NameResolver::camelToUnderScore($options['name']);
        $serviceContext = $xml->addService(
            sprintf('%s.command.%s', $resourcePrefix, $resourceName),
            $classNamespace->getName() . '\\' . $class->getName(),
            ['autowire' => 'true']
        );

        $serviceContext->addChild('tag', null, [
            'name' => 'console.command',
        ]);

        $this->manager->persist(new Code($xml->__toString(), $options['config_dir'] . $options['entry_service_file_path']));
    }
}
