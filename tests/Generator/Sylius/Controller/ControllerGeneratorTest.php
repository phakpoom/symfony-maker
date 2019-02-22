<?php

declare(strict_types=1);

namespace Test\Generator\Sylius\Factory;

use Bonn\Maker\Generator\Sylius\ControllerGenerator;
use Bonn\Maker\Generator\Sylius\SyliusResourceYamlConfigGenerator;
use Bonn\Maker\Tests\AbstractMakerTestCase;

class ControllerGeneratorTest extends AbstractMakerTestCase
{
    /**
     * @var ControllerGenerator
     */
    protected $generator;

    protected function setUp()
    {
        parent::setUp();

        $syliusResourceService = new SyliusResourceYamlConfigGenerator();
        $syliusResourceService->setManager($this->manager);
        $this->generator = new ControllerGenerator($syliusResourceService);
        $this->generator->setManager($this->manager);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGenerateNoExistsClass()
    {
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
            'controller_dir' => __DIR__,
            'resource_dir' => __DIR__,
            'namespace' => 'Test\\Generator\\Sylius\\Controller',
        ]);

        $this
            ->assertCountFilesWillBeCreated(2)
            ->assertFileWillBeCreated(__DIR__ . '/ControllerGeneratorTestController.php', file_get_contents(__DIR__ . '/ExpectedController.php'))
            ->assertFileWillBeCreated(__DIR__ . '/app/sylius_resource/controller_generator_test.yml', file_get_contents(__DIR__ . '/expect_sylius_resource.yml'))
        ;
    }
}
