<?php

declare(strict_types=1);

namespace App\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface MockInterface extends ResourceInterface
{
    public function getPrice(): ?int;

    public function setPrice(?int $price): void;
}
