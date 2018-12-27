<?php

declare(strict_types=1);

namespace Test\ModelPropType\Rray;

use Bonn\Maker\ModelPropType\ArrayType;
use Test\ModelPropType\AbstractPropTypeTest;

class ArrayTypeTest extends AbstractPropTypeTest
{
    public function testGenerated()
    {
        $this->generate(new ArrayType('configs'));

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
}
