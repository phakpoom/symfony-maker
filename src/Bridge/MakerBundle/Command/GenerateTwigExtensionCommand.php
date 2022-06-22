<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\GeneratorInterface;
use Bonn\Maker\Generator\TwigExtensionGenerator;

class GenerateTwigExtensionCommand extends CommonServiceCommand
{
    private TwigExtensionGenerator $generator;

    public function __construct(TwigExtensionGenerator $generator)
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
        return 'twig_extension';
    }

    public function getServiceEntryXmlFileName(): string
    {
        return 'twigs';
    }
}
