<?php

declare(strict_types=1);

namespace App\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface MockInterface extends ResourceInterface, TranslatableInterface
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

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param string|null $description
     *
     * @return void
     */
    public function setDescription(?string $description): void;
}
