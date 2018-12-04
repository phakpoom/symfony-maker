<?php

declare(strict_types=1);

namespace Test\ModelPropType\Boolean;

use Bonn\Maker\ModelPropType\DateTimeType;
use Test\ModelPropType\AbstractPropTypeTest;

class DateTimeTypeTest extends AbstractPropTypeTest
{
    public function testGenerated()
    {
        $this->generate(new DateTimeType('deletedAt'));

        $allCodes = $this->manager->getCodes();

        $this->assertCount(3, $allCodes);
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedModel.php'),
            $allCodes[$this->codeDir() . '/Mock.php']->getContent());
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedInterface.php'),
            $allCodes[$this->codeDir() . '/MockInterface.php']->getContent());
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedDoctrine.orm.xml'),
            $allCodes[$this->codeDir() . '/Mock.orm.xml']->getContent());
    }
}
