<?php

declare(strict_types=1);

namespace App\Tests\Command;

use AppBundle\Model\Dummy;
use Bonn\Maker\Bridge\MakerBundle\Command\GenerateRepositoryCommand;
use Bonn\Maker\Bridge\MakerBundle\Tests\AbstractGenerateCommandWebTestCase;

class GenerateRepositoryCommandTest extends AbstractGenerateCommandWebTestCase
{
    public function testWithDummy()
    {
        $command = $this->getCommand();
        $command->setConfigs(array_replace($command->getConfigs(), [
            'writer_dev' => true,
        ]));

        $output = $this->runWithInput($command, [], ['class' => Dummy::class]);

        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/AppBundle/') . '/Doctrine/ORM/DummyRepository.php' , $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/AppBundle/') . '/Doctrine/ORM/DummyRepositoryInterface.php' , $output);
    }

    protected function getCommand(): GenerateRepositoryCommand
    {
        self::bootKernel();
        return self::$kernel->getContainer()->get(GenerateRepositoryCommand::class);
    }
}
