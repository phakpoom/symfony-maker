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
    /** @var SyliusResourceGeneratorInterface */
    private $generator;

    public function __construct(
        SyliusResourceGeneratorInterface $generator
    ) {
        $this->generator = $generator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('bonn:sylius:maker')
            ->setDescription('Generate sylius resource config')
            ->addArgument('class', InputArgument::REQUIRED, 'name of class')
            ->addArgument('resource_name', InputArgument::REQUIRED, 'resource name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->generator->generate([
            'class' => $input->getArgument('class'),
            'resource_name' => $input->getArgument('resource_name'),
            'resource_dir' => $this->configs['resource_dir'],
        ]);

        $this->writeCreatedFiles($this->manager, new SymfonyStyle($input, $output));

        $this->manager->flush();
    }
}
