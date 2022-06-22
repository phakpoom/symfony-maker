<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\Sylius\SyliusResourceGeneratorInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateSyliusResourceConfigCommand extends AbstractGenerateCommand
{
    private SyliusResourceGeneratorInterface $generator;

    public function __construct(
        SyliusResourceGeneratorInterface $generator
    ) {
        $this->generator = $generator;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('bonn:sylius:maker')
            ->setDescription('Generate sylius resource config')
            ->addArgument('class', InputArgument::REQUIRED, 'name of class')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $resourceDir = $this->guessRootModelDir($input->getArgument('class')) . $this->configs['config_dir'];

        $this->generator->generate([
            'class' => $input->getArgument('class'),
            'resource_dir' => $resourceDir,
        ]);

        $this->writeCreatedFiles($this->manager, new SymfonyStyle($input, $output));

        $this->manager->flush();

        return 0;
    }
}
