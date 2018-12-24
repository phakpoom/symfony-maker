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

    public function testGeneratedWithSameNamespace()
    {
        $this->generate(new InterfaceType('category', 'App\\Model\\CategoryInterface'));
        $this
            ->assertCountFilesWillBeCreated(3)
            ->assertFileWillBeCreated($this->codeDir() . '/MockInterface.php',
                file_get_contents(__DIR__ . '/ExpectedInterface1.php'))
        ;
    }
}
