<?php

declare(strict_types=1);

namespace Bonn\Maker\Converter;

use Bonn\Maker\ModelPropType\PropTypeInterface;

interface PropTypeConverterInterface
{
    public function getSupportedType(): array;

    /**
     * @return array|PropTypeInterface[]
     */
    public function convertMultiple(string $infosString): array;

    /**
     * @throws \InvalidArgumentException
     */
    public function convert(string $infoString): PropTypeInterface;

    public function buildInfoString(string $name, string $type, ?string $value = null): string;

    public function combineInfos(array $infos): string;
}
