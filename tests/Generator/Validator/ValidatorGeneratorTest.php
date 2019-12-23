<?php

declare(strict_types=1);

namespace Test\Generator\Validator;

use Bonn\Maker\Generator\TwigExtensionGenerator;
use Bonn\Maker\Generator\ValidatorGenerator;
use Bonn\Maker\Tests\AbstractMakerTestCase;

class ValidatorGeneratorTest extends AbstractMakerTestCase
{
    /** @var ValidatorGenerator */
    private $generator;
    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new ValidatorGenerator();
        $this->generator->setManager($this->manager);
    }

    public function testGenerateBasic()
    {
        $this->generator->generate([
            'name' => 'Dummy',
            'namespace' => __NAMESPACE__,
            'class_dir' => __DIR__,
        ]);

        $this
            ->assertCountFilesWillBeCreated(2)
            ->assertFileWillBeCreated(__DIR__ . '/Dummy.php', file_get_contents(__DIR__ . '/Expected.php'))
            ->assertFileWillBeCreated(__DIR__ . '/DummyValidator.php', file_get_contents(__DIR__ . '/ExpectedConstraint.php'))
        ;
    }

    public function testGenerateWithServiceImport()
    {
        $this->generator->generate([
            'name' => 'Dummy',
            'namespace' => __NAMESPACE__,
            'class_dir' => __DIR__,
            'config_dir' => $configDir = __DIR__ . '/config',
            'entry_service_file_path' => '/services/validators.xml',
            'all_service_file_path' => '/services.xml',
        ]);

        $this
            ->assertCountFilesWillBeCreated(4)
            ->assertFileWillBeCreated(__DIR__ . '/Dummy.php', file_get_contents(__DIR__ . '/Expected.php'))
            ->assertFileWillBeCreated(__DIR__ . '/DummyValidator.php', file_get_contents(__DIR__ . '/ExpectedConstraint.php'))
            ->assertFileWillBeCreated($configDir . '/services/validators.xml', file_get_contents(__DIR__ . '/expectedService.xml'));
    }
}
