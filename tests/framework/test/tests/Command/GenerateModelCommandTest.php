<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Bonn\Maker\Bridge\MakerBundle\Command\GenerateModelCommand;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateModelCommandTest extends WebTestCase
{
    public function testWithBundleConfig()
    {
        // Class, prop name, type, defaultValue, end, end
        $inputs = ['Test', 'name', '0', null, '', ''];
        $command = $this->getCommand();
        // [namespace_prefix] with multiple slash
        $command->setConfigs(array_replace($command->getConfigs(), [
            'namespace_prefix' => 'Bonn\\\\'
        ]));
        $output = $this->runWithInput($command, $inputs);
        $this->assertContains('namespace Bonn\\App\\Model;', $output);

        // [namespace_prefix] with empty
        $command->setConfigs(array_replace($command->getConfigs(), [
            'namespace_prefix' => ''
        ]));
        $output = $this->runWithInput($command, $inputs);
        $this->assertContains('namespace App\\Model;', $output);

        // [namespace_prefix] with normal
        $command->setConfigs(array_replace($command->getConfigs(), [
            'namespace_prefix' => 'Bonn\\Test'
        ]));
        $output = $this->runWithInput($command, $inputs);
        $this->assertContains('namespace Bonn\\Test\\App\\Model;', $output);

        // [bundle_root_dir] it must auto generate namespace prefix with folder
        $command->setConfigs(array_replace($command->getConfigs(), [
            'namespace_prefix' => '',
            'bundle_root_dir' => realpath(__DIR__ . '/../../src/App/SubModule/'),
            'project_source_dir' => realpath(__DIR__ . '/../../src/'),
        ]));

        $output = $this->runWithInput($command, ['Test', '2' , 'name', '0', null, '', '']);
        $this->assertContains($command->getConfigs()['bundle_root_dir'] . '/SomeFeature2', $output);
        $this->assertContains('namespace App\\SubModule\\SomeFeature2\\Model;', $output);

        $output = $this->runWithInput($command, ['Test', '1' , 'name', '0', null, '', '']);
        $this->assertContains($command->getConfigs()['bundle_root_dir'] . '/SomeFeature1', $output);
        $this->assertContains('namespace App\\SubModule\\SomeFeature1\\Model;', $output);

        // [model_dir_name] / [doctrine_mapping_dir_name]
        $command->setConfigs(array_replace($command->getConfigs(), [
            'doctrine_mapping_dir_name' => 'mapping',
            'model_dir_name' => 'Entity',
            'bundle_root_dir' => realpath(__DIR__ . '/../../src/'),
        ]));

        $output = $this->runWithInput($command, $inputs);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App/') . '/Entity/Test.php', $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App/') . '/mapping/Test.orm.xml', $output);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage  Operation must be one of
     */
    public function testWithInvalidOp()
    {
        $command = $this->getCommand();
        $command->setConfigs(array_replace($command->getConfigs(), [
            'writer_dev' => false,
        ]));

        $this->runWithInput($command, [], 'test');
    }

    public function testDumpOp()
    {
        $command = $this->getCommand();

        $output = $this->runWithInput($command, ['Test', 'name', '0', null, '', ''], 'make');
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App/') . '/Model/Test.php', $output);

        $output = $this->runWithInput($command, ['name', '0', null, '', ''], 'dump');
        $this->assertFileHasNotCreated(realpath(__DIR__ . '/../../src/App/') . '/Model/Dummy.php', $output);
    }

    public function testRollbackOp()
    {
        $command = $this->getCommand();
        self::$container->get('bonn_maker.cache.generated_model')->clear('Test');
        // version 0
        $output = $this->runWithInput($command, ['Test', 'name', '0', null, '', ''], 'make');
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App/') . '/Model/Test.php', $output);

        // version 1
        $output = $this->runWithInput($command, ['Test', 'description', '0', null, '', ''], 'make');
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App/') . '/Model/Test.php', $output);

        $output = $this->runWithInput($command, ['NoClassCache', 'name', '0', null, '', ''], 'rollback');
        $this->assertContains('No versions for class', $output);

        $output = $this->runWithInput($command, ['Test', '0'], 'rollback');
        $this->assertContains('Please select your version:Test', $output);
        $this->assertContains('protected $name', $output);

        $output = $this->runWithInput($command, ['Test', '1'], 'rollback');
        $this->assertContains('Please select your version:Test', $output);
        $this->assertContains('protected $description', $output);
    }

    public function testMakeOp()
    {
        $command = $this->getCommand();
        self::$container->get('bonn_maker.cache.generated_model')->clear('Test');
        // version 0
        $output = $this->runWithInput($command, ['Test', 'name', '0', null, '', ''], 'make');
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App/') . '/Model/Test.php', $output);

        // version 1
        $output = $this->runWithInput($command, ['Test', 'description', '0', null, '', ''], 'make');
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/App/') . '/Model/Test.php', $output);

        $output = $this->runWithInput($command, ['NoClassCache', 'name', '0', null, '', ''], 'rollback');
        $this->assertContains('No versions for class', $output);

        $output = $this->runWithInput($command, ['Test', '0'], 'rollback');
        $this->assertContains('Please select your version:Test', $output);
        $this->assertContains('protected $name', $output);

        $output = $this->runWithInput($command, ['Test', '1'], 'rollback');
        $this->assertContains('Please select your version:Test', $output);
        $this->assertContains('protected $description', $output);
    }

    protected function runWithInput(Command $commandClass, array $inputs, string $op = 'make'): string
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $application->add($commandClass);

        $commandTester = new CommandTester($command = $application->find('bonn:model:maker'));

        $commandTester->setInputs($inputs);

        if ($commandClass->getConfigs()['writer_dev']) {
            \ob_start();
            $commandTester->execute(['command' => $command->getName(), 'op' => $op]);
            $output = \ob_get_contents();
            \ob_end_clean();
        } else {
            $output = '';
            $commandTester->execute(['command' => $command->getName(), 'op' => $op]);
        }

        return $commandTester->getDisplay() . $output;
    }

    protected function getCommand(): GenerateModelCommand
    {
        self::bootKernel();
        return self::$container->get('bonn_maker.command.generate_model');
    }

    protected function assertFileHasCreated(string $path, string $output)
    {
        $this->assertContains('[OK] ' . $path, $output);
    }

    protected function assertFileHasNotCreated(string $path, string $output)
    {
        $this->assertNotContains('[OK] ' . $path, $output);
    }
}
