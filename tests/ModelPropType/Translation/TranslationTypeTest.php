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

        $allCodes = $this->manager->getCodes();

        $this->assertCount(6, $allCodes);
        $this->assertEquals(file_get_contents(__DIR__ . '/1ExpectedModel.php'),
            $allCodes[$this->codeDir() . '/Mock.php']->getContent());
        $this->assertEquals(file_get_contents(__DIR__ . '/1ExpectedInterface.php'),
            $allCodes[$this->codeDir() . '/MockInterface.php']->getContent());

        $this->assertEquals(file_get_contents(__DIR__ . '/1ExpectedModelTranslation.php'),
            $allCodes[$this->codeDir() . '/MockTranslation.php']->getContent());

        $this->assertEquals(file_get_contents(__DIR__ . '/1ExpectedInterfaceTranslation.php'),
            $allCodes[$this->codeDir() . '/MockTranslationInterface.php']->getContent());

        $this->assertEquals(file_get_contents(__DIR__ . '/1ExpectedDoctrine.orm.xml'),
            $allCodes[$this->codeDir() . '/Mock.orm.xml']->getContent());

        $this->assertEquals(file_get_contents(__DIR__ . '/1ExpectedDoctrineTranslation.orm.xml'),
            $allCodes[$this->codeDir() . '/MockTranslation.orm.xml']->getContent());
    }

    public function testMultipleFieldGenerated()
    {
        $this->generate([
            new TranslationType('name'),
            new TranslationType('description'),
        ]);

        $allCodes = $this->manager->getCodes();

        $this->assertCount(6, $allCodes);
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedModel.php'),
            $allCodes[$this->codeDir() . '/Mock.php']->getContent());
        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedInterface.php'),
            $allCodes[$this->codeDir() . '/MockInterface.php']->getContent());

        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedModelTranslation.php'),
            $allCodes[$this->codeDir() . '/MockTranslation.php']->getContent());

        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedInterfaceTranslation.php'),
            $allCodes[$this->codeDir() . '/MockTranslationInterface.php']->getContent());

        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedDoctrine.orm.xml'),
            $allCodes[$this->codeDir() . '/Mock.orm.xml']->getContent());

        $this->assertEquals(file_get_contents(__DIR__ . '/ExpectedDoctrineTranslation.orm.xml'),
            $allCodes[$this->codeDir() . '/MockTranslation.orm.xml']->getContent());
    }
}
