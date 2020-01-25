<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Bonn\Maker\Bridge\MakerBundle\Command\GenerateValidatorConfigCommand;
use Bonn\Maker\Bridge\MakerBundle\Tests\AbstractGenerateCommandWebTestCase;

class GenerateValidatorConfigCommandTest extends AbstractGenerateCommandWebTestCase
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

        $this->assertFileHasCreated(realpath(__DIR__ . '/../../') . '/config/validator/Dummy.xml', $output);
    }

    protected function getCommand(): GenerateValidatorConfigCommand
    {
        self::bootKernel();
        return self::$container->get(GenerateValidatorConfigCommand::class);
    }
}
