<?php

declare(strict_types=1);

namespace Test\ModelPropType\Boolean;

use Bonn\Maker\ModelPropType\IntegerType;
use Test\ModelPropType\AbstractPropTypeTest;

class IntegerTypeTest extends AbstractPropTypeTest
{
    public function testGenerated()
    {
        $this->generate(new IntegerType('price'));

        $allCodes = $this->manager->getCodes();

        $this->assertCount(3, $allCodes);
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedModel.php'),
            $allCodes[$this->codeDir() . '/Mock.php']->getContent());
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedInterface.php'),
            $allCodes[$this->codeDir() . '/MockInterface.php']->getContent());
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedDoctrine.orm.xml'),
            $allCodes[$this->codeDir() . '/Mock.orm.xml']->getContent());

        $this->manager->clear();
        // with default value
        $this->generate(new IntegerType('price', '12'));
        $allCodes = $this->manager->getCodes();
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedModel1.php'),
            $allCodes[$this->codeDir() . '/Mock.php']->getContent());
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedInterface1.php'),
            $allCodes[$this->codeDir() . '/MockInterface.php']->getContent());
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedDoctrine1.orm.xml'),
            $allCodes[$this->codeDir() . '/Mock.orm.xml']->getContent());
    }
}
