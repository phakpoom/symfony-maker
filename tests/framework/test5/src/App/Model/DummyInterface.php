<?php

declare(strict_types=1);

namespace App\App\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface DummyInterface extends ResourceInterface
{
    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $name
     *
     * @return void
     */
    public function setName(?string $name): void;
}
