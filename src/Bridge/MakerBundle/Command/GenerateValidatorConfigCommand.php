<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\ValidatorConfigGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateValidatorConfigCommand extends AbstractGenerateCommand
{
    private ValidatorConfigGenerator $generator;

    public function __construct(ValidatorConfigGenerator $generator)
    {
        $this->generator = $generator;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('bonn:validator_config:maker')
            ->setDescription('Generate Validator Config')
            ->addArgument('full_class_name', InputArgument::REQUIRED, 'Full Class name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->generator->generate([
            'full_class_name' => $input->getArgument('full_class_name'),
            'validator_config_dir' => $this->configs['root_config_dir'] . '/' . $this->configs['validator_config_dir'],
        ]);

        $this->writeCreatedFiles($this->manager, new SymfonyStyle($input, $output));

        $this->manager->flush();

        return 0;
    }
}
