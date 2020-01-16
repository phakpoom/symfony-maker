<?php

declare(strict_types=1);

namespace Test\Generator\Model\CaseOne;

use Sylius\Component\Resource\Model\ResourceInterface;

interface DummyInterface extends ResourceInterface
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
