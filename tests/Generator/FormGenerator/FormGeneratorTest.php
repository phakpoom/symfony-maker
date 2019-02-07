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
            'config_dir' => $configDir = __DIR__ . '/config',
            'form_service_file_path' => '/services/forms.xml',
            'all_service_file_path' => '/services.xml',
        ]);

        $this
            ->assertCountFilesWillBeCreated(3)
            ->assertFileWillBeCreated(__DIR__ . '/EntityType.php', file_get_contents(__DIR__ . '/ExpectedCommonForm.php'))
            ->assertFileWillBeCreated($configDir . '/services/forms.xml', file_get_contents(__DIR__ . '/expectedCreatedForms.xml'))
            ->assertFileWillBeCreated($configDir . '/services.xml',
 <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<container xmlns="http://symfony.com/schema/dic/services" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
    <imports>
        <import resource="services/ok.xml"/>
        <import resource="services/forms.xml"/>
    </imports>
    <services/>
</container>

XML
                )
        ;
    }

    public function testGenerateWithServiceNotImported()
    {
        $this->generator->generate([
            'class' => Entity::class,
            'namespace' => __NAMESPACE__,
            'form_dir' => __DIR__,
            'config_dir' => $configDir = __DIR__ . '/config',
            'form_service_file_path' => '/services/forms.xml',
            'all_service_file_path' => '/servicesHasImport.xml',
        ]);

        $this
            ->assertCountFilesWillBeCreated(2)
            ->assertFileWillBeCreated(__DIR__ . '/EntityType.php', file_get_contents(__DIR__ . '/ExpectedCommonForm.php'))
            ->assertFileWillBeCreated($configDir . '/services/forms.xml', file_get_contents(__DIR__ . '/expectedCreatedForms.xml'));
    }
}

class Entity
{
    protected $id;

    protected $name;

    protected $phoneNumber;
}