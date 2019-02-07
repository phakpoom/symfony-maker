<?php

declare(strict_types=1);

namespace App\Tests\Command;

use AppBundle\Model\Dummy;
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

        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/AppBundle/') . '/Form/Type/DummyType.php' , $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/AppBundle/') . '/Resources/config/services.xml' , $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/AppBundle/') . '/Resources/config/services/forms.xml' , $output);
    }

    protected function getCommand(): GenerateFormCommand
    {
        self::bootKernel();
        return self::$kernel->getContainer()->get('bonn_maker.command.generate_form');
    }
}
