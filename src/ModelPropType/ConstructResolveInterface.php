<?php

namespace Bonn\Maker\ModelPropType;

use Nette\PhpGenerator\Method;

interface ConstructResolveInterface
{
    /**
     * @param Method $construct
     */
    public function resolveConstruct(Method $construct): void ;
}
