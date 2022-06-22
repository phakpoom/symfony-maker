<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\DoctrineEventListenerGenerator;
use Bonn\Maker\Generator\GeneratorInterface;

class GenerateDoctrineEventListenerCommand extends CommonServiceCommand
{
    private DoctrineEventListenerGenerator $generator;

    public function __construct(DoctrineEventListenerGenerator $generator)
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
        return 'doctrine_event_listener';
    }

    public function getServiceEntryXmlFileName(): string
    {
        return 'events';
    }
}
