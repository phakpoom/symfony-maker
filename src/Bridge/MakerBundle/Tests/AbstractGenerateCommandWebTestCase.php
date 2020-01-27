<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Tests;

use Bonn\Maker\Bridge\MakerBundle\Command\AbstractGenerateCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;

abstract class AbstractGenerateCommandWebTestCase extends WebTestCase
{
    protected function runWithInput(AbstractGenerateCommand $commandClass, array $inputs, array $args = []): string
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $application->add($commandClass);

        $commandTester = new CommandTester($command = $application->find($commandClass->getName()));

        $commandTester->setInputs($inputs);

        if ($commandClass->getConfigs()['writer_dev']) {
            \ob_start();
            $commandTester->execute(array_replace($args, ['command' => $command->getName()]));
            $output = \ob_get_contents();
            \ob_end_clean();
        } else {
            $output = '';
            $commandTester->execute(array_replace($args, ['command' => $command->getName()]));
        }

        return preg_replace('/\\n/', '', $commandTester->getDisplay() . $output);
    }

    protected function assertFileHasCreated(string $path, string $output)
    {
        $this->assertContains('======' . $path . '======', $output, "$output hasn't created");

        return $this;
    }

    protected function assertFileHasDumped(string $path, string $output)
    {
        $this->assertContains('>>>>' . $path . '<<<<', $output, "$output hasn't dumped");

        return $this;
    }

    protected function assertFileHasNotCreated(string $path, string $output)
    {
        $this->assertNotContains('======' . $path . '======' . $path, $output, "$output has created");

        return $this;
    }
}
