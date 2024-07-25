<?php

declare(strict_types=1);

namespace Test\Generator\Sylius\Routing;

use Bonn\Maker\Generator\Sylius\RoutingGenerator;
use Bonn\Maker\Generator\Sylius\SyliusResourceServiceNameResolver;
use Bonn\Maker\Generator\Sylius\SyliusResourceYamlConfigGenerator;
use Bonn\Maker\Tests\AbstractMakerTestCase;

class RoutingGeneratorTest extends AbstractMakerTestCase
{
    /**
     * @var RoutingGenerator
     */
    protected $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $syliusResourceService = new SyliusResourceYamlConfigGenerator(new SyliusResourceServiceNameResolver());
        $syliusResourceService->setManager($this->manager);
        $this->generator = new RoutingGenerator($syliusResourceService);
        $this->generator->setManager($this->manager);
    }

    public function testGenerateExistsClass()
    {
        $this->generator->generate([
            'class' => __CLASS__,
            'routing_dir' => __DIR__,
        ]);

        $this
            ->assertCountFilesWillBeCreated(2)
            ->assertFileWillBeCreated(__DIR__ . '/routing_generator_test.yml', file_get_contents(__DIR__ . '/expect_routing.yml'))
            ->assertFileWillBeCreated(__DIR__ . '/main.yml', file_get_contents(__DIR__ . '/expect_routing_entry.yml'))
        ;
    }
}
