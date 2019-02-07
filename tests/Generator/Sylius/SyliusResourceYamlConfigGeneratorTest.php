<?php

declare(strict_types=1);

namespace Test\Generator\Sylius;

use Bonn\Maker\Generator\Sylius\SyliusResourceYamlConfigGenerator;
use Bonn\Maker\Tests\AbstractMakerTestCase;

class SyliusResourceYamlConfigGeneratorTest extends AbstractMakerTestCase
{
    /**
     * @var SyliusResourceYamlConfigGenerator
     */
    protected $generator;

    protected function setUp()
    {
        parent::setUp();

        $this->generator = new SyliusResourceYamlConfigGenerator();
        $this->generator->setManager($this->manager);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGenerateNoExistsClass()
    {
        $this->generator->generate([
            'class' => 'App\\Test',
            'resource_prefix_name' => 'app',
            'resource_dir' => __DIR__
        ]);
    }

    public function testGenerateExistsClass()
    {
        $this->generator->generate([
            'class' => __CLASS__,
            'resource_prefix_name' => 'app',
            'resource_dir' => __DIR__
        ]);

        $this
            ->assertCountFilesWillBeCreated(1)
            ->assertFileWillBeCreated(__DIR__ . '/app/sylius_resource/sylius_resource_yaml_config_generator_test.yml',
<<<EOP
sylius_resource:
    resources:
        app.sylius_resource_yaml_config_generator_test:
            classes:
                model: Test\Generator\Sylius\SyliusResourceYamlConfigGeneratorTest
                interface: Test\Generator\Sylius\SyliusResourceYamlConfigGeneratorTestInterface

EOP
)
        ;
    }
}
