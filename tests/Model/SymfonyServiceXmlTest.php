<?php

declare(strict_types=1);

namespace Test\Model;

use Bonn\Maker\Model\SymfonyServiceXml;
use PHPUnit\Framework\TestCase;

class SymfonyServiceXmlTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage file
     */
    public function testCreateWithNoExistsFile()
    {
        new SymfonyServiceXml('no_exist');
    }

    public function testCreate()
    {
        $xml = new SymfonyServiceXml();
        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<container xmlns="http://symfony.com/schema/dic/services" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
    <services/>
</container>

XML
            , $xml->__toString());
    }
}
