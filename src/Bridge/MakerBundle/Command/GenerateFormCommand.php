<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\Sylius\FormGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateFormCommand extends AbstractGenerateCommand
{
    private FormGenerator $generator;

    public function __construct(FormGenerator $generator)
    {
        $this->generator = $generator;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('bonn:form:maker')
            ->setDescription('Generate form')
            ->addArgument('class', InputArgument::REQUIRED, 'class name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configDir = $this->guessRootModelDir($input->getArgument('class')) . $this->configs['config_dir'];

        $this->generator->generate([
            'class' => $class = $input->getArgument('class'),
            'form_dir' => $this->guessRootModelDir($class) . $this->configs['form_type_dir'],
            'namespace' => $this->getNamespaceFromClass($class, $this->configs['form_type_dir']),
            'form_service_file_path' => '/' . $this->configs['service_import_dir'] . '/forms.xml',
            'all_service_file_path' => '/services.xml',
            'config_dir' => $configDir,
        ]);

        $this->writeCreatedFiles($this->manager, new SymfonyStyle($input, $output));

        $this->manager->flush();

        return 0;
    }
}
