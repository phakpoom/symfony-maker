<?php

declare(strict_types=1);

namespace Test\Generator\Model;

use Bonn\Maker\Generator\ModelGenerator;
use Bonn\Maker\ModelPropType\CollectionType;
use Bonn\Maker\ModelPropType\IntegerType;
use Bonn\Maker\ModelPropType\StringType;
use Bonn\Maker\Tests\AbstractMakerTestCase;
use Sylius\Component\Resource\Model\ResourceInterface;

class ModelGeneratorTest extends AbstractMakerTestCase
{
    /** @var ModelGenerator */
    private $generator;
    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new ModelGenerator();
        $this->generator->setManager($this->manager);
    }

    public function testGenerateBasic()
    {
        $this->generator->generate([
            'class' => 'Test\\Generator\\Model\\CaseOne\\Dummy',
            'props' => [
                new StringType('displayName'),
            ],
            'model_dir' => __DIR__ . '/CaseOne',
        ]);

        $this
            ->assertCountFilesWillBeCreated(2)
            ->assertFileWillBeCreated(__DIR__ . '/CaseOne/Dummy.php', file_get_contents(__DIR__ . '/CaseOne/Customer.php'))
            ->assertFileWillBeCreated(__DIR__ . '/CaseOne/DummyInterface.php', file_get_contents(__DIR__ . '/CaseOne/CustomerInterface.php'))
        ;
    }

    public function testGenerateExistsClass()
    {
        $this->generator->generate([
            'class' => 'Test\\Generator\\Model\\CaseExists\\Dummy',
            'props' => [
                new IntegerType('age'),
                new CollectionType('groups', ResourceInterface::class),
            ],
            'model_dir' => __DIR__ . '/CaseExists',
        ]);

        $this
            ->assertCountFilesWillBeCreated(2)
            ->assertFileWillBeCreated(__DIR__ . '/CaseExists/Dummy.php', file_get_contents(__DIR__ . '/CaseExists/ExpectedDummy.php'))
            ->assertFileWillBeCreated(__DIR__ . '/CaseExists/DummyInterface.php', file_get_contents(__DIR__ . '/CaseExists/ExpectedDummyInterface.php'))
        ;
    }
}
