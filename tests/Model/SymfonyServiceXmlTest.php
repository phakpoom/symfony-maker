<?php

declare(strict_types=1);

namespace Test\Model;

use Bonn\Maker\Model\SymfonyServiceXml;
use PHPUnit\Framework\TestCase;

class SymfonyServiceXmlTest extends TestCase
{
    public function testCreateWithNoExistsFile()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('file');

        new SymfonyServiceXml('no_exist');
    }

    public function testCreateWithEmpty()
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

    public function testAddService()
    {
        $xml = new SymfonyServiceXml();
        $xml->addService('test_id', __CLASS__);
        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<container xmlns="http://symfony.com/schema/dic/services" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
    <services>
        <service id="test_id" class="Test\Model\SymfonyServiceXmlTest"/>
    </services>
</container>

XML
            , $xml->__toString());
    }

    public function testAddServiceWithArgument()
    {
        $xml = new SymfonyServiceXml();
        $xml->addService('test_id', __CLASS__)->add('argument', 'test');
        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<container xmlns="http://symfony.com/schema/dic/services" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
    <services>
        <service id="test_id" class="Test\Model\SymfonyServiceXmlTest">
            <argument>test</argument>
        </service>
    </services>
</container>

XML
            , $xml->__toString());
    }

    public function testAddImport()
    {
        $xml = new SymfonyServiceXml();
        $xml->addImport('ok/test.xml');
        $xml->addImport('ok/test1.xml');
        $this->assertEquals(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<container xmlns="http://symfony.com/schema/dic/services" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
    <imports>
        <import resource="ok/test.xml"/>
        <import resource="ok/test1.xml"/>
    </imports>
    <services/>
</container>

XML
            , $xml->__toString());
    }
}
