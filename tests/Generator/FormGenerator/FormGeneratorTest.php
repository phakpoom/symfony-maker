<?php

declare(strict_types=1);

namespace Test\Generator\FormGenerator;

use Bonn\Maker\Generator\FormGenerator;
use Bonn\Maker\Tests\AbstractMakerTestCase;

class FormGeneratorTest extends AbstractMakerTestCase
{
    /** @var FormGenerator */
    private $generator;
    protected function setUp()
    {
        parent::setUp();

        $this->generator = new FormGenerator($this->manager);
    }

    public function testGenerateBasic()
    {
        $this->generator->generate([
            'class' => Entity::class,
            'namespace' => __NAMESPACE__,
            'form_dir' => __DIR__,
        ]);

        $this
            ->assertCountFilesWillBeCreated(1)
            ->assertFileWillBeCreated(__DIR__ . '/EntityType.php', file_get_contents(__DIR__ . '/ExpectedCommonForm.php'))
        ;
    }

    public function testGenerateWithServiceImported()
    {
        $this->generator->generate([
            'class' => Entity::class,
            'namespace' => __NAMESPACE__,
            'form_dir' => __DIR__,
            'form_service_file_path' => $formServicePath = __DIR__ . '/services/forms.xml',
            'all_service_file_path' => $allServicePath = __DIR__ . '/config/services.xml',
        ]);

        $this
            ->assertCountFilesWillBeCreated(3)
            ->assertFileWillBeCreated(__DIR__ . '/EntityType.php', file_get_contents(__DIR__ . '/ExpectedCommonForm.php'))
            ->assertFileWillBeCreated($formServicePath, file_get_contents(__DIR__ . '/expectedCreatedForms.xml'))
            ->assertFileWillBeCreated($allServicePath, file_get_contents(__DIR__ . '/expectedimportedServices.xml'))
        ;
    }
}

class Entity
{
    protected $id;

    protected $name;

    protected $phoneNumber;
}