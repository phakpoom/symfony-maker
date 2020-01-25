<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Service;

interface TwigTemplateResolverInterface
{
    public function resolve(array $data): array;
}
