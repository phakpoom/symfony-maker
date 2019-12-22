<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\ValidatorGenerator;
use Bonn\Maker\Utils\NameResolver;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateValidatorCommand extends AbstractGenerateCommand
{
    /** @var ValidatorGenerator */
    private $generator;

    public function __construct(ValidatorGenerator $generator)
    {
        $this->generator = $generator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('bonn:validator:maker')
            ->setDescription('Generate validator')
            ->addArgument('name', InputArgument::REQUIRED, 'validator name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $dir = $this->askForBundle($helper, $input, $output) . '/';

        $configDir = $dir . $this->configs['config_dir'];

        $fullClassName = $this->getFullClassNameFromDir($dir . $this->configs['validator_dir'], $input->getArgument('name'));

        $this->generator->generate([
            'name' => $input->getArgument('name'),
            'validator_dir' => $dir . $this->configs['validator_dir'],
            'namespace' => NameResolver::resolveNamespace($fullClassName),
            'validator_service_file_path' => '/' . $this->configs['service_import_dir'] . '/validators.xml',
            'all_service_file_path' => '/services.xml',
            'config_dir' => $configDir,
        ]);

        $this->writeCreatedFiles($this->manager, new SymfonyStyle($input, $output));

        $this->manager->flush();
    }
}
