<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Bonn\Maker\Bridge\MakerBundle\Command\GenerateModelCommand;
use Bonn\Maker\Bridge\MakerBundle\Tests\AbstractGenerateCommandWebTestCase;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class GenerateModelCommandTest extends AbstractGenerateCommandWebTestCase
{
    public function testWithBundleConfig()
    {
        // Class, prop name, type, defaultValue, end, end
        $inputs = ['Test', 'name', '0', null, '', 'Y'];
        $command = $this->getCommand();
        // [namespace_prefix] with multiple slash
        $command->setConfigs(array_replace($command->getConfigs(), [
            'namespace_prefix' => 'Bonn\\\\'
        ]));
        $output = $this->runWithInput($command, $inputs);
        $this->assertContains('namespace Bonn\\AppBundle\\Model;', $output);

        // [namespace_prefix] with empty
        $command->setConfigs(array_replace($command->getConfigs(), [
            'namespace_prefix' => ''
        ]));
        $output = $this->runWithInput($command, $inputs);
        $this->assertContains('namespace AppBundle\\Model;', $output);

        // [namespace_prefix] with normal
        $command->setConfigs(array_replace($command->getConfigs(), [
            'namespace_prefix' => 'Bonn\\Test'
        ]));
        $output = $this->runWithInput($command, $inputs);
        $this->assertContains('namespace Bonn\\Test\\AppBundle\\Model;', $output);

        // [bundle_root_dir] it must auto generate namespace prefix with folder
        $command->setConfigs(array_replace($command->getConfigs(), [
            'namespace_prefix' => '',
            'bundle_root_dir' => realpath(__DIR__ . '/../../src/AppBundle/SubModule/'),
            'project_source_dir' => realpath(__DIR__ . '/../../src/'),
        ]));

        $output = $this->runWithInput($command, ['Test', '2' , 'name', '0', null, '', 'Y']);
        $this->assertContains($command->getConfigs()['bundle_root_dir'] . '/SomeFeature2', $output);
        $this->assertContains('namespace AppBundle\\SubModule\\SomeFeature2\\Model;', $output);

        $output = $this->runWithInput($command, ['Test', '1' , 'name', '0', null, '', 'Y']);
        $this->assertContains($command->getConfigs()['bundle_root_dir'] . '/SomeFeature1', $output);
        $this->assertContains('namespace AppBundle\\SubModule\\SomeFeature1\\Model;', $output);

        // [model_dir_name] / [doctrine_mapping_dir_name]
        $command->setConfigs(array_replace($command->getConfigs(), [
            'doctrine_mapping_dir_name' => 'mapping',
            'model_dir_name' => 'Entity',
            'bundle_root_dir' => realpath(__DIR__ . '/../../src/'),
        ]));

        $output = $this->runWithInput($command, $inputs);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/AppBundle/') . '/Entity/Test.php', $output);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/AppBundle/') . '/mapping/Test.orm.xml', $output);
    }

    public function testWithInvalidOp()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Operation must be one of');

        $command = $this->getCommand();
        $command->setConfigs(array_replace($command->getConfigs(), [
            'writer_dev' => false,
        ]));

        $this->runWithInput($command, [], ['op' => 'test']);
    }

    public function testDumpOp()
    {
        $command = $this->getCommand();

        $output = $this->runWithInput($command, ['Test', 'name', '0', null, '', 'Y']);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/AppBundle/') . '/Model/Test.php', $output);

        $output = $this->runWithInput($command, ['name', '0', null, '', 'Y'], ['op' => 'dump']);
        $this->assertFileHasNotCreated(realpath(__DIR__ . '/../../src/AppBundle/') . '/Model/Dummy.php', $output);
    }

    public function testRollbackOp()
    {
        $command = $this->getCommand();
        self::$kernel->getContainer()->get('bonn_maker.cache.generated_model')->clear();

        // version 0
        $output = $this->runWithInput($command, ['Test', 'name', '0', null, '', 'Y']);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/AppBundle/') . '/Model/Test.php', $output);

        // version 1
        $output = $this->runWithInput($command, ['Test', 'description', '0', null, '', 'Y']);
        $this->assertFileHasCreated(realpath(__DIR__ . '/../../src/AppBundle/') . '/Model/Test.php', $output);

        $output = $this->runWithInput($command, ['NoClassCache', 'name', '0', null, '', 'Y'], ['op' => 'rollback']);
        $this->assertContains('No versions for class', $output);

        $output = $this->runWithInput($command, ['Test', '0'], ['op' => 'rollback']);
        $this->assertContains('Please select your version:Test', $output);
        $this->assertContains('protected $name', $output);

        $output = $this->runWithInput($command, ['Test', '1'], ['op' => 'rollback']);
        $this->assertContains('Please select your version:Test', $output);
        $this->assertContains('protected $description', $output);
    }

    public function testPropTypeAskDocBlock()
    {
        // no docblock
        $command = $this->getCommand();
        $output = $this->runWithInput($command, ['Test', 'name', '0', null, '', 'Y']);
        $this->assertContains('Enter value (enter for skip)', $output);

        // @commandValueSkip
        $command = $this->getCommand();
        $output = $this->runWithInput($command, ['Test', 'name', '3', null, '', 'Y']);
        $this->assertNotContains('Enter value (enter for skip)', $output);

        // @commandValueDescription
        $command = $this->getCommand();
        $output = $this->runWithInput($command, ['Test', 'name', '2', null, '', 'Y']);
        $this->assertContains('Enter true|false (default false)', $output);

        // @commandValueRequired
        $command = $this->getCommand();
        $output = $this->runWithInput($command, ['Test', 'name', '7', null, null, null, 'TestInterface', '', 'Y']);
        $this->assertContains('Value cannot be empty', $output);
    }

    public function testHasConfirmationBeforeFinish()
    {
        $command = $this->getCommand();

        $output = $this->runWithInput($command, ['Test', 'name', '0', null, '', 'n', '', 'Y']);
        $this->assertContains('Are you sure', $output);
    }

    protected function getCommand(): GenerateModelCommand
    {
        self::bootKernel();
        return self::$kernel->getContainer()->get(GenerateModelCommand::class);
    }
}
