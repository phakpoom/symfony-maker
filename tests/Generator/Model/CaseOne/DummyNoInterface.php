<?php

declare(strict_types=1);

namespace Test\Generator\Model\CaseOne;

use Sylius\Component\Resource\Model\ResourceInterface;

interface DummyInterface extends ResourceInterface
{
    public function getDisplayName(): ?string;

    public function setDisplayName(?string $displayName): void;
}
