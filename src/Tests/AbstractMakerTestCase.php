<?php

declare(strict_types=1);

namespace Bonn\Maker\Tests;

use Bonn\Maker\Manager\CodeManager;
use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Writer\EchoWriter;
use PHPUnit\Framework\TestCase;

abstract class AbstractMakerTestCase extends TestCase
{
    protected CodeManagerInterface $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = new CodeManager(new EchoWriter());
    }

    protected function assertCountFilesWillBeCreated(int $count): self
    {
        $this->assertCount($count, $this->manager->getCodes());

        return $this;
    }

    protected function assertFileWillBeCreated(string $path, string $withContent): self
    {
        $this->assertEquals($withContent,
            $this->manager->getCodes()[$path]->getContent());

        return $this;
    }

    protected function assertFileHasCreated(string $path, string $output): self
    {
        $this->assertContains('======' . $path . '======', $output, "$output hasn't created");

        return $this;
    }

    protected function assertFileHasNotCreated(string $path, string $output): self
    {
        $this->assertNotContains('======' . $path . '======' . $path, $output, "$output has created");

        return $this;
    }
}
