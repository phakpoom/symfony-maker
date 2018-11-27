<?php

namespace Bonn\Maker\ModelPropType;

use Nette\PhpGenerator\PhpNamespace;

interface NamespaceModifyableInterface
{
    /**
     * @param PhpNamespace $classNameSpace
     * @param PhpNamespace $interfaceNameSpace
     */
    public function modify(PhpNamespace $classNameSpace, PhpNamespace $interfaceNameSpace): void ;
}
