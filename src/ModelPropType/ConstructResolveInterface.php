<?php

namespace Bonn\Maker\ModelPropType;

use Nette\PhpGenerator\Method;

interface ConstructResolveInterface
{
    /**
     * @param Method $construct
     */
    public static function resolveConstruct(Method $construct): void ;
}
