<?php

declare(strict_types=1);

namespace Test\Generator\Sylius;

use Bonn\Maker\Generator\Sylius\SyliusResourceYamlConfigGenerator;
use Bonn\Maker\Manager\CodeManager;
use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Writer\EchoWriter;
use PHPUnit\Framework\TestCase;

class SyliusResourceYamlConfigGeneratorTest extends TestCase
{
    /**
     * @var CodeManagerInterface
     */
    protected $manager;

    /**
     * @var SyliusResourceYamlConfigGenerator
     */
    protected $generator;

    protected function setUp()
    {
        parent::setUp();

        $this->manager = new CodeManager(new EchoWriter());
        $this->generator = new SyliusResourceYamlConfigGenerator($this->manager);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGenerateNoExistsClass()
    {
        $this->generator->generate([
            'class' => 'App\\Test',
            'resource_name' => 'app',
            'resource_dir' => __DIR__
        ]);
    }

    public function testGenerateExistsClass()
    {
        $this->generator->generate([
            'class' => __CLASS__,
            'resource_name' => 'app',
            'resource_dir' => __DIR__
        ]);

        $allCodes = $this->manager->getCodes();

        $this->assertCount(1, $allCodes);
        var_dump(array_keys($allCodes));exit;
        $this->assertEquals("",
            $allCodes[__DIR__ . '/sylius_resource_yaml_config_generator_test.yml']->getContent());
    }
}
