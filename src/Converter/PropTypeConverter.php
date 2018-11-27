<?php

namespace Bonn\Maker\Converter;

use Bonn\Maker\ModelPropType\PropTypeInterface;
use Bonn\Maker\ModelPropType\StringType;
use Bonn\Maker\ModelPropType\IntegerType;

final class PropTypeConverter implements PropTypeConverterInterface
{
    const VALUE_SEPARATOR = ':';
    const PROP_SEPARATOR = '|';

    const TYPES = [
        StringType::class,
        IntegerType::class,
    ];

    /**
     * @var PropTypeInterface[]|array
     */
    private $types = self::TYPES;

    public function __construct(?array $types = null)
    {
        if (null !== $types) {
            $this->types = array_unique(array_merge($this->types, $types));
        }
    }

    /**
     * @return array
     */
    public function getSupportedType(): array
    {
        return $this->types;
    }

    /**
     * @param string $infosString
     * @return array|PropTypeInterface[]
     */
    public function convertMultiple(string $infosString): array
    {
        if (empty($infosString)) {
            return [];
        }

        return array_map(function ($info) {
            return $this->convert($info);
        }, explode(self::PROP_SEPARATOR, $infosString));
    }

    /**
     * @param string $infoString
     *
     * @return PropTypeInterface
     * @throws \InvalidArgumentException
     */
    public function convert(string $infoString): PropTypeInterface
    {
        $p = explode(self::VALUE_SEPARATOR, $infoString);
        /** @var PropTypeInterface $typeClass */
        foreach ($this->getSupportedType() as $typeClass) {
            if ($typeClass::getTypeName() === $p[1]) {
                return new $typeClass($p[0], $p[2] ?? null);
            }
        }

        throw new \InvalidArgumentException('Unsupported propType "' . $p[1] . '"');
    }

    /**
     * @param string $name
     * @param string $type
     * @param null|string $value
     *
     * @return string
     */
    public function buildInfoString(string $name, string $type, ?string $value = null): string
    {
        $str = $name . self::VALUE_SEPARATOR . $type;
        if (!empty($value)) {
            $str .= self::VALUE_SEPARATOR . $value;
        }

        return $str;
    }

    /**
     * @param array $infos
     *
     * @return string
     */
    public function combineInfos(array $infos): string
    {
        return implode(self::PROP_SEPARATOR, $infos);
    }
}
