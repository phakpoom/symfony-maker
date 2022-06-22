<?php

declare(strict_types=1);

namespace Bonn\Maker\Converter;

use Bonn\Maker\ModelPropType\ArrayType;
use Bonn\Maker\ModelPropType\BooleanType;
use Bonn\Maker\ModelPropType\CollectionManyToManyInverseType;
use Bonn\Maker\ModelPropType\CollectionManyToManyOwnerBidirectionalType;
use Bonn\Maker\ModelPropType\CollectionManyToManyOwnerUnidirectionalType;
use Bonn\Maker\ModelPropType\CollectionType;
use Bonn\Maker\ModelPropType\DateTimeType;
use Bonn\Maker\ModelPropType\FloatType;
use Bonn\Maker\ModelPropType\IntegerType;
use Bonn\Maker\ModelPropType\InterfaceType;
use Bonn\Maker\ModelPropType\PropTypeInterface;
use Bonn\Maker\ModelPropType\StringType;
use Bonn\Maker\ModelPropType\TranslationType;
use JetBrains\PhpStorm\Pure;

final class PropTypeConverter implements PropTypeConverterInterface
{
    public const VALUE_SEPARATOR = ':';

    public const PROP_SEPARATOR = '|';

    public const TYPES = [
        StringType::class,
        IntegerType::class,
        BooleanType::class,
        ArrayType::class,
        DateTimeType::class,
        FloatType::class,
        TranslationType::class,
        InterfaceType::class,
        CollectionType::class,
        CollectionManyToManyOwnerUnidirectionalType::class,
        CollectionManyToManyOwnerBidirectionalType::class,
        CollectionManyToManyInverseType::class,
    ];

    /** @var PropTypeInterface[]|array */
    private array $types = self::TYPES;

    #[Pure] public function __construct(?array $types = null)
    {
        if (null !== $types) {
            $this->types = array_unique(array_merge($this->types, $types));
        }
    }

    public function getSupportedType(): array
    {
        return $this->types;
    }

    /**
     * @param string $infosString
     *
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

    public function buildInfoString(string $name, string $type, ?string $value = null): string
    {
        $str = $name . self::VALUE_SEPARATOR . $type;
        if ('' !== $value && null !== $value) {
            $str .= self::VALUE_SEPARATOR . $value;
        }

        return $str;
    }

    #[Pure] public function combineInfos(array $infos): string
    {
        return implode(self::PROP_SEPARATOR, $infos);
    }
}
