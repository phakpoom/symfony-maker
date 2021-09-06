<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\App\Model\Dummy;
use Bonn\Maker\Bridge\MakerBundle\Command\GenerateFormCommand;
use Bonn\Maker\Bridge\MakerBundle\Command\GenerateStateMachineCommand;
use Bonn\Maker\Bridge\MakerBundle\Tests\AbstractGenerateCommandWebTestCase;

class GenerateStateMachineCommandTest extends AbstractGenerateCommandWebTestCase
{
    public function testWithDummy()
    {
        $command = $this->getCommand();
        $command->setConfigs(array_replace($command->getConfigs(), [
            'writer_dev' => true,
        ]));

        $output = $this->runWithInput($command, [], [
            'class' => 'App\App\Model\Dummy'
        ]);

        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App') . '/StateCallback/DummyCallback.php' , $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App') . '/Resources/config/services.xml' , $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App') . '/Resources/config/app/state_machine/dummy.yml' , $output);
    }

    protected function getCommand(): GenerateStateMachineCommand
    {
        self::bootKernel();

        return self::$container->get(GenerateStateMachineCommand::class);
    }
}
