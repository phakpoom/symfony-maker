<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Generator\GeneratorInterface;
use Bonn\Maker\Generator\ValidatorGenerator;

class GenerateValidatorCommand extends CommonServiceCommand
{
    /** @var ValidatorGenerator */
    private $generator;

    public function __construct(ValidatorGenerator $generator)
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
        return 'validator';
    }

    public function getServiceEntryXmlFileName(): string
    {
        return 'validators';
    }
}
