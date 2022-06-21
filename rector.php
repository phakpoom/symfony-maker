<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src/ModelPropType/BooleanType.php'
    ]);

    $rectorConfig->rules([
        \Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector::class,
        \Rector\TypeDeclaration\Rector\FunctionLike\ReturnTypeDeclarationRector::class,
    ]);

    $rectorConfig->phpVersion(\Rector\Core\ValueObject\PhpVersion::PHP_81);
};
