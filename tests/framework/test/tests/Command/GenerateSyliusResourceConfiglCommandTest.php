<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\App\Model\Dummy;
use Bonn\Maker\Bridge\MakerBundle\Command\GenerateSyliusResourceConfigCommand;
use Bonn\Maker\Bridge\MakerBundle\Tests\AbstractGenerateCommandWebTestCase;

class GenerateSyliusResourceConfiglCommandTest extends AbstractGenerateCommandWebTestCase
{
    public function testWithDummy()
    {
        $command = $this->getCommand();
        $command->setConfigs(array_replace($command->getConfigs(), [
            'writer_dev' => true,
        ]));

        $output = $this->runWithInput($command, [], ['class' => Dummy::class]);

        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App/') . '/Resources/config/app/sylius_resource/dummy.yml' , $output);
    }

    protected function getCommand(): GenerateSyliusResourceConfigCommand
    {
        self::bootKernel();
        return self::$container->get('bonn_maker.command.generate_sylius_resource_config');
    }
}
