<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\App\Model\Dummy;
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

        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App') . '/Resources/config/app/sylius_resource/dummy.yml' , $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App') . '/Factory/DummyFactory.php' , $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App') . '/Factory/DummyFactoryInterface.php' , $output);
    }

    protected function getCommand(): GenerateFactoryCommand
    {
        self::bootKernel();
        return self::$container->get(GenerateFactoryCommand::class);
    }
}
