<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\Sylius\AbstractSyliusGenerator;
use Bonn\Maker\Generator\Sylius\RepositoryGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateRepositoryCommand extends AbstractGenerateCommand
{
    /** @var AbstractSyliusGenerator */
    private $generator;

    public function __construct(RepositoryGenerator $generator)
    {
        $this->generator = $generator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('bonn:repository:maker')
            ->setDescription('Generate repository')
            ->addArgument('class', InputArgument::REQUIRED, 'class name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->generator->generate([
            'class' => $class = $input->getArgument('class'),
            'repository_dir' => $this->guessRootModelDir($class) . $this->configs['repository_dir'],
            'resource_dir' => $this->guessRootModelDir($class) . $this->configs['config_dir'],
            'namespace' => $this->getNamespaceFromClass($class, $this->configs['repository_dir']),
        ]);

        $this->writeCreatedFiles($this->manager, new SymfonyStyle($input, $output));

        $this->manager->flush();

        return 0;
    }
}
