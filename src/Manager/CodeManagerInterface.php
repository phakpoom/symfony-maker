<?php

namespace Bonn\Maker\Manager;

use Bonn\Maker\Model\Code;

interface CodeManagerInterface
{
    /**
     * @return array|Code[]
     */
    public function getCodes(): array;

    /**
     * @param Code $code
     *
     * @return void
     */
    public function persist(Code $code): void;

    /**
     * @param Code $code
     *
     * @return void
     */
    public function detach(Code $code): void;

    /**
     * @return void
     */
    public function flush(): void;

    /**
     * @return void
     */
    public function clear(): void;
}
