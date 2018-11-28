<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Nette\PhpGenerator\PhpNamespace;

interface NamespaceModifyableInterface
{
    public function modify(PhpNamespace $classNameSpace, PhpNamespace $interfaceNameSpace): void;
}
