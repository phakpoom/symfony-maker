<?php

declare(strict_types=1);

namespace App\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface MockInterface extends ResourceInterface
{
    /**
     * @return array
     */
    public function getConfigs(): array;

    /**
     * @param array $configs
     *
     * @return void
     */
    public function setConfigs(array $configs): void;
}
