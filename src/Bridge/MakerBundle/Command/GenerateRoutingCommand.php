<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\Sylius\RoutingGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateRoutingCommand extends AbstractGenerateCommand
{
    private RoutingGenerator $generator;

    public function __construct(RoutingGenerator $generator)
    {
        $this->generator = $generator;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('bonn:routing:maker')
            ->setDescription('Generate routing')
            ->addArgument('class', InputArgument::REQUIRED, 'class name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->generator->generate([
            'class' => $class = $input->getArgument('class'),
            'routing_dir' => $this->guessRootModelDir($class) . $this->configs['routing_dir'],
        ]);

        $this->writeCreatedFiles($this->manager, new SymfonyStyle($input, $output));

        $this->manager->flush();

        return 0;
    }
}
