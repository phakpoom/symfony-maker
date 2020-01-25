<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\GeneratorInterface;
use Bonn\Maker\Utils\NameResolver;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class CommonServiceCommand extends AbstractGenerateCommand
{
    abstract public function getGenerator(): GeneratorInterface;

    abstract public function getServiceTypeName(): string;

    abstract public function getServiceEntryXmlFileName(): string;

    protected function configure()
    {
        $this
            ->setName('bonn:' . $this->getServiceTypeName() . ':maker')
            ->setDescription('Generate' . $this->getServiceTypeName())
            ->addArgument('name', InputArgument::REQUIRED, 'name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $dir = $this->askForBundle($helper, $input, $output) . '/';

        $configDir = $dir . $this->configs['config_dir'];

        $fullClassName = $this->getFullClassNameFromDir($dir . $this->configs[$this->getServiceTypeName() . '_dir'], $input->getArgument('name'));

        $this->getGenerator()->generate([
            'name' => $input->getArgument('name'),
            'class_dir' => $dir . $this->configs[$this->getServiceTypeName() . '_dir'],
            'namespace' => NameResolver::resolveNamespace($fullClassName),
            'entry_service_file_path' => sprintf('/%s/%s.xml', $this->configs['service_import_dir'], $this->getServiceEntryXmlFileName() ?: ($this->getServiceTypeName() . 's')),
            'all_service_file_path' => '/services.xml',
            'config_dir' => $configDir,
        ]);

        $this->writeCreatedFiles($this->manager, new SymfonyStyle($input, $output));

        $this->manager->flush();
    }
}
