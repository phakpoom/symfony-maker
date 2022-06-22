<?php

declare(strict_types=1);

namespace Test\Generator\Model\CaseExists;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface DummyInterface extends ResourceInterface, TimestampableInterface
{
    public function getDisplayName(): ?string;

    public function setDisplayName(?string $displayName): void;
}
