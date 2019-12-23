<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\CommandGenerator;
use Bonn\Maker\Generator\GeneratorInterface;
use Bonn\Maker\Utils\NameResolver;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateCommandCommand extends CommonServiceCommand
{
    /** @var CommandGenerator */
    private $generator;

    public function __construct(CommandGenerator $generator)
    {
        $this->generator = $generator;

        parent::__construct();
    }

    public function getGenerator(): GeneratorInterface
    {
        return $this->generator;
    }

    public function getServiceTypeName(): string
    {
        return 'command';
    }

    public function getServiceEntryXmlFileName(): string
    {
        return 'commands';
    }
}
