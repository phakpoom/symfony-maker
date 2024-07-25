<?php

declare(strict_types=1);

namespace Test\Generator\Sylius\Grid;

use Bonn\Maker\Generator\Sylius\ControllerGenerator;
use Bonn\Maker\Generator\Sylius\GridGenerator;
use Bonn\Maker\Generator\Sylius\SyliusResourceServiceNameResolver;
use Bonn\Maker\Generator\Sylius\SyliusResourceYamlConfigGenerator;
use Bonn\Maker\Tests\AbstractMakerTestCase;

class GridGeneratorTest extends AbstractMakerTestCase
{
    /**
     * @var GridGenerator
     */
    protected $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $syliusResourceService = new SyliusResourceYamlConfigGenerator(new SyliusResourceServiceNameResolver());
        $syliusResourceService->setManager($this->manager);
        $this->generator = new GridGenerator($syliusResourceService);
        $this->generator->setManager($this->manager);
    }

    public function testGenerateNoExistsClass()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->generator->generate([
            'class' => 'App\\Test',
            'resource_dir' => __DIR__,
            'grid_dir' => __DIR__,
        ]);
    }

    public function testGenerateExistsClass()
    {
        $this->generator->generate([
            'class' => __CLASS__,
            'grid_dir' => __DIR__,
            'namespace' => 'Test\\Generator\\Sylius\\Grid',
        ]);

        $this
            ->assertCountFilesWillBeCreated(2)
            ->assertFileWillBeCreated(__DIR__ . '/grid_generator_test.yml', file_get_contents(__DIR__ . '/expect_grid.yml'))
            ->assertFileWillBeCreated(__DIR__ . '/main.yml', file_get_contents(__DIR__ . '/expect_grid_entry.yml'))
        ;
    }
}
