<?php

declare(strict_types=1);

namespace Test\ModelPropType\Interfaces;

use Bonn\Maker\ModelPropType\InterfaceType;
use Test\ModelPropType\AbstractPropTypeTest;

class InterfaceTypeTest extends AbstractPropTypeTest
{
    public function testGenerated()
    {
        $this->generate(new InterfaceType('category', 'MyApp\\Model\\CategoryInterface'));

        $allCodes = $this->manager->getCodes();

        $this->assertCount(3, $allCodes);
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedModel.php'),
            $allCodes[$this->codeDir() . '/Mock.php']->getContent());
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedInterface.php'),
            $allCodes[$this->codeDir() . '/MockInterface.php']->getContent());
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedDoctrine.orm.xml'),
            $allCodes[$this->codeDir() . '/Mock.orm.xml']->getContent());

        // if same name space
        $this->generate(new InterfaceType('category', 'App\\Model\\CategoryInterface'));
        $allCodes = $this->manager->getCodes();
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedInterface1.php'),
            $allCodes[$this->codeDir() . '/MockInterface.php']->getContent());
    }
}
