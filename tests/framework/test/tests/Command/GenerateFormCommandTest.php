<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\App\Model\Dummy;
use Bonn\Maker\Bridge\MakerBundle\Command\GenerateFormCommand;
use Bonn\Maker\Bridge\MakerBundle\Tests\AbstractGenerateCommandWebTestCase;

class GenerateFormCommandTest extends AbstractGenerateCommandWebTestCase
{
    public function testWithDummy()
    {
        $command = $this->getCommand();
        $command->setConfigs(array_replace($command->getConfigs(), [
            'writer_dev' => true,
        ]));

        $output = $this->runWithInput($command, [], [
            'class' => Dummy::class
        ]);

        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App') . '/Form/Type/DummyType.php' , $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App') . '/Resources/config/services.xml' , $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App') . '/Resources/config/services/forms.xml' , $output);
    }

    protected function getCommand(): GenerateFormCommand
    {
        self::bootKernel();
        return self::$container->get(GenerateFormCommand::class);
    }
}
