<?php

declare(strict_types=1);

namespace Test\ModelPropType;

use Bonn\Maker\Generator\DoctrineGeneratorInterface;
use Bonn\Maker\Generator\DoctrineXmlMappingGenerator;
use Bonn\Maker\Generator\ModelGenerator;
use Bonn\Maker\Generator\ModelGeneratorInterface;
use Bonn\Maker\Manager\CodeManager;
use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\ModelPropType\PropTypeInterface;
use Bonn\Maker\Writer\EchoWriter;
use PHPUnit\Framework\TestCase;

abstract class AbstractPropTypeTest extends TestCase
{
    /** @var ModelGeneratorInterface */
    protected $generator;

    /** @var CodeManagerInterface */
    protected $manager;

    /** @var DoctrineGeneratorInterface */
    protected $doctrineGenerator;

    protected function setUp()
    {
        parent::setUp();

        $this->manager = new CodeManager(new EchoWriter());
        $this->generator = new ModelGenerator($this->manager);
        $this->doctrineGenerator = new DoctrineXmlMappingGenerator($this->manager);
    }

    protected function generate(/* array|PropTypeInterface */ $propType)
    {
        $this->generator->generate([
            'class' => 'App\\Model\\Mock',
            'props' => is_array($propType) ? $propType : [$propType],
            'model_dir' => $this->codeDir()
        ]);

        $this->doctrineGenerator->generate([
            'class' => 'App\\Model\\Mock',
            'props' => is_array($propType) ? $propType : [$propType],
            'doctrine_mapping_dir' => $this->codeDir()
        ]);
    }

    protected function codeDir()
    {
        return __DIR__;
    }
}
