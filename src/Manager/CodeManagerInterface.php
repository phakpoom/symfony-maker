<?php

declare(strict_types=1);

namespace Bonn\Maker\Manager;

use Bonn\Maker\Model\Code;

interface CodeManagerInterface
{
    /**
     * @return array|Code[]
     */
    public function getCodes(): array;

    public function persist(Code $code): void;

    public function detach(Code $code): void;

    public function flush(): void;

    public function clear(): void;
}
