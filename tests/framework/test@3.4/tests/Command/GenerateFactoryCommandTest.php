<?php

declare(strict_types=1);

namespace App\Tests\Command;

use AppBundle\Model\Dummy;
use Bonn\Maker\Bridge\MakerBundle\Command\GenerateFactoryCommand;
use Bonn\Maker\Bridge\MakerBundle\Tests\AbstractGenerateCommandWebTestCase;

class GenerateFactoryCommandTest extends AbstractGenerateCommandWebTestCase
{
    public function testWithDummy()
    {
        $command = $this->getCommand();
        $command->setConfigs(array_replace($command->getConfigs(), [
            'writer_dev' => true,
        ]));

        $output = $this->runWithInput($command, [], ['class' => Dummy::class]);

        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/AppBundle/') . '/Factory/DummyFactory.php' , $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/AppBundle/') . '/Factory/DummyFactoryInterface.php' , $output);
    }

    protected function getCommand(): GenerateFactoryCommand
    {
        self::bootKernel();
        return self::$kernel->getContainer()->get('bonn_maker.command.generate_factory');
    }
}
