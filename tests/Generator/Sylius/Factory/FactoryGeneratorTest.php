<?php

declare(strict_types=1);

namespace Test\Generator\Sylius\Factory;

use Bonn\Maker\Generator\Sylius\FactoryGenerator;
use Bonn\Maker\Generator\Sylius\SyliusResourceServiceNameResolver;
use Bonn\Maker\Generator\Sylius\SyliusResourceYamlConfigGenerator;
use Bonn\Maker\Tests\AbstractMakerTestCase;

class FactoryGeneratorTest extends AbstractMakerTestCase
{
    /**
     * @var FactoryGenerator
     */
    protected $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $syliusResourceService = new SyliusResourceYamlConfigGenerator(new SyliusResourceServiceNameResolver());
        $syliusResourceService->setManager($this->manager);
        $this->generator = new FactoryGenerator($syliusResourceService);
        $this->generator->setManager($this->manager);
    }

    public function testGenerateNoExistsClass()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->generator->generate([
            'class' => 'App\\Test',
            'factory_dir' => __DIR__,
            'resource_dir' => __DIR__,
            'namespace' => 'Test\\Generator\\Sylius',
        ]);
    }

    public function testGenerateExistsClass()
    {
        $this->generator->generate([
            'class' => __CLASS__,
            'factory_dir' => __DIR__,
            'resource_dir' => __DIR__,
            'namespace' => 'Test\\Generator\\Sylius\\Factory',
        ]);

        $this
            ->assertCountFilesWillBeCreated(3)
            ->assertFileWillBeCreated(__DIR__ . '/FactoryGeneratorTestFactory.php', file_get_contents(__DIR__ . '/ExpectedFactory.php'))
            ->assertFileWillBeCreated(__DIR__ . '/FactoryGeneratorTestFactoryInterface.php', file_get_contents(__DIR__ . '/ExpectedFactoryInterface.php'))
            ->assertFileWillBeCreated(__DIR__ . '/app/sylius_resource/factory_generator_test.yml', file_get_contents(__DIR__ . '/expect_sylius_resource.yml'))
        ;
    }
}
