<?php

declare(strict_types=1);

namespace Test\Generator\Sylius\StateMachine;

use Bonn\Maker\Generator\Sylius\RepositoryGenerator;
use Bonn\Maker\Generator\Sylius\StateMachineGenerator;
use Bonn\Maker\Generator\Sylius\SyliusResourceServiceNameResolver;
use Bonn\Maker\Generator\Sylius\SyliusResourceYamlConfigGenerator;
use Bonn\Maker\Tests\AbstractMakerTestCase;

class StateMachineGeneratorTest extends AbstractMakerTestCase
{
    /** @var StateMachineGenerator */
    protected $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new StateMachineGenerator();
        $this->generator->setManager($this->manager);
    }

    public function testGenerate()
    {
        $this->generator->generate([
            'class' => __CLASS__,
            'state_callback_dir' => __DIR__,
            'all_service_file_path' => '/Resources/service.xml',
            'config_dir' => __DIR__,
            'namespace' => 'Test\\Generator\\Sylius\\StateMachine',
        ]);

        $this
            ->assertCountFilesWillBeCreated(3)
            ->assertFileWillBeCreated(__DIR__ . '/StateMachineGeneratorTestCallback.php', file_get_contents(__DIR__ . '/ExpectedCallback.php'))
            ->assertFileWillBeCreated(__DIR__ . '/Resources/service.xml', file_get_contents(__DIR__ . '/expect_service.xml'))
            ->assertFileWillBeCreated(__DIR__ . '/app/state_machine/state_machine_generator_test.yml', file_get_contents(__DIR__ . '/expect_state_machine.yml'))
        ;
    }

    public function testGenerateWithAlreadyRegistered()
    {
        $this->generator->generate([
            'class' => __CLASS__,
            'state_callback_dir' => __DIR__,
            'all_service_file_path' => '/Resources/service_already_registered.xml',
            'config_dir' => __DIR__,
            'namespace' => 'Test\\Generator\\Sylius\\StateMachine',
        ]);

        $this
            ->assertFileWillBeCreated(__DIR__ . '/Resources/service_already_registered.xml', file_get_contents(__DIR__ . '/expect_service.xml'))
        ;
    }
}
