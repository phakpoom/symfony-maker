<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Bonn\Maker\Bridge\MakerBundle\Command\GenerateTwigExtensionCommand;
use Bonn\Maker\Bridge\MakerBundle\Tests\AbstractGenerateCommandWebTestCase;

class GenerateTwigExtensionCommandTest extends AbstractGenerateCommandWebTestCase
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

        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/AppBundle/') . '/Twig/Extension/DummyExtension.php' , $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/AppBundle/') . '/Resources/config/services.xml' , $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/AppBundle/') . '/Resources/config/services/twigs.xml' , $output);
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
            'bundle_root_dir' => realpath(__DIR__ . '/../../src/AppBundle/SubModule/'),
            'project_source_dir' => realpath(__DIR__ . '/../../src/'),
        ]));

        $output = $this->runWithInput($command, ['2'], [
            'name' => 'Dummy'
        ]);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/AppBundle/SubModule/SomeFeature2') . '/Twig/Extension/DummyExtension.php' , $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/AppBundle/SubModule/SomeFeature2') . '/Resources/config/services.xml' , $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/AppBundle/SubModule/SomeFeature2') . '/Resources/config/services/twigs.xml' , $output);
    }

    protected function getCommand(): GenerateTwigExtensionCommand
    {
        self::bootKernel();
        return self::$kernel->getContainer()->get('bonn_maker.command.generate_twig_extension');
    }
}
