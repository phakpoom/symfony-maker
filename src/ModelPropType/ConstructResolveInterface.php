<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Nette\PhpGenerator\Method;

interface ConstructResolveInterface
{
    public function resolveConstruct(Method $construct): void;
}
