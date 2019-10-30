<?php

declare(strict_types=1);

namespace Test\Generator\Command;

use Bonn\Maker\Generator\CommandGenerator;
use Bonn\Maker\Generator\TwigExtensionGenerator;
use Bonn\Maker\Tests\AbstractMakerTestCase;

class CommandGeneratorTest extends AbstractMakerTestCase
{
    /** @var CommandGenerator */
    private $generator;
    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new CommandGenerator();
        $this->generator->setManager($this->manager);
    }

    public function testGenerateBasic()
    {
        $this->generator->generate([
            'name' => 'DummyWhatIsThis',
            'namespace' => __NAMESPACE__,
            'command_dir' => __DIR__,
        ]);

        $this
            ->assertCountFilesWillBeCreated(1)
            ->assertFileWillBeCreated(__DIR__ . '/DummyWhatIsThisCommand.php', file_get_contents(__DIR__ . '/Expected.php'))
        ;
    }

    public function testGenerateWithServiceImport()
    {
        $this->generator->generate([
            'name' => 'DummyWhatIsThis',
            'namespace' => __NAMESPACE__,
            'command_dir' => __DIR__,
            'config_dir' => $configDir = __DIR__ . '/config',
            'command_service_file_path' => '/services/commands.xml',
            'all_service_file_path' => '/services.xml',
        ]);

        $this
            ->assertCountFilesWillBeCreated(3)
            ->assertFileWillBeCreated(__DIR__ . '/DummyWhatIsThisCommand.php', file_get_contents(__DIR__ . '/Expected.php'))
            ->assertFileWillBeCreated($configDir . '/services/commands.xml', file_get_contents(__DIR__ . '/expectedService.xml'));
    }
}
