<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\CommandGenerator;
use Bonn\Maker\Generator\GeneratorInterface;

class GenerateCommandCommand extends CommonServiceCommand
{
    private CommandGenerator $generator;

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
