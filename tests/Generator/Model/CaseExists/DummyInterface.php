<?php

declare(strict_types=1);

namespace Test\Generator\Model\CaseExists;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface DummyInterface extends ResourceInterface, TimestampableInterface
{
    /**
     * @return string|null
     */
    public function getDisplayName(): ?string;

    /**
     * @param string|null $displayName
     *
     * @return void
     */
    public function setDisplayName(?string $displayName): void;
}
