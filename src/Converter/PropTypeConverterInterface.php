<?php

namespace Bonn\Maker\Converter;

use Bonn\Maker\ModelPropType\PropTypeInterface;

interface PropTypeConverterInterface
{
    /**
     * @return array
     */
    public function getSupportedType(): array;

    /**
     * @param string $infosString
     * @return array|PropTypeInterface[]
     */
    public function convertMultiple(string $infosString): array;

    /**
     * @param string $infoString
     *
     * @return PropTypeInterface
     * @throws \InvalidArgumentException
     */
    public function convert(string $infoString): PropTypeInterface;

    /**
     * @param string $name
     * @param string $type
     * @param null|string $value
     *
     * @return string
     */
    public function buildInfoString(string $name, string $type, ?string $value = null): string;

    /**
     * @param array $infos
     *
     * @return string
     */
    public function combineInfos(array $infos): string;
}
