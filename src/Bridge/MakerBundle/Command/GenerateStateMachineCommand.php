<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\Sylius\StateMachineGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateStateMachineCommand extends AbstractGenerateCommand
{
    private StateMachineGenerator $generator;

    public function __construct(StateMachineGenerator $generator)
    {
        $this->generator = $generator;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('bonn:sm:maker')
            ->setDescription('Generate StateMachine')
            ->addArgument('class', InputArgument::REQUIRED, 'class name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configDir = $this->guessRootModelDir($input->getArgument('class')) . $this->configs['config_dir'];

        $this->generator->generate([
            'class' => $class = $input->getArgument('class'),
            'state_callback_dir' => $this->guessRootModelDir($class) . $this->configs['state_callback_dir'],
            'namespace' => $this->getNamespaceFromClass($class, $this->configs['state_callback_dir']),
            'all_service_file_path' => '/services.xml',
            'config_dir' => $configDir,
        ]);

        $this->writeCreatedFiles($this->manager, new SymfonyStyle($input, $output));

        $this->manager->flush();

        return 0;
    }
}
