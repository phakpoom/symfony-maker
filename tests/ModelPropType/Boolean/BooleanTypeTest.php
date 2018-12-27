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

        $this
            ->assertCountFilesWillBeCreated(3)
            ->assertFileWillBeCreated($this->codeDir() . '/Mock.php',
                file_get_contents(__DIR__ . '/ExpectedModel.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/MockInterface.php',
                file_get_contents(__DIR__ . '/ExpectedInterface.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/Mock.orm.xml',
                file_get_contents(__DIR__ . '/ExpectedDoctrine.orm.xml'))
        ;

        // with default value
        $this->manager->clear();
        $this->generate(new BooleanType('active', 'true'));
        $this
            ->assertCountFilesWillBeCreated(3)
            ->assertFileWillBeCreated($this->codeDir() . '/Mock.php',
                file_get_contents(__DIR__ . '/ExpectedModel1.php'))
        ;
    }
}
