<?php

declare(strict_types=1);

namespace Test\ModelPropType\Boolean;

use Bonn\Maker\ModelPropType\BooleanType;
use Test\ModelPropType\AbstractPropTypeTest;

class BooleanTypeTest extends AbstractPropTypeTest
{
    public function testGenerated()
    {
        $this->generate(new BooleanType('active'));

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
        $this->generate(new BooleanType('active', 'true'));
        $allCodes = $this->manager->getCodes();
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedModel1.php'),
            $allCodes[$this->codeDir() . '/Mock.php']->getContent());
    }
}
