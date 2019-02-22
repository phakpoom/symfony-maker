<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\Sylius\AbstractSyliusGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateControllerCommand extends AbstractGenerateCommand
{
    /** @var AbstractSyliusGenerator */
    private $generator;

    public function __construct(AbstractSyliusGenerator $generator)
    {
        $this->generator = $generator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('bonn:controller:maker')
            ->setDescription('Controller factory')
            ->addArgument('class', InputArgument::REQUIRED, 'class name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->generator->generate([
            'class' => $class = $input->getArgument('class'),
            'controller_dir' => $this->guessRootModelDir($class) . $this->configs['controller_dir'],
            'resource_dir' => $this->guessRootModelDir($class) . $this->configs['config_dir'],
            'namespace' => $this->getNamespaceFromClass($class, $this->configs['controller_dir']),
        ]);

        $this->writeCreatedFiles($this->manager, new SymfonyStyle($input, $output));

        $this->manager->flush();
    }
}
