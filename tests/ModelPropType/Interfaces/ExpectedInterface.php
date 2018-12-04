<?php

declare(strict_types=1);

namespace App\Model;

use MyApp\Model\CategoryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface MockInterface extends ResourceInterface
{
    /**
     * @return CategoryInterface|null
     */
    public function getCategory(): ?CategoryInterface;

    /**
     * @param CategoryInterface|null $category
     *
     * @return void
     */
    public function setCategory(?CategoryInterface $category): void;
}
