<?php

declare(strict_types=1);

namespace App\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface MockInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name): void;
}
