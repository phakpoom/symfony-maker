<?php

declare(strict_types=1);

namespace Test\Writer;

use Bonn\Maker\Writer\InMemoryWriter;
use PHPUnit\Framework\TestCase;
use Test\Cache\ModelGeneratedCacheTest;

final class InMemoryWriterTest extends TestCase
{
    public function testWriter()
    {
        $localeFile = ModelGeneratedCacheTest::CACHE_DIR . '/Test.php';

        $writer = new InMemoryWriter();
        $writer->write('write me', $localeFile);

        $this->assertArrayHasKey($localeFile, $writer->files);
        $this->assertEquals('write me', $writer->files[$localeFile]);
    }
}
