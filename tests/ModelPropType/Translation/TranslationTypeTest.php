<?php

declare(strict_types=1);

namespace Test\ModelPropType\Translation;

use Bonn\Maker\ModelPropType\TranslationType;
use Test\ModelPropType\AbstractPropTypeTest;

class TranslationTypeTest extends AbstractPropTypeTest
{
    public function testGenerated()
    {
        $this->generate([
            new TranslationType('name'),
        ]);

        $this
            ->assertCountFilesWillBeCreated(6)
            ->assertFileWillBeCreated($this->codeDir() . '/Mock.php',
                file_get_contents(__DIR__ . '/1ExpectedModel.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/MockInterface.php',
                file_get_contents(__DIR__ . '/1ExpectedInterface.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/MockTranslation.php',
                file_get_contents(__DIR__ . '/1ExpectedModelTranslation.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/MockTranslationInterface.php',
                file_get_contents(__DIR__ . '/1ExpectedInterfaceTranslation.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/Mock.orm.xml',
                file_get_contents(__DIR__ . '/1ExpectedDoctrine.orm.xml'))
            ->assertFileWillBeCreated($this->codeDir() . '/MockTranslation.orm.xml',
                file_get_contents(__DIR__ . '/1ExpectedDoctrineTranslation.orm.xml'))
        ;
    }

    public function testMultipleFieldGenerated()
    {
        $this->generate([
            new TranslationType('name'),
            new TranslationType('description'),
        ]);

        $this
            ->assertCountFilesWillBeCreated(6)
            ->assertFileWillBeCreated($this->codeDir() . '/Mock.php',
                file_get_contents(__DIR__ . '/ExpectedModel.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/MockInterface.php',
                file_get_contents(__DIR__ . '/ExpectedInterface.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/MockTranslation.php',
                file_get_contents(__DIR__ . '/ExpectedModelTranslation.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/MockTranslationInterface.php',
                file_get_contents(__DIR__ . '/ExpectedInterfaceTranslation.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/Mock.orm.xml',
                file_get_contents(__DIR__ . '/ExpectedDoctrine.orm.xml'))
            ->assertFileWillBeCreated($this->codeDir() . '/MockTranslation.orm.xml',
                file_get_contents(__DIR__ . '/ExpectedDoctrineTranslation.orm.xml'))
        ;
    }
}
