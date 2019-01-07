<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\Sylius\FactoryGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateFactoryCommand extends AbstractGenerateCommand
{
    /** @var FactoryGenerator */
    private $generator;

    public function __construct(FactoryGenerator $generator) {
        $this->generator = $generator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('bonn:factory:maker')
            ->setDescription('Generate factory')
            ->addArgument('class', InputArgument::REQUIRED, 'class name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->generator->generate([
            'class' => $class = $input->getArgument('class'),
            'factory_dir' => $this->guessRootModelDir($class) . $this->configs['factory_dir'],
            'resource_dir' => $this->guessRootModelDir($class) . $this->configs['config_dir'],
            'namespace' => $this->getNamespaceFromClass($class, $this->configs['factory_dir'])
        ]);

        $this->writeCreatedFiles($this->manager, new SymfonyStyle($input, $output));

        $this->manager->flush();
    }
}
