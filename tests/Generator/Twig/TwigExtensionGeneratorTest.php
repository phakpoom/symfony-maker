<?php

declare(strict_types=1);

namespace Test\Generator\Twig;

use Bonn\Maker\Generator\TwigExtensionGenerator;
use Bonn\Maker\Tests\AbstractMakerTestCase;

class TwigExtensionGeneratorTest extends AbstractMakerTestCase
{
    /** @var TwigExtensionGenerator */
    private $generator;
    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new TwigExtensionGenerator();
        $this->generator->setManager($this->manager);
    }

    public function testGenerateBasic()
    {
        $this->generator->generate([
            'name' => 'Dummy',
            'namespace' => __NAMESPACE__,
            'twig_extension_dir' => __DIR__,
        ]);

        $this
            ->assertCountFilesWillBeCreated(1)
            ->assertFileWillBeCreated(__DIR__ . '/DummyExtension.php', file_get_contents(__DIR__ . '/Expected.php'))
        ;
    }

    public function testGenerateWithServiceImport()
    {
        $this->generator->generate([
            'name' => 'Dummy',
            'namespace' => __NAMESPACE__,
            'twig_extension_dir' => __DIR__,
            'config_dir' => $configDir = __DIR__ . '/config',
            'twig_service_file_path' => '/services/twigs.xml',
            'all_service_file_path' => '/services.xml',
        ]);

        $this
            ->assertCountFilesWillBeCreated(3)
            ->assertFileWillBeCreated(__DIR__ . '/DummyExtension.php', file_get_contents(__DIR__ . '/Expected.php'))
            ->assertFileWillBeCreated($configDir . '/services/twigs.xml', file_get_contents(__DIR__ . '/expectedService.xml'));
    }
}
