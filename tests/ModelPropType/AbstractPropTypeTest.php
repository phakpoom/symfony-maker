<?php

declare(strict_types=1);

namespace Test\ModelPropType;

use Bonn\Maker\Generator\DoctrineGeneratorInterface;
use Bonn\Maker\Generator\DoctrineXmlMappingGenerator;
use Bonn\Maker\Generator\ModelGenerator;
use Bonn\Maker\Generator\ModelGeneratorInterface;
use Bonn\Maker\ModelPropType\PropTypeInterface;
use Bonn\Maker\Tests\AbstractMakerTestCase;

abstract class AbstractPropTypeTest extends AbstractMakerTestCase
{
    protected ModelGeneratorInterface $generator;
    protected DoctrineGeneratorInterface $doctrineGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new ModelGenerator();
        $this->generator->setManager($this->manager);

        $this->doctrineGenerator = new DoctrineXmlMappingGenerator();
        $this->doctrineGenerator->setManager($this->manager);
    }

    protected function generate(array|PropTypeInterface $propType): void
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

    protected function codeDir(): string
    {
        return __DIR__;
    }
}
