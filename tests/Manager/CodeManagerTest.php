<?php

declare(strict_types=1);

namespace Test\Manager;

use Bonn\Maker\Manager\CodeManager;
use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Model\Code;
use Bonn\Maker\Writer\EchoWriter;
use PHPUnit\Framework\TestCase;

final class CodeManagerTest extends TestCase
{
    /** @var CodeManagerInterface */
    private $manager;

    protected function setUp()
    {
        parent::setUp();

        $this->manager = new CodeManager(new EchoWriter());
    }

    public function testBasicPersist()
    {
        $this->manager->persist(new Code('test code', __DIR__));

        $this->assertArrayHasKey(__DIR__, $this->manager->getCodes());
        $this->assertCount(1, $this->manager->getCodes());
        $this->assertInstanceOf(Code::class, $this->manager->getCodes()[__DIR__]);
    }

    public function testPersistSameDir()
    {
        $this->manager->persist(new Code('test code1', __DIR__));
        $this->manager->persist(new Code('test code2', __DIR__));
        $this->manager->persist($codeExpect = new Code('test code3', __DIR__));

        $this->assertArrayHasKey(__DIR__, $this->manager->getCodes());
        $this->assertCount(1, $this->manager->getCodes());
        $this->assertInstanceOf(Code::class, $this->manager->getCodes()[__DIR__]);
        $this->assertEquals('test code3', $this->manager->getCodes()[__DIR__]->getContent());
    }

    public function testDetachExists()
    {
        $this->manager->persist($code = new Code('test code', __DIR__));
        $this->manager->persist(new Code('test code1', __DIR__ . '/test1'));
        $this->assertCount(2, $this->manager->getCodes());

        $this->manager->detach($code);
        $this->assertCount(1, $this->manager->getCodes());
        $this->assertArrayNotHasKey(__DIR__, $this->manager->getCodes());
    }

    public function testPersistAndThenClear()
    {
        $this->manager->persist(new Code('test code1', __DIR__));
        $this->manager->persist(new Code('test code2', __DIR__ . '/test2'));
        $this->manager->persist(new Code('test code3', __DIR__ . '/test3'));

        $this->assertCount(3, $this->manager->getCodes());
        $this->manager->clear();

        $this->assertArrayNotHasKey(__DIR__, $this->manager->getCodes());
        $this->assertCount(0, $this->manager->getCodes());
    }

    public function testFlush()
    {
        $this->manager->persist(new Code('test code', __DIR__));
        \ob_start();
        $this->manager->flush();
        $output = \ob_get_contents();
        \ob_end_clean();

        $this->assertCount(0, $this->manager->getCodes());
        $this->assertContains('test code', $output);
    }
}
