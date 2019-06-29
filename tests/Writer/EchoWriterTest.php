<?php

declare(strict_types=1);

namespace Test\Writer;

use Bonn\Maker\Writer\EchoWriter;
use PHPUnit\Framework\TestCase;

final class EchoWriterTest extends TestCase
{
    public function testWriter()
    {
        $writer = new EchoWriter();
        \ob_start();
        $writer->write('write me', __DIR__);
        $output = \ob_get_contents();
        \ob_end_clean();


        $this->assertStringContainsString('write me', $output);
        $this->assertStringContainsString(__DIR__, $output);
    }
}
