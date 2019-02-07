<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\Sylius\FormGenerator;
use Bonn\Maker\Generator\TwigExtensionGenerator;
use Bonn\Maker\Utils\NameResolver;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateTwigExtensionCommand extends AbstractGenerateCommand
{
    /** @var TwigExtensionGenerator */
    private $generator;

    public function __construct(TwigExtensionGenerator $generator)
    {
        $this->generator = $generator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('bonn:twig_extension:maker')
            ->setDescription('Generate twig extension')
            ->addArgument('name', InputArgument::REQUIRED, 'twig extension name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $dir = $this->askForBundle($helper, $input, $output) . '/';

        $dir = NameResolver::replaceDoubleSlash($dir);

        $configDir = $dir . $this->configs['config_dir'];

        $fullClassName = $this->getFullClassNameFromDir($dir . $this->configs['twig_extension_dir'], $input->getArgument('name'));

        $this->generator->generate([
            'name' => $input->getArgument('name'),
            'twig_extension_dir' => $dir . $this->configs['twig_extension_dir'],
            'namespace' => NameResolver::resolveNamespace($fullClassName),
            'twig_service_file_path' => '/' . $this->configs['service_import_dir'] .'/twigs.xml', // hardedcode ?
            'all_service_file_path' => '/services.xml',
            'config_dir' => $configDir,
        ]);

        $this->writeCreatedFiles($this->manager, new SymfonyStyle($input, $output));

        $this->manager->flush();
    }
}
