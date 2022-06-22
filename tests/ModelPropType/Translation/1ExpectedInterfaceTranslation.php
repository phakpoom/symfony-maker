<?php

declare(strict_types=1);

namespace App\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface MockTranslationInterface extends TranslationInterface, ResourceInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;
}
