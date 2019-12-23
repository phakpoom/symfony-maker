<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\GeneratorInterface;
use Bonn\Maker\Generator\ResourceEventListenerGenerator;

class GenerateResourceEventListenerCommand extends CommonServiceCommand
{
    /** @var ResourceEventListenerGenerator */
    private $generator;

    public function __construct(ResourceEventListenerGenerator $generator)
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
        return 'resource_event_listener';
    }

    public function getServiceEntryXmlFileName(): string
    {
        return 'events';
    }
}
