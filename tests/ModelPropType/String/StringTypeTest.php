<?php

declare(strict_types=1);

namespace Test\ModelPropType\String;

use Bonn\Maker\ModelPropType\StringType;
use Test\ModelPropType\AbstractPropTypeTest;

class StringTypeTest extends AbstractPropTypeTest
{
    public function testGenerated()
    {
        $this->generate(new StringType('name'));

        $this
            ->assertCountFilesWillBeCreated(3)
            ->assertFileWillBeCreated($this->codeDir() . '/Mock.php',
                file_get_contents(__DIR__ . '/ExpectedModel.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/MockInterface.php',
                file_get_contents(__DIR__ . '/ExpectedInterface.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/Mock.orm.xml',
                file_get_contents(__DIR__ . '/ExpectedDoctrine.orm.xml'))
        ;

        $this->manager->clear();
        // with default value
        $this->generate(new StringType('name', 'bon'));
        $this
            ->assertCountFilesWillBeCreated(3)
            ->assertFileWillBeCreated($this->codeDir() . '/Mock.php',
                file_get_contents(__DIR__ . '/ExpectedModel1.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/MockInterface.php',
                file_get_contents(__DIR__ . '/ExpectedInterface1.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/Mock.orm.xml',
                file_get_contents(__DIR__ . '/ExpectedDoctrine1.orm.xml'))
        ;
    }
}
