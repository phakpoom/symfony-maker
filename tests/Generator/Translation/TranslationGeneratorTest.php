<?php

declare(strict_types=1);

namespace Test\Generator\Twig;

use Bonn\Maker\Generator\TranslationGenerator;
use Bonn\Maker\Tests\AbstractMakerTestCase;

class TranslationGeneratorTest extends AbstractMakerTestCase
{
    /** @var TranslationGenerator */
    private $generator;
    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new TranslationGenerator();
        $this->generator->setManager($this->manager);
    }

    public function testGenerateExistsKey()
    {
        $this->generator->generate([
            'full_class_name' => DummyOk::class,
            'translation_dir' => __DIR__ . '/translations',
        ]);

        $this
            ->assertCountFilesWillBeCreated(2)
            ->assertFileWillBeCreated(__DIR__ . '/translations/messages.en.yaml', file_get_contents(__DIR__ . '/expected/messages.en_1.yaml'))
            ->assertFileWillBeCreated(__DIR__ . '/translations/messages.th.yaml', file_get_contents(__DIR__ . '/expected/messages.th_1.yaml'))
        ;
    }

    public function testGenerateNonExistsKey()
    {
        $this->generator->generate([
            'full_class_name' => DummyNo::class,
            'translation_dir' => __DIR__ . '/translations',
        ]);

        $this
            ->assertCountFilesWillBeCreated(2)
            ->assertFileWillBeCreated(__DIR__ . '/translations/messages.en.yaml', file_get_contents(__DIR__ . '/expected/messages.en_2.yaml'))
            ->assertFileWillBeCreated(__DIR__ . '/translations/messages.th.yaml', file_get_contents(__DIR__ . '/expected/messages.th_2.yaml'))
        ;
    }
}

class DummyOk
{
    protected $name;

    protected $enabled;

    protected $links;
}

class DummyNo
{
    protected $code;
}
