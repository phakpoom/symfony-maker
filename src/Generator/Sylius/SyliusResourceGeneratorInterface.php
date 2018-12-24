<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator\Sylius;

use Bonn\Maker\Generator\GeneratorInterface;

interface SyliusResourceGeneratorInterface extends GeneratorInterface
{
    public function resolveConfigFileName(array $options = []): string;
}
