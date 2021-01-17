<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Bonn\Maker\Bridge\MakerBundle\Command\GenerateTranslationCommand;
use Bonn\Maker\Bridge\MakerBundle\Tests\AbstractGenerateCommandWebTestCase;

class GenerateTranslationCommandTest extends AbstractGenerateCommandWebTestCase
{
    public function testWithDummy()
    {
        $command = $this->getCommand();
        $command->setConfigs(array_replace($command->getConfigs(), [
            'writer_dev' => true,
        ]));

        $output = $this->runWithInput($command, [], [
            'full_class_name' => 'App\\App\\Model\\Dummy'
        ]);

        $this->assertFileHasCreated(realpath(__DIR__ . '/../../') . '/translations/messages.th.yaml', $output);
    }

    protected function getCommand(): GenerateTranslationCommand
    {
        self::bootKernel();
        return self::$container->get(GenerateTranslationCommand::class);
    }
}
