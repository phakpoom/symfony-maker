<?php

declare(strict_types=1);

namespace Test\Writer;

use Bonn\Maker\Writer\FileWriter;
use PHPUnit\Framework\TestCase;
use Test\Cache\ModelGeneratedCacheTest;

final class FileWriterTest extends TestCase
{
    public function testWriter()
    {
        $localeFile = ModelGeneratedCacheTest::CACHE_DIR . '/Test.php';
        @unlink($localeFile);
        $this->assertFalse(\file_exists($localeFile));

        $writer = new FileWriter();
        $writer->write('write me', $localeFile);

        $this->assertTrue(\file_exists($localeFile));
        $this->assertEquals('write me', \file_get_contents($localeFile));
    }
}
