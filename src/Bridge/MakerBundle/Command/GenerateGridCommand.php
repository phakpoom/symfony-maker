<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\Sylius\GridGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateGridCommand extends AbstractGenerateCommand
{
    private GridGenerator $generator;

    public function __construct(GridGenerator $generator)
    {
        $this->generator = $generator;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('bonn:grid:maker')
            ->setDescription('Generate grid')
            ->addArgument('class', InputArgument::REQUIRED, 'class name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->generator->generate([
            'class' => $class = $input->getArgument('class'),
            'grid_dir' => $this->guessRootModelDir($class) . $this->configs['grid_dir'],
        ]);

        $this->writeCreatedFiles($this->manager, new SymfonyStyle($input, $output));

        $this->manager->flush();

        return 0;
    }
}
