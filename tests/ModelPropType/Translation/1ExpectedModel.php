<?php

declare(strict_types=1);

namespace App\Model;

use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

/**
 * @method MockTranslationInterface getTranslation()
 */
class Mock implements MockInterface
{
    use TranslatableTrait {
        __construct as protected initializeTranslationsCollection;
    }

    protected ?int $id;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->getTranslation()->getName();
    }

    public function setName(?string $name): void
    {
        $this->getTranslation()->setName($name);
    }

    protected function createTranslation(): TranslationInterface|MockTranslation
    {
        return new MockTranslation();
    }
}
