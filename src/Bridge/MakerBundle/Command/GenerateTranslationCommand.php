<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\TranslationGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GenerateTranslationCommand extends AbstractGenerateCommand
{
    private TranslationGenerator $generator;

    private ContainerInterface $container;

    public function __construct(TranslationGenerator $generator, ContainerInterface $container)
    {
        $this->generator = $generator;
        $this->container = $container;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('bonn:translation:maker')
            ->setDescription('Generate Translation')
            ->addArgument('full_class_name', InputArgument::REQUIRED, 'Full Class name')
            ->addArgument('bundle', InputArgument::OPTIONAL, 'Bundle');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $translationDir = $this->container->getParameter('kernel.project_dir') . '/' . $this->configs['translations_dir'];
        if ($input->getArgument('bundle')) {
            $translationDir = $this->container->get('kernel')->locateResource($input->getArgument('bundle') . '/translations');
        }

        $this->generator->generate([
            'full_class_name' => $input->getArgument('full_class_name'),
            'translation_dir' => $translationDir,
        ]);

        $this->writeCreatedFiles($this->manager, new SymfonyStyle($input, $output));

        $this->manager->flush();

        return 0;
    }
}
