<?php

declare(strict_types=1);

namespace Test\Generator\EventListener;

use Bonn\Maker\Generator\DoctrineEventListenerGenerator;
use Bonn\Maker\Tests\AbstractMakerTestCase;

class DoctrineEventListenerGeneratorTest extends AbstractMakerTestCase
{
    /** @var DoctrineEventListenerGenerator */
    private $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new DoctrineEventListenerGenerator();
        $this->generator->setManager($this->manager);
    }

    public function testGenerateWithServiceImport()
    {
        $this->generator->generate([
            'name' => 'DummyWhatIsThis',
            'namespace' => __NAMESPACE__,
            'class_dir' => __DIR__,
            'config_dir' => $configDir = __DIR__ . '/config',
            'entry_service_file_path' => '/services/events.xml',
            'all_service_file_path' => '/services.xml',
        ]);

        $this
            ->assertCountFilesWillBeCreated(3)
            ->assertFileWillBeCreated(__DIR__ . '/DummyWhatIsThisListener.php', file_get_contents(__DIR__ . '/Expected.php'))
            ->assertFileWillBeCreated($configDir . '/services/events.xml', file_get_contents(__DIR__ . '/expectedService.xml'))
            ->assertFileWillBeCreated($configDir . '/services.xml', file_get_contents(__DIR__ . '/expectedServices.xml'));
    }
}
