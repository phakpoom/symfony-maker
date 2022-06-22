<?php

declare(strict_types=1);

namespace App\Model;

use MyApp\Model\CategoryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface MockInterface extends ResourceInterface
{
    public function getCategory(): ?CategoryInterface;

    public function setCategory(?CategoryInterface $category): void;
}
