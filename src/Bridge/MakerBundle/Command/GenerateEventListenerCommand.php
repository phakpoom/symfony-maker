<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\EventListenerGenerator;
use Bonn\Maker\Generator\GeneratorInterface;

class GenerateEventListenerCommand extends CommonServiceCommand
{
    private EventListenerGenerator $generator;

    public function __construct(EventListenerGenerator $generator)
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
        return 'event_listener';
    }

    public function getServiceEntryXmlFileName(): string
    {
        return 'events';
    }
}
