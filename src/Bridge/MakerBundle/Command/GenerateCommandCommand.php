<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\CommandGenerator;
use Bonn\Maker\Utils\NameResolver;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateCommandCommand extends AbstractGenerateCommand
{
    /** @var CommandGenerator */
    private $generator;

    public function __construct(CommandGenerator $generator)
    {
        $this->generator = $generator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('bonn:command:maker')
            ->setDescription('Generate command')
            ->addArgument('name', InputArgument::REQUIRED, 'command name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $dir = $this->askForBundle($helper, $input, $output). '/';;

        $configDir = $dir . $this->configs['config_dir'];

        $fullClassName = $this->getFullClassNameFromDir($dir . $this->configs['command_dir'], $input->getArgument('name'));

        $this->generator->generate([
            'name' => $input->getArgument('name'),
            'command_dir' => $dir . $this->configs['command_dir'],
            'namespace' => NameResolver::resolveNamespace($fullClassName),
            'command_service_file_path' => '/' . $this->configs['service_import_dir'] . '/commands.xml',
            'all_service_file_path' => '/services.xml',
            'config_dir' => $configDir,
        ]);

        $this->writeCreatedFiles($this->manager, new SymfonyStyle($input, $output));

        $this->manager->flush();
    }
}
