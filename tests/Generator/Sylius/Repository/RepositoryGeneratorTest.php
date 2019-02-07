<?php

declare(strict_types=1);

namespace Test\Generator\Sylius\Factory;

use Bonn\Maker\Generator\Sylius\RepositoryGenerator;
use Bonn\Maker\Generator\Sylius\SyliusResourceYamlConfigGenerator;
use Bonn\Maker\Tests\AbstractMakerTestCase;

class RepositoryGeneratorTest extends AbstractMakerTestCase
{
    /**
     * @var RepositoryGenerator
     */
    protected $generator;

    protected function setUp()
    {
        parent::setUp();

        $syliusResourceService = new SyliusResourceYamlConfigGenerator();
        $syliusResourceService->setManager($this->manager);
        $this->generator = new RepositoryGenerator($syliusResourceService);
        $this->generator->setManager($this->manager);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGenerateNoExistsClass()
    {
        $this->generator->generate([
            'class' => 'App\\Test',
            'repository_dir' => __DIR__,
            'resource_dir' => __DIR__,
            'namespace' => 'Test\\Generator\\Sylius',
        ]);
    }

    public function testGenerateExistsClass()
    {
        $this->generator->generate([
            'class' => __CLASS__,
            'repository_dir' => __DIR__,
            'resource_dir' => __DIR__,
            'namespace' => 'Test\\Generator\\Sylius\\Repository',
        ]);

        $this
            ->assertCountFilesWillBeCreated(3)
            ->assertFileWillBeCreated(__DIR__ . '/RepositoryGeneratorTestRepository.php', file_get_contents(__DIR__ . '/ExpectedRepository.php'))
            ->assertFileWillBeCreated(__DIR__ . '/RepositoryGeneratorTestRepositoryInterface.php', file_get_contents(__DIR__ . '/ExpectedRepositoryInterface.php'))
            ->assertFileWillBeCreated(__DIR__ . '/app/sylius_resource/repository_generator_test.yml', file_get_contents(__DIR__ . '/expect_sylius_resource.yml'))
        ;
    }
}
