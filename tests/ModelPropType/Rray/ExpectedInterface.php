<?php

declare(strict_types=1);

namespace App\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface MockInterface extends ResourceInterface
{
    public function getConfigs(): array;

    public function setConfigs(array $configs): void;
}
