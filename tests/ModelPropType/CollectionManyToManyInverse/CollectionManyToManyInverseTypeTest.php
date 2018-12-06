<?php

declare(strict_types=1);

namespace Test\ModelPropType\Interfaces;

use Bonn\Maker\ModelPropType\CollectionManyToManyInverseType;
use Test\ModelPropType\AbstractPropTypeTest;

class CollectionManyToManyInverseTypeTest extends AbstractPropTypeTest
{
    public function testGenerated()
    {
        $this->generate(new CollectionManyToManyInverseType('comments', 'App\\Model\\CommentInterface'));

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
