<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Service;

interface TranslationResolverInterface
{
    public function resolve(array $data): array;
}
