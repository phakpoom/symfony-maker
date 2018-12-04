<?php

declare(strict_types=1);

namespace App\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface MockInterface extends ResourceInterface
{
    /**
     * @return float|null
     */
    public function getPrice(): ?float;

    /**
     * @param float|null $price
     *
     * @return void
     */
    public function setPrice(?float $price): void;
}
