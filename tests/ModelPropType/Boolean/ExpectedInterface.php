<?php

declare(strict_types=1);

namespace App\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface MockInterface extends ResourceInterface
{
    /**
     * @return bool
     */
    public function isActive(): bool;

    /**
     * @param bool $active
     *
     * @return void
     */
    public function setActive(bool $active): void;
}
