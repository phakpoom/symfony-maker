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

        $this
            ->assertCountFilesWillBeCreated(3)
            ->assertFileWillBeCreated($this->codeDir() . '/Mock.php',
                file_get_contents(__DIR__ . '/ExpectedModel.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/MockInterface.php',
                file_get_contents(__DIR__ . '/ExpectedInterface.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/Mock.orm.xml',
                file_get_contents(__DIR__ . '/ExpectedDoctrine.orm.xml'))
        ;
    }

    public function testBidirectionalGenerated()
    {
        $this->generate(
            [
                new CollectionManyToManyOwnerBidirectionalType('comments', 'App\\Model\\CommentInterface'),
            ]
        );

        $this
            ->assertCountFilesWillBeCreated(3)
            ->assertFileWillBeCreated($this->codeDir() . '/Mock.php',
                file_get_contents(__DIR__ . '/ExpectedModel.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/MockInterface.php',
                file_get_contents(__DIR__ . '/ExpectedInterface.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/Mock.orm.xml',
                file_get_contents(__DIR__ . '/ExpectedDoctrine1.orm.xml'))
        ;
    }

    public function testBidirectionalMultipleGenerated()
    {
        $this->generate(
            [
                new CollectionManyToManyOwnerBidirectionalType('comments', 'App\\Model\\CommentInterface'),
                new CollectionManyToManyOwnerBidirectionalType('posts', 'App\\Model\\PostInterface'),
            ]
        );

        $this
            ->assertCountFilesWillBeCreated(3)
            ->assertFileWillBeCreated($this->codeDir() . '/Mock.php',
                file_get_contents(__DIR__ . '/ExpectedModel1.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/MockInterface.php',
                file_get_contents(__DIR__ . '/ExpectedInterface1.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/Mock.orm.xml',
                file_get_contents(__DIR__ . '/ExpectedDoctrine2.orm.xml'))
        ;
    }
}
