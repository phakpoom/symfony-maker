<?php

declare(strict_types=1);

namespace App\Tests\Command;

use AppBundle\Model\Dummy;
use Bonn\Maker\Bridge\MakerBundle\Command\GenerateControllerCommand;
use Bonn\Maker\Bridge\MakerBundle\Tests\AbstractGenerateCommandWebTestCase;

class GenerateControllerCommandTest extends AbstractGenerateCommandWebTestCase
{
    public function testWithDummy()
    {
        $command = $this->getCommand();
        $command->setConfigs(array_replace($command->getConfigs(), [
            'writer_dev' => true,
        ]));

        $output = $this->runWithInput($command, [], ['class' => Dummy::class]);

        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/AppBundle/') . '/Controller/DummyController.php' , $output);
    }

    protected function getCommand(): GenerateControllerCommand
    {
        self::bootKernel();
        return self::$kernel->getContainer()->get(GenerateControllerCommand::class);
    }
}
