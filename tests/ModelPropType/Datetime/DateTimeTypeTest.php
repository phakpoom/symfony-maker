<?php

declare(strict_types=1);

namespace Test\ModelPropType\Boolean;

use Bonn\Maker\ModelPropType\DateTimeType;
use Test\ModelPropType\AbstractPropTypeTest;

class DateTimeTypeTest extends AbstractPropTypeTest
{
    public function testGenerated()
    {
        $this->generate(new DateTimeType('deletedAt'));

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
