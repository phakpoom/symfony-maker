<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Bonn\Maker\Bridge\MakerBundle\Command\GenerateEventListenerCommand;
use Bonn\Maker\Bridge\MakerBundle\Command\GenerateTwigExtensionCommand;
use Bonn\Maker\Bridge\MakerBundle\Tests\AbstractGenerateCommandWebTestCase;

class GenerateEventListenerCommandTest extends AbstractGenerateCommandWebTestCase
{
    public function testWithDummy()
    {
        $command = $this->getCommand();
        $command->setConfigs(array_replace($command->getConfigs(), [
            'writer_dev' => true,
        ]));

        $output = $this->runWithInput($command, [], [
            'name' => 'Dummy'
        ]);

        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App/') . '/EventListener/DummyListener.php' , $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App/') . '/Resources/config/services.xml' , $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App/') . '/Resources/config/services/event_listeners.xml' , $output);
    }

    public function testWithSubModule()
    {
        $command = $this->getCommand();
        $command->setConfigs(array_replace($command->getConfigs(), [
            'writer_dev' => true,
        ]));
        // [bundle_root_dir] it must auto generate namespace prefix with folder
        $command->setConfigs(array_replace($command->getConfigs(), [
            'namespace_prefix' => '',
            'bundle_root_dir' => realpath(__DIR__ . '/../../src/App/SubModule/'),
            'project_source_dir' => realpath(__DIR__ . '/../../src/'),
        ]));

        $output = $this->runWithInput($command, ['2'], [
            'name' => 'Dummy'
        ]);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App/SubModule/SomeFeature2') . '/EventListener/DummyListener.php' , $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App/SubModule/SomeFeature2') . '/Resources/config/services.xml' , $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App/SubModule/SomeFeature2') . '/Resources/config/services/event_listeners.xml' , $output);
    }

    protected function getCommand(): GenerateEventListenerCommand
    {
        self::bootKernel();
        return self::$container->get(GenerateEventListenerCommand::class);
    }
}
