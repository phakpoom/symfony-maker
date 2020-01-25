<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Bonn\Maker\Bridge\MakerBundle\Command\GenerateAllResourceFileFromSourceCommand;
use Bonn\Maker\Bridge\MakerBundle\Tests\AbstractGenerateCommandWebTestCase;
use Bonn\Maker\Writer\InMemoryWriter;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateAllFileSrouceFromSourceCommandTest extends AbstractGenerateCommandWebTestCase
{
    public function testRouteBasic()
    {
        $command = $this->getCommand();

        $this->setProp(self::$container->get('bonn_maker.manager.code_manager'), 'writer', $writer = new InMemoryWriter());
        $commandTester = new CommandTester($command);

        $commandTester->setInputs([])->execute([
            'file' => __DIR__ . '/../fixtures/routing.yaml'
        ]);

        $this->assertCount(4, $writer->files);
        $this->assertArrayHasKey(self::$container->getParameter('kernel.project_dir') . '/templates/Test/test.html.twig', $writer->files);
        $this->assertArrayHasKey(self::$container->getParameter('kernel.project_dir') . '/templates/_Admin/Crud/update.html.twig', $writer->files);
        $this->assertArrayHasKey(realpath(__DIR__ . '/../../../../../src') . '/Bridge/MakerBundle/Resources/views/AdminUser/_form.html.twig', $writer->files);
        $this->assertArrayHasKey(realpath(__DIR__ . '/../../../../../src') . '/Bridge/MakerBundle/Resources/views/_breadcrumb.html.twig', $writer->files);
    }

    public function testGridBasic()
    {
        $command = $this->getCommand();

        $this->setProp(self::$container->get('bonn_maker.manager.code_manager'), 'writer', $writer = new InMemoryWriter());
        $commandTester = new CommandTester($command);

        $commandTester->setInputs([])->execute([
            'file' => __DIR__ . '/../fixtures/grid.yaml'
        ]);

        $this->assertCount(3, $writer->files);
        $this->assertArrayHasKey(self::$container->getParameter('kernel.project_dir') . '/templates/_Admin/Grid/Field/duration.html.twig', $writer->files);
        $this->assertArrayHasKey(self::$container->getParameter('kernel.project_dir') . '/templates/_Admin/Grid/Field/botStatus.html.twig', $writer->files);
        $this->assertArrayHasKey($transFile = self::$container->getParameter('kernel.project_dir') . '/translations/messages.th.yaml', $writer->files);

        $this->assertEquals(<<<YAML
app:
    bonn:
        ui:
            name: บอน
            like: ''
        admin:
            ok: ''
    admin:
        grid:
            ok:
                bill_number: ''
                time: ''
                blame_admin_by: ''
                bot_state: ''
                note: ''

YAML
            , $writer->files[$transFile]
);
    }

    protected function getCommand(): GenerateAllResourceFileFromSourceCommand
    {
        self::bootKernel();
        return self::$container->get(GenerateAllResourceFileFromSourceCommand::class);
    }

    protected function setProp($service, string $prop, $newService)
    {
        $reflection = new \ReflectionClass($service);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        $property->setValue($service, $newService);
    }
}
