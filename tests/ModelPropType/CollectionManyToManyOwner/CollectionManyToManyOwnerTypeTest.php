<?php

declare(strict_types=1);

namespace Test\ModelPropType\Interfaces;

use Bonn\Maker\ModelPropType\CollectionManyToManyOwnerBidirectionalType;
use Bonn\Maker\ModelPropType\CollectionManyToManyOwnerUnidirectionalType;
use Test\ModelPropType\AbstractPropTypeTest;

class CollectionManyToManyOwnerTypeTest extends AbstractPropTypeTest
{
    public function testUnidirectionalGenerated()
    {
        $this->generate(new CollectionManyToManyOwnerUnidirectionalType('comments', 'App\\Model\\CommentInterface'));

        $allCodes = $this->manager->getCodes();

        $this->assertCount(3, $allCodes);
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedModel.php'),
            $allCodes[$this->codeDir() . '/Mock.php']->getContent());
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedInterface.php'),
            $allCodes[$this->codeDir() . '/MockInterface.php']->getContent());
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedDoctrine.orm.xml'),
            $allCodes[$this->codeDir() . '/Mock.orm.xml']->getContent());
    }

    public function testBidirectionalGenerated()
    {
        $this->generate(
            [
                new CollectionManyToManyOwnerBidirectionalType('comments', 'App\\Model\\CommentInterface'),
            ]
        );

        $allCodes = $this->manager->getCodes();

        $this->assertCount(3, $allCodes);
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedModel.php'),
            $allCodes[$this->codeDir() . '/Mock.php']->getContent());
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedInterface.php'),
            $allCodes[$this->codeDir() . '/MockInterface.php']->getContent());
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedDoctrine1.orm.xml'),
            $allCodes[$this->codeDir() . '/Mock.orm.xml']->getContent());
    }

    public function testBidirectionalMultipleGenerated()
    {
        $this->generate(
            [
                new CollectionManyToManyOwnerBidirectionalType('comments', 'App\\Model\\CommentInterface'),
                new CollectionManyToManyOwnerBidirectionalType('posts', 'App\\Model\\PostInterface'),
            ]
        );

        $allCodes = $this->manager->getCodes();

        $this->assertCount(3, $allCodes);
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedModel1.php'),
            $allCodes[$this->codeDir() . '/Mock.php']->getContent());
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedInterface1.php'),
            $allCodes[$this->codeDir() . '/MockInterface.php']->getContent());
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedDoctrine2.orm.xml'),
            $allCodes[$this->codeDir() . '/Mock.orm.xml']->getContent());
    }
}
