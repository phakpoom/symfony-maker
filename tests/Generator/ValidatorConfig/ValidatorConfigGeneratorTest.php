<?php

declare(strict_types=1);

namespace Test\Generator\Twig;

use Bonn\Maker\Generator\ValidatorConfigGenerator;
use Bonn\Maker\Tests\AbstractMakerTestCase;

class ValidatorConfigGeneratorTest extends AbstractMakerTestCase
{
    /** @var ValidatorConfigGenerator */
    private $generator;
    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new ValidatorConfigGenerator();
        $this->generator->setManager($this->manager);
    }

    public function testGenerateBasic()
    {
        $this->generator->generate([
            'className' => __CLASS__,
            'validator_config_dir' => __DIR__,
        ]);

        $this
            ->assertCountFilesWillBeCreated(1)
            ->assertFileWillBeCreated(__DIR__ . '/ValidatorConfigGeneratorTest.xml', file_get_contents(__DIR__ . '/expectedConfig.xml'))
        ;
    }
}
