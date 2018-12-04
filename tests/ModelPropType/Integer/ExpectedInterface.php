<?php

declare(strict_types=1);

namespace App\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface MockInterface extends ResourceInterface
{
    /**
     * @return int|null
     */
    public function getPrice(): ?int;

    /**
     * @param int|null $price
     *
     * @return void
     */
    public function setPrice(?int $price): void;
}
